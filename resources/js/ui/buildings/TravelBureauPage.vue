<template>
  <div class="flex flex-col gap-4">
    <h1 class="page_title">{{ $t('Travel Bureau') }}</h1>
    <div v-if="currentCart" class="mb-2">
      <p class="mb-0">{{ $t('Your current cart') }}</p>
      <BaseItem :item="currentCart" :show-amount="false" disable-tooltip />
    </div>
    <BaseLoadingIcon v-if="isLoading" class="h-6 w-6 mx-auto" />
    <StoreContainer
      v-else
      :store-items="storeItems"
      button-text="Buy"
      :show-amount-input="false"
      :show-item-requirements="true"
      :show-item-information="true"
      @trade="handleTrade"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import type { StoreItemResource } from '@/types/StoreItemResource';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { travelbureauDataLoader } from '@/buildingScripts/buildingLoaders';
import { AdvApi } from '@/AdvApi';
import type { advAPIResponse } from '@/types/Responses/AdvResponse';
import StoreContainer from '@/ui/components/store/StoreContainer.vue';
import BaseItem from '@/ui/components/base/BaseItem.vue';
import BaseLoadingIcon from '@/ui/components/base/BaseLoadingIcon.vue';

interface BuyCartResponse extends advAPIResponse {
  data: {
    new_cart: string;
  };
}

const isLoading = ref(false);
const storeItems = ref<StoreItemResource[]>([]);
const currentCart = ref<string | null>(null);

const fetchData = async () => {
  const cached = buildingDataPreloader.getBuildingCache('travelbureau');
  if (cached) {
    storeItems.value = cached.store_items;
    return;
  }

  try {
    isLoading.value = true;
    const response = await travelbureauDataLoader.store_items();
    storeItems.value = response.data.store_items;
  } finally {
    isLoading.value = false;
  }
};

void fetchData();

const handleTrade = async ({ item }: { item: StoreItemResource; amount: number }) => {
  const response = await AdvApi.post<BuyCartResponse>('/travelbureau/buy', { item: item.name });
  currentCart.value = response.data.new_cart;
};
</script>
