<template>
  <div class="item">
    <figure @mouseleave="itemTitle.hide()" @mouseover="itemTitle.show($event)">
      <img :src="'/images/' + item + '.png'" alt="inventory-item" />
      <figcaption class="tooltip">
        <span class="tooltip_item">{{ jsUcWords(item) }}</span>
        <span v-if="item !== undefined && amount !== undefined">
          <br />

          x {{ amount }}
        </span>
      </figcaption>
    </figure>
    <span v-if="showAmountInput && amount !== undefined" class="item_amount">
      {{ itemAmountWithDelimiter }}
    </span>
  </div>
</template>

<script setup lang="ts">
import { jsUcWords } from '@/utilities/uppercase';
import { computed } from 'vue';
import { itemTitle } from '@/utilities/itemTitle';
import { formatItemAmount } from '@/utilities/formatters';
import { Item } from '@/types/Item';

interface Props {
  item: Item['item'];
  amount?: number;
  showAmountInput?: boolean;
}

const {
  showAmountInput = false,
  item,
  amount = undefined,
} = defineProps<Props>();

const itemAmountWithDelimiter = computed((): string | number => {
  if (amount === undefined) {
    return '';
  }
  return formatItemAmount(amount);
});
</script>
