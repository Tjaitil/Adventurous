<template>
  <UApp>
    <div id="app-layout" :class="{ 'has-aside': hasAsideContent }">
      <AppHeader class="col-span-full" />
      <AppSection
        class="row-start-2 max-h-[800px] min-h-[600px] px-2 py-2"
        :class="hasAsideContent ? 'col-span-5 col-start-2' : 'col-span-full'"
      >
        <slot></slot>
      </AppSection>
      <aside
        v-if="hasAsideContent"
        class="relative z-20 col-span-1 col-start-1 row-start-2 max-h-[800px]"
      >
        <slot name="aside"></slot>
      </aside>
      <AppFooter
        class="row-start-3"
        :class="hasAsideContent ? 'col-span-6 col-start-2' : 'col-span-full'"
      />
    </div>
  </UApp>
</template>

<script setup lang="ts">
import { computed, useSlots } from 'vue';
import AppHeader from './AppHeader.vue';
import AppSection from './AppSection.vue';
import AppFooter from './AppFooter.vue';

const slots = useSlots();
const hasAsideContent = computed(() => !!slots.aside);

defineSlots<{
  default(): unknown;
  aside?(): unknown;
}>();
</script>

<style>
#app-layout {
  background-color: #120e07;
  font-family: serif;
  font-size: 16px;
  padding: 10px 5px;
  display: grid;
  grid-gap: 10px;
  width: 100%;
  grid-template-rows: 200px auto 100px;
  box-sizing: border-box;
  overflow-x: hidden;
  grid-template-columns: repeat(1, minmax(0, 1fr));
}

#app-layout.has-aside {
  grid-template-columns: 12% 87%;
}
</style>
