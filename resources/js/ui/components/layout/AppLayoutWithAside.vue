<template>
  <AppLayout>
    <slot></slot>
    <template #aside>
      <slot name="aside"></slot>
    </template>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from './AppLayout.vue';

defineSlots<{
  default(): unknown;
  aside(): unknown;
}>();
</script>
