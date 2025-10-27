<template>
  <BaseLayoutContainer>
    <AppHeader class="col-span-full row-start-1" />
    <AppSection
      class="col-span-5 col-start-2 row-start-2 max-h-[800px] min-h-[600px] px-2 py-2"
    >
      <slot></slot>
    </AppSection>
    <aside
      class="relative z-20 col-span-1 col-start-1 row-start-2 max-h-[800px]"
    >
      <slot name="aside"></slot>
    </aside>
    <AppFooter class="col-span-6 col-start-2 row-start-3" />
  </BaseLayoutContainer>
</template>

<script setup lang="ts">
import AppHeader from './AppHeader.vue';
import AppSection from './AppSection.vue';
import BaseLayoutContainer from './AppLayoutContainer.vue';
import AppFooter from './AppFooter.vue';

defineSlots<{
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  default(): any;
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  aside(): any;
}>();
</script>
