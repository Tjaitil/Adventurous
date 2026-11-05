<template>
  <div
    class="relative inline-block"
    :class="`help-tooltip-${size}`"
    @mouseenter="showTooltip"
    @focusin="showTooltip"
    @focusout="hideTooltip"
    @mouseleave="hideTooltip"
  >
    <button
      type="button"
      class="custom-button"
      :style="{ 'anchor-name': anchorName }"
      :aria-label="$t('Help tooltip')"
      :aria-describedby="tooltipId"
    >
      <img src="/images/help icon.png" :alt="$t('Help icon')" />
    </button>
    <div
      :id="tooltipId"
      ref="tooltipEl"
      popover="manual"
      role="tooltip"
      class="border-primary-700 pixelated-corners-sm m-0 border-2 bg-(--ui-bg-elevated) p-2 text-sm whitespace-nowrap text-inherit [position-area:bottom] [position-try:flip-block]"
    >
      <slot></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useId, useTemplateRef } from 'vue';

interface Props {
  size?: 'sm' | 'md';
}
const { size = 'md' } = defineProps<Props>();

const tooltip = useTemplateRef<HTMLElement | null>('tooltipEl');
const id = useId();
const anchorName = `--help-tooltip-${id}`;
const tooltipId = `help-tooltip-${id}`;

const showTooltip = () => {
  tooltip.value?.showPopover();
};

const hideTooltip = () => {
  tooltip.value?.hidePopover();
};
</script>
<style scoped>
.help-tooltip-sm {
  --width: calc(var(--spacing) * 4);
  --height: calc(var(--spacing) * 4);
  width: var(--width);
  height: var(--height);
}
.help-tooltip-md {
  --width: calc(var(--spacing) * 6);
  --height: calc(var(--spacing) * 6);
  width: var(--width);
  height: var(--height);
}
img {
  width: var(--width);
  height: var(--height);
}

[role='tooltip'] {
  position-anchor: v-bind(anchorName);
}
</style>
