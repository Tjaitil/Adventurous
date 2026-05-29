<template>
  <div class="flex flex-col gap-4">
    <h1 class="page_title">{{ $t('Zins Store') }}</h1>
    <p class="text-sm">
      {{ $t("Zins is a trader who trades daqloon loot. He is willing to trade daqloon horns and daqloon scales.") }}
    </p>
    <BaseLoadingIcon v-if="isLoading" class="h-6 w-6 mx-auto" />
    <StoreContainer
      v-else
      :store-items="storeItems"
      button-text="Sell"
      :show-item-requirements="false"
      :show-requirements="false"
      @trade="handleTrade"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import type { StoreItemResource } from '@/types/StoreItemResource';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { zinsStoreDataLoader } from '@/buildingScripts/buildingLoaders';
import { AdvApi } from '@/AdvApi';
import StoreContainer from '@/ui/components/store/StoreContainer.vue';
import BaseLoadingIcon from '@/ui/components/base/BaseLoadingIcon.vue';

const isLoading = ref(false);
const storeItems = ref<StoreItemResource[]>([]);

const fetchData = async () => {
  const cached = buildingDataPreloader.getBuildingCache('zinsstore');
  if (cached) {
    storeItems.value = cached.store_items;
    return;
  }

  try {
    isLoading.value = true;
    const response = await zinsStoreDataLoader.store_items();
    storeItems.value = response.data.store_items;
  } finally {
    isLoading.value = false;
  }
};

void fetchData();

const handleTrade = async ({ item, amount }: { item: StoreItemResource; amount: number }) => {
  await AdvApi.post('/zinsstore/sell', { item: item.name, amount });
};
</script>
