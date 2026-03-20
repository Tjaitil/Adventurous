<template>
  <div class="bg-orange-50">
    <Link
      href="/guides"
      class="mb-5 inline-block text-emerald-700 hover:underline"
      :onclick="inModal ? handleBackClick : undefined"
      >&larr; {{ t('Back to Guides') }}</Link
    >

    <div class="rounded-lg bg-orange-50 p-4 text-left">
      <div class="mb-8 border-b-2 border-emerald-500 pb-4">
        <h1 class="text-3xl font-bold text-stone-900">
          {{ guide.title }}
        </h1>
      </div>

      <div class="guide-wrapper text-base" v-html="guide.html" />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Guide } from '@/types/Guide';
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';

interface Props {
  guide: Guide;
  inModal?: boolean;
}

const { inModal = false } = defineProps<Props>();

const { t } = useI18n();

const emit = defineEmits<{
  backToGuides: [];
}>();

const handleBackClick = (event: Event): void => {
  event.preventDefault();
  event.stopPropagation();
  emit('backToGuides');
};
</script>
<style>
@reference 'tailwindcss';
.guide-wrapper {
  .guide-toc {
    width: min-content;
    min-width: 300px;
    @apply mb-6 rounded-md border border-stone-300 bg-white/70 p-4 text-sm text-stone-700;
    @apply list-disc space-y-1;
    padding-left: 1.5rem;
  }

  .guide-toc a {
    @apply text-emerald-700 hover:underline;
  }

  h2 {
    line-height: 1.5em;
    margin-bottom: 0.75rem;
    font-size: 1.5rem;
    font-weight: 600;
    color: #27272a;
  }

  h3 {
    margin-top: 1.3rem;
    margin-bottom: 0.5rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: #27272a;
  }

  li {
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
  }

  ol {
    margin-top: 1rem;
    margin-bottom: 1rem;
    list-style-type: decimal;
    padding-left: 1.5rem;
  }

  p {
    margin-bottom: 0.5rem;
  }
  table {
    margin-bottom: 1rem;
    width: 100%;
    border-collapse: collapse;
  }

  ul {
    margin-top: 1rem;
    margin-bottom: 1rem;
    list-style-type: disc;
    padding-left: 1.5rem;
  }
}
</style>
