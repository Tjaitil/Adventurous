<template>
  <div v-if="selectedType" class="flex flex-col gap-3">
    <p>
      <span class="font-semibold">{{ actionTypeLabel }}:</span>
      {{ jsUcWords(selectedType) }}
    </p>
    <p>
      <span class="font-semibold">{{ $t('Time') }}:</span>
      {{ time }} {{ $t('s') }}
    </p>
    <p>
      <span class="font-semibold">{{ $t('Efficiency level reduction') }}:</span>
      - {{ baseReduction }}{{ $t('s') }} & - {{ perWorkforce }}{{ $t('s') }}
      {{ $t('each worker') }}
    </p>
    <p>
      <span class="font-semibold">{{ $t('Location') }}:</span>
      {{ formatLocationName(location ?? '') }}
    </p>
    <p :class="{ 'not-able-color': !hasRequiredLevel }">
      <span class="font-semibold">{{ $t('Level required') }}:</span>
      {{ levelRequired }}
    </p>
    <p>
      <span class="font-semibold">{{ $t('Experience') }}:</span>
      {{ experience }}
    </p>
    <slot name="cost" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { jsUcWords } from '@/utilities/uppercase';
import { formatLocationName } from '@/utilities/formatters';
import { useSkillsStore } from '@/ui/stores/SkillsStore';

interface Props {
  actionTypeLabel: string;
  skill: 'farmer' | 'miner';
  selectedType: string | null;
  levelRequired: number | null;
  time: number | null;
  experience: number | null;
  location: string | null;
  efficiencyLevel: number;
}

const props = defineProps<Props>();

const skillsStore = useSkillsStore();

const hasRequiredLevel = computed(() => {
  if (props.levelRequired === null) {
    return true;
  }
  return props.skill === 'farmer'
    ? skillsStore.hasRequiredFarmerLevel(props.levelRequired)
    : skillsStore.hasRequiredMinerLevel(props.levelRequired);
});

const baseReduction = computed(() =>
  props.time === null
    ? '0.00'
    : (props.time * (props.efficiencyLevel * 0.01)).toFixed(2),
);

const perWorkforce = computed(() =>
  props.time === null ? '0.00' : (props.time * 0.005).toFixed(2),
);
</script>
