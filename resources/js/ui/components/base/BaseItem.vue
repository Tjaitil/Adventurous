<template>
  <div class="item">
    <figure @mouseenter="onMouseEnter" @mouseleave="onMouseLeave">
      <img
        :src="'/images/' + item + '.png'"
        :alt="t('game item')"
        class="item-image h-12 w-12 align-middle"
      />
    </figure>
    <span v-if="showAmount && amount !== undefined" class="item_amount">
      {{ itemAmountWithDelimiter }}
    </span>
    <ul
      ref="tooltipEl"
      popover="manual"
      class="item-tooltip bg-primary-800 min-h-20 max-w-[125px] border-2 border-black py-2 text-center text-xs text-white"
    >
      <li class="w-[120px]">
        <span class="tooltip_item">{{ jsUcWords(item) }}</span>
        <br />
        x {{ amount }}
      </li>
      <li
        v-if="item !== 'gold'"
        class="flex w-[120px] flex-row items-center justify-center gap-1"
      >
        <span class="item-price-label">{{ itemPrice }}</span>
        <img class="gold" src="/images/gold.png" :alt="$t('gold icon')" />
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { jsUcWords } from '@/utilities/uppercase';
import { computed, useId, useTemplateRef } from 'vue';
import { formatItemAmount } from '@/utilities/formatters';
import type { Item } from '@/types/Item';
import { itemPrices } from '@/clientScripts/inventory';
import { useI18n } from 'vue-i18n';

interface Props {
  disableTooltip?: boolean;
  item: Item['item'];
  amount?: number;
  showAmount?: boolean;
}

const { t } = useI18n();

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

const tooltip = useTemplateRef<HTMLElement | null>('tooltipEl');
const anchorName = `--item-${useId()}`;

const itemPrice = computed(() => itemPrices.findItem(item));

const onMouseEnter = () => {
  if (!disableTooltip) {
    tooltip.value?.showPopover();
  }
};

const onMouseLeave = () => {
  if (!disableTooltip) {
    tooltip.value?.hidePopover();
  }
};
</script>
<style>
.item figure {
  anchor-name: v-bind(anchorName);
}
.item-tooltip {
  position-anchor: v-bind(anchorName);
  position-area: right;
  position-try: flip-inline;
}

.item img {
  image-rendering: pixelated;
  image-rendering: -moz-crisp-edges;
}
.item-price-label {
  text-box-trim: trim-end;
}
</style>
