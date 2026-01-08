<template>
  <div class="inventory_item">
    <figure
      @click="handleEvent($event, item.item)"
      v-on="
        inventoryStore.inventoryItemEvent === null
          ? {
              mouseleave: () => itemTitle.hide(),
              mouseover: e => itemTitle.show(e),
            }
          : {}
      "
    >
      <img :src="'/images/' + item.item + '.png'" alt="inventory-item" />
      <figcaption class="tooltip">
        <span class="tooltip_item">{{ jsUcWords(item.item) }}</span>
        <br />
        x {{ item.amount }}
      </figcaption>
    </figure>
    <span class="item_amount">
      {{ itemAmountWithDelimiter }}
    </span>
  </div>
</template>

<script setup lang="ts">
import type { InventoryItem } from '@/types/InventoryItem';
import { jsUcWords } from '@/utilities/uppercase';
import { computed } from 'vue';
import { itemTitle } from '@/utilities/itemTitle';
import { useInventoryStore } from '@/ui/stores/InventoryStore';
import stockpileModule from '@/buildingScripts/stockpile';

interface Props {
  item: InventoryItem;
}

const props = defineProps<Props>();

const inventoryStore = useInventoryStore();

const itemAmountWithDelimiter = computed(() => {
  if (props.item.amount > 1000) {
    return `${(props.item.amount / 1000).toFixed(1)} k`;
  }

  return props.item.amount;
});
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
