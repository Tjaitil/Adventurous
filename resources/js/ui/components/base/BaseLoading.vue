<template>
  <div v-if="isLoading" class="flex h-full items-center justify-center">
    <slot name="loading-icon">
      <img
        src="/images/loading.png"
        alt="loading icon"
        class="loading-icon mx-auto h-12"
      />
    </slot>
  </div>
  <div v-else>
    <slot></slot>
  </div>
</template>

<script setup lang="ts">
interface Props {
  isLoading: boolean;
}
defineProps<Props>();
</script>
