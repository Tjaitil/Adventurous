<template>
  <UCard
    id="inventory"
    variant="soft"
    class="relative z-20 float-right mt-0 h-[600px] w-full text-white"
  >
    <BaseLoading :is-loading>
      <p class="mb-4">
        Inventory
        <span
          id="inventory-status"
          :class="{
            'not-able-color': inventoryStore.isInventoryFull,
          }"
        >
          {{ inventoryStore.inventoryItems.length }} / {{ 18 }}
        </span>
      </p>
      <InventoryItemWrapper
        v-for="item in inventoryStore.inventoryItems"
        :key="item.id"
        :item="item"
      />
    </BaseLoading>
  </UCard>
</template>

<script lang="ts" setup>
import { CustomFetchApi } from '@/CustomFetchApi';
import { InventoryItem } from '@/types/InventoryItem';
import { ref } from 'vue';
import InventoryItemWrapper from './InventoryItemWrapper.vue';
import BaseLoading from '@/ui/components/base/BaseLoading.vue';
import { useInventoryStore } from '@/ui/stores/InventoryStore';
import { itemPrices } from '@/clientScripts/inventory';
import { reportCatchError } from '@/base/ErrorHandler';

const inventoryStore = useInventoryStore();

const isLoading = ref(true);

const getInventory = async () => {
  try {
    isLoading.value = true;
    const response =
      await CustomFetchApi.get<InventoryItem[]>('/inventory/items');
    inventoryStore.inventoryItems = response.data;

    isLoading.value = false;
    inventoryStore.setShouldUpdateInventory(false);
    itemPrices.get();
  } catch (e) {
    reportCatchError(e);
  }
};
getInventory();

inventoryStore.$subscribe((mutation, state) => {
  if (state.shouldUpdateInventory && !isLoading.value) {
    getInventory();
  }
});
</script>
