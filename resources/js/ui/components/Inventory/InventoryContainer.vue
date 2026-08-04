<template>
  <UCard
    id="inventory"
    variant="soft"
    class="relative z-20 float-right mt-0 h-[600px] w-full text-white"
  >
    <BaseLoading v-if="isLoading" :is-loading> </BaseLoading>
    <template v-else>
      <p class="mb-4">
        {{ $t('Inventory') }}
        <span
          id="inventory-status"
          :class="{
            'not-able-color': inventoryStore.isInventoryFull,
          }"
        >
          {{ inventoryStore.inventoryItems.length }} / {{ 18 }}
        </span>
      </p>
      <div
        class="grid-tem grid grid-cols-[repeat(auto-fill,minmax(88px,1fr))] grid-rows-[minmax(90px,1fr)] gap-y-4"
      >
        <BaseItem
          v-for="item in inventoryStore.inventoryItems"
          ref="inventoryItemRefs"
          :key="item.id"
          :item="item.item"
          :amount="item.amount"
          :show-amount="true"
          class="inventory_item"
          :disable-tooltip="!inventoryStore.showTooltips"
          @click="handleEvent($event, item.item)"
        />
      </div>
    </template>
  </UCard>
</template>

<script lang="ts" setup>
import { CustomFetchApi } from '@/CustomFetchApi';
import type { InventoryItem } from '@/types/InventoryItem';
import { ref } from 'vue';
import BaseLoading from '@/ui/components/base/BaseLoading.vue';
import { useInventoryStore } from '@/ui/stores/InventoryStore';
import { itemPrices } from '@/clientScripts/inventory';
import { reportCatchError } from '@/base/ErrorHandler';
import BaseItem from '../base/BaseItem.vue';

const inventoryStore = useInventoryStore();
const isLoading = ref(true);

const getInventory = async () => {
  try {
    isLoading.value = true;
    const response =
      await CustomFetchApi.get<InventoryItem[]>('/inventory/items');
    inventoryStore.inventoryItems = response.data;
    inventoryStore.setShouldUpdateInventory(false);
    itemPrices.get();
  } catch (e) {
    reportCatchError(e);
  } finally {
    isLoading.value = false;
  }
};
void getInventory();

inventoryStore.$subscribe((mutation, state) => {
  if (state.shouldUpdateInventory && !isLoading.value) {
    void getInventory();
  }
});

const handleEvent = (e: Event, itemName: string): void => {
  inventoryStore.handleItemClick(e, itemName);
};
</script>

<style>
.inventory_item {
  display: inline-block;
  max-width: 104px;
  min-width: 60px;
  width: 30%;
  height: 90px;
  position: relative;
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  -khtml-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

.inventory_item figure {
  position: relative;
}
</style>
