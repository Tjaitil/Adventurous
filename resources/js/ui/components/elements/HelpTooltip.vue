<template>
  <div
    class="relative inline-block h-6 w-6"
    @mouseenter="showTooltip"
    @focus="showTooltip"
    @blur="hideTooltip"
    @mouseleave="hideTooltip"
  >
    <button
      ref="buttonRef"
      class="custom-button"
      :aria-label="$t('Help tooltip')"
    >
      <img
        src="/images/help icon.png"
        :alt="$t('Help icon')"
        width="24px"
        height="24px"
      />
    </button>
    <transition name="fade">
      <div
        v-if="isTooltipVisible"
        class="border-primary-700 pixelated-corners-sm absolute z-50 -translate-x-[44%] rounded border-2 bg-(--light-color) p-2 text-sm whitespace-nowrap"
      >
        <slot></slot>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const isTooltipVisible = ref(false);
const showTooltip = () => {
  isTooltipVisible.value = true;
};

const hideTooltip = () => {
  isTooltipVisible.value = false;
};
</script>

<style scoped>
.fade-enter-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from {
  opacity: 0;
}
</style>
