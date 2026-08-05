<template>
  <div class="flex flex-col gap-4">
    <h1 class="page_title">{{ $t('Stockpile') }}</h1>
    <p class="mb-2">
      {{ $t('Click items in inventory or stockpile to insert/withdraw') }}
    </p>
    <UCard variant="soft">
      <p class="mb-3 text-sm text-white">
        {{ $t('Item slots') }}: {{ stockpileItems.length }} /
        {{ maxSlots ?? 0 }}
      </p>

      <div v-if="isLoading" class="py-4 text-sm">{{ $t('Loading...') }}</div>

      <div
        v-else
        id="stockpile"
        ref="stockpileContainerRef"
        class="grid grid-cols-[repeat(auto-fill,minmax(88px,1fr))] gap-3"
      >
        <BaseItem
          v-for="item in stockpileItems"
          ref="stockpileItemRefs"
          :key="item.item"
          :item="item.item"
          :amount="item.amount"
          class="stockpile_item"
          :disable-tooltip="stockpileStore.menu.isOpen"
          @click.stop="event => handleStockpileItemClick(event, item.item)"
        />
      </div>
    </UCard>
    <Teleport to="body">
      <StockpileItemActionMenu ref="actionMenuEl" @select="handleAction" />
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import type {
  StockpileDataResponse,
  StockpileItemResource,
} from '@/types/Stockpile';
import { onMounted, onUnmounted, ref, useTemplateRef } from 'vue';
import { useInventoryStore } from '../stores/InventoryStore';
import { useItemActionMenuStore } from '../stores/ItemActionMenuStore.ts';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import StockpileItemActionMenu from '../components/stockpile/StockpileItemActionMenu.vue';
import BaseItem from '../components/base/BaseItem.vue';
import { CustomFetchApi } from '@/CustomFetchApi';
import { stockpileDataLoader } from '@/buildingScripts/buildingLoaders';

const stockpileStore = useItemActionMenuStore();
const inventoryStore = useInventoryStore();

const isLoading = ref(false);
const stockpileItems = ref<StockpileItemResource[]>([]);
const maxSlots = ref<number>(0);

const stockpileItemEls =
  useTemplateRef<InstanceType<typeof BaseItem>[]>('stockpileItemRefs');

const setData = (res: StockpileDataResponse): void => {
  stockpileItems.value = res.data.items;
  maxSlots.value = res.meta.max_slots;
};

const fetchStockpile = async (): Promise<void> => {
  const cachedData = buildingDataPreloader.getStockpileData();
  if (cachedData) {
    setData(cachedData);
    return;
  }

  try {
    isLoading.value = true;
    const response = await stockpileDataLoader.res();

    setData(response);
  } finally {
    isLoading.value = false;
  }
};

const handleStockpileItemClick = (event: Event, item: string): void => {
  const target = event.currentTarget;
  if (!(target instanceof HTMLElement)) {
    return;
  }
  const parent = target.closest('.item');

  if (!(parent instanceof HTMLElement)) {
    throw new Error('Expected to find parent with class "item"');
  }

  stockpileStore.openMenu({
    item,
    insert: false,
    target: parent,
  });
};

const handleAction = async (action: number | 'all') => {
  if (!stockpileStore.menu.item) {
    return;
  }

  let actionAmount: number | undefined;
  if (stockpileStore.menu.insert && action === 'all') {
    actionAmount = inventoryStore.inventoryItems.find(
      i => i.item === stockpileStore.menu.item,
    )?.amount;
  } else if (!stockpileStore.menu.insert && action === 'all') {
    actionAmount = stockpileItems.value.find(
      i => i.item === stockpileStore.menu.item,
    )?.amount;
  } else if (typeof action === 'number') {
    actionAmount = action;
  }

  try {
    const response = await CustomFetchApi.post<
      StockpileDataResponse,
      { item: string; insert: boolean; all?: true; amount?: number }
    >('/stockpile/update', {
      item: stockpileStore.menu.item,
      insert: stockpileStore.menu.insert,
      amount: actionAmount,
    });

    setData(response.data);
    stockpileStore.closeMenu();
    restoreFocus();
    inventoryStore.setShouldUpdateInventory(true);
  } catch {
    return;
  }
};

const restoreFocus = () => {
  if (!(stockpileStore.anchorTarget?.nextSibling instanceof HTMLElement)) {
    const previousSibling = stockpileStore.anchorTarget?.previousSibling;
    if (previousSibling instanceof HTMLElement) {
      previousSibling.focus();
    }
    return;
  }

  stockpileStore.anchorTarget.focus();
};

onMounted(() => {
  stockpileStore.addMenuEvent();
  window.addEventListener('scroll', handleScroll);
  fetchStockpile()
    .then(() => {
      if (
        stockpileItemEls.value !== null &&
        stockpileItemEls.value.length > 0
      ) {
        stockpileItemEls.value[0].$el.focus();
      }
    })
    .catch(() => {
      return;
    });
});

onUnmounted(() => {
  stockpileStore.removeMenuEvent();
  window.removeEventListener('scroll', handleScroll);
});

const handleScroll = () => {
  if (stockpileStore.menu.isOpen) {
    stockpileStore.closeMenu();
  }
};
</script>
