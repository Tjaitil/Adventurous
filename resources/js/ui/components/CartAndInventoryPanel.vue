<template>
  <div class="w-[29%]">
    <UTabs
      v-model="selectedTab"
      :items="items"
      :ui="{
        list: 'pixelated-corners-sm',
      }"
    >
      <template #inventory-container>
        <InventoryContainer />
      </template>
      <template #user-cart>
        <UserCart />
      </template>
    </UTabs>
  </div>
</template>

<script setup lang="ts">
import UserCart from '@/ui/components/UserCart.vue';
import InventoryContainer from '@/ui/components/Inventory/InventoryContainer.vue';
import type { TabsItem } from '@nuxt/ui';
import { ref, watch } from 'vue';
import { useCartAndInventoryTab } from '../composables/useCartAndInventoryTab';

const items = [
  {
    label: 'Inventory',
    slot: 'inventory-container' as const,
    value: 'inventory',
  },
  {
    label: 'Cart',
    slot: 'user-cart' as const,
    value: 'cart',
  },
] satisfies TabsItem[];

const selectedTab = ref<'inventory' | 'cart'>('inventory');

const { activeTab } = useCartAndInventoryTab();
watch(
  () => activeTab.value,
  newTab => {
    selectedTab.value = newTab;
  },
);
</script>
