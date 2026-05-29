<template>
  <div class="flex flex-col gap-4">
    <h1 class="page_title">{{ $t('Archery Shop') }}</h1>
    <p class="text-sm">
      {{ $t('Craft bows, unfinished arrows or arrow shafts from logs. Some bows will require a certain total level of warriors.') }}
    </p>
    <div v-if="isDiscountActive" class="proficiency-notice text-sm text-green-400">
      {{ $t('Miner discount of {pct} % is active', { pct: discountPercentage }) }}
    </div>
    <BaseLoadingIcon v-if="isLoading" class="h-6 w-6 mx-auto" />
    <StoreContainer
      v-else
      :store-items="storeItems"
      button-text="Fletch"
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
import { archeryShopDataLoader } from '@/buildingScripts/buildingLoaders';
import { AdvApi } from '@/AdvApi';
import StoreContainer from '@/ui/components/store/StoreContainer.vue';
import BaseLoadingIcon from '@/ui/components/base/BaseLoadingIcon.vue';

const isLoading = ref(false);
const storeItems = ref<StoreItemResource[]>([]);
const isDiscountActive = ref(false);
const discountPercentage = ref(0);

const fetchData = async () => {
  const cached = buildingDataPreloader.getBuildingCache('archeryshop');
  if (cached) {
    storeItems.value = cached.store_items;
    isDiscountActive.value = cached.is_discount_active ?? false;
    discountPercentage.value = cached.store_value_modifier_as_percentage ?? 0;
    return;
  }

  try {
    isLoading.value = true;
    const response = await archeryShopDataLoader.store_items();
    storeItems.value = response.data.store_items;
    isDiscountActive.value = response.data.is_discount_active ?? false;
    discountPercentage.value = response.data.store_value_modifier_as_percentage ?? 0;
  } finally {
    isLoading.value = false;
  }
};

void fetchData();

const handleTrade = async ({ item, amount }: { item: StoreItemResource; amount: number }) => {
  await AdvApi.post('/archeryshop/fletch', { item: item.name, amount });
};
</script>
