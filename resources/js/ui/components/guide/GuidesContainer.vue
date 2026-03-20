<template>
  <div>
    <h1 class="mb-8 text-3xl font-bold">{{ $t('Guides') }}</h1>
    <div class="grid gap-10">
      <div v-for="category in categories" :key="category">
        <div
          v-if="categoryGuides[category]?.length > 0"
          class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        >
          <component
            :is="inModal ? 'div' : Link"
            v-bind="
              !inModal
                ? {
                    href: `/guides/${category}/${categoryGuides[category][0].slug}`,
                  }
                : undefined
            "
          >
            <UCard
              v-for="guide in categoryGuides[category]"
              :key="guide.slug"
              class="cursor-pointer overflow-hidden rounded-lg border border-gray-200 bg-white transition-shadow duration-200 hover:shadow-lg"
              @click="emit('selectGuide', category, guide.slug)"
            >
              <div class="block text-inherit">
                <h3 class="mb-2.5 font-semibold">
                  {{ guide.title }}
                </h3>
              </div>
              <p v-if="guide.description" class="mb-2.5 text-sm text-gray-600">
                {{ guide.description }}
              </p>
            </UCard>
          </component>
        </div>
        <p v-else class="text-gray-500 italic">
          {{ $t('No guides available') }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Guide } from '@/types/Guide';
import { Link } from '@inertiajs/vue3';

interface Props {
  inModal?: boolean;
  categories: string[];
  categoryGuides: Record<string, Guide[]>;
}

defineProps<Props>();

const emit = defineEmits<{
  selectGuide: [category: string, slug: string];
}>();
</script>
