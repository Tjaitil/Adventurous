<template>
  <AppLayout>
    <GuidesContainer
      :in-modal="false"
      :categories="categories"
      :category-guides="categoryGuides"
    />
  </AppLayout>
</template>

<script setup lang="ts">
import type { Guide } from '@/types/Guide';
import AppLayout from '@/ui/components/layout/AppLayout.vue';
import GuidesContainer from '@/ui/components/guide/GuidesContainer.vue';

interface Props {
  categories: string[];
  categoryGuides: Record<string, Guide[]>;
}
defineProps<Props>();
</script>
