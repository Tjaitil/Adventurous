<template>
  <div class="space-y-6">
    <div>
      <img
        src="/images/diplomacy icon.png"
        alt="Diplomacy icon"
        class="mx-auto w-12"
      />
    </div>

    <div class="grid grid-cols-2 gap-6">
      <div v-for="(value, key) in diplomacyData" :key="key" class="text-center">
        <div class="mb-2">
          <span class="border-b-2 border-stone-300 px-2 pb-0.5 text-stone-300">
            {{ formatLocationName(key) }}
          </span>
        </div>
        <p class="text-lg font-bold" :class="computedDiplomacyClasses[key]">
          {{ value }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { DiplomacyResource } from '@/types/Diplomacy';
import { jsUcWords } from '@/utilities/uppercase';
import { formatLocationName } from '@/utilities/formatters';
import { computed, ref } from 'vue';

interface Props {
  initData: DiplomacyResource;
}

const { initData: data } = defineProps<Props>();

const diplomacyData = ref<Props['initData']>(data);

const diplomacyClass = (diplomacy: number) => {
  if (typeof diplomacy !== 'number') return '';
  if (diplomacy > 1) return 'text-green-500';
  if (diplomacy < 1) return 'text-red-500';
  return '';
};

const computedDiplomacyClasses = computed(() => {
  return {
    hirtam: diplomacyClass(data.hirtam),
    pvitul: diplomacyClass(data.pvitul),
    khanz: diplomacyClass(data.khanz),
    ter: diplomacyClass(data.ter),
    fansal_plains: diplomacyClass(data.fansal_plains),
  };
});
</script>
