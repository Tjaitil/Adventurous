<template>
  <AppLayout>
    <Head :title="title" />
    <div
      class="bg-p-6 flex h-full flex-col items-center justify-center gap-y-6 text-center font-serif"
    >
      <p class="text-6xl font-bold">{{ status }}</p>
      <div class="space-y-2">
        <h1 class="text-2xl">{{ title }}</h1>
        <p class="text-white/70">{{ description }}</p>
      </div>
      <UButton to="/">{{ t('Back to dashboard') }}</UButton>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '../components/layout/AppLayout.vue';
import { useI18n } from 'vue-i18n';

interface Props {
  status: number;
}

const { t } = useI18n();

const { status } = defineProps<Props>();

const messages: Record<number, { title: string; description: string }> = {
  403: {
    title: 'Forbidden',
    description: 'You are not allowed to access this page.',
  },
  404: {
    title: 'Page not found',
    description: 'The page you were looking for does not exist.',
  },
  500: {
    title: 'Server error',
    description: 'Something went wrong on our end. Please try again later.',
  },
  503: {
    title: 'Adventurous is down for maintenance',
    description: "We'll be back shortly.",
  },
};

const fallback = {
  title: 'Something went wrong',
  description: 'An unexpected error occurred.',
};

const title = computed(() => (messages[status] ?? fallback).title);
const description = computed(() => (messages[status] ?? fallback).description);
</script>
