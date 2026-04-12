<template>
  <BaseItem
    class="inventory_item"
    :item="item.item"
    :amount="item.amount"
    @click="handleEvent($event, item.item)"
  />
</template>

<script setup lang="ts">
import type { InventoryItem } from '@/types/InventoryItem';
import { useInventoryStore } from '@/ui/stores/InventoryStore';
import stockpileModule from '@/buildingScripts/stockpile';
import BaseItem from '../base/BaseItem.vue';

interface Props {
  item: InventoryItem;
}

defineProps<Props>();

const inventoryStore = useInventoryStore();

const handleEvent = (e: Event, itemName: string) => {
  if (inventoryStore.inventoryItemEvent === null) {
    return;
  }

  switch (inventoryStore.inventoryItemEvent) {
    case 'stockpileMenu':
      stockpileModule.show_menu(e);
      break;
    case 'selectItem':
      inventoryStore.setSelectedItem(itemName);
      break;
  }
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
  /* iOS Safari */
  -webkit-user-select: none;
  /* Safari */
  -khtml-user-select: none;
  /* Konqueror HTML */
  -moz-user-select: none;
  /* Firefox */
  -ms-user-select: none;
  /* Internet Explorer/Edge */
  user-select: none;
}
</style>
