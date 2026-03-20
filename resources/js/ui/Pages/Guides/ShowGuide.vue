<template>
  <AppLayout>
    <GuideContainer :guide="guide" :category="category" />
  </AppLayout>
</template>

<script setup lang="ts">
import type { Guide } from '@/types/Guide';
import GuideContainer from '@/ui/components/guide/GuideContainer.vue';
import AppLayout from '@/ui/components/layout/AppLayout.vue';
interface Props {
  guide: Guide;
  category: string;
}

defineProps<Props>();
</script>

<style></style>
