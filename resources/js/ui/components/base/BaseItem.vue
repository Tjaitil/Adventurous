<template>
  <div class="item">
    <figure
      @mouseleave="disableTooltip ? itemTitle.hide() : null"
      @mouseover="disableTooltip ? null : itemTitle.show($event)"
    >
      <img :src="'/images/' + item + '.png'" alt="inventory-item" />
      <figcaption class="tooltip">
        <span class="tooltip_item">{{ jsUcWords(item) }}</span>
        <span v-if="item !== undefined && amount !== undefined">
          <br />

          x {{ amount }}
        </span>
      </figcaption>
    </figure>
    <span v-if="showAmount && amount !== undefined" class="item_amount">
      {{ itemAmountWithDelimiter }}
    </span>
  </div>
</template>

<script setup lang="ts">
import { jsUcWords } from '@/utilities/uppercase';
import { computed } from 'vue';
import { itemTitle } from '@/utilities/itemTitle';
import { formatItemAmount } from '@/utilities/formatters';
import type { Item } from '@/types/Item';

interface Props {
  disableTooltip?: boolean;
  item: Item['item'];
  amount?: number;
  showAmount?: boolean;
}

const {
  disableTooltip = false,
  showAmount = true,
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
