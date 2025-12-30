<template>
  <div>
    <h3 class="mb-1 w-fit border-b-2 border-stone-300 pb-0.5 font-bold">
      {{ formatLocationName(status.location) }}
    </h3>
    <template v-if="status.crop_type !== null">
      <div v-show="!isFinished" class="flex flex-row items-center">
        <BaseItem :item="status.crop_type" />
        <p class="flex flex-row items-center gap-2">
          <BaseIcon icon="timer" />
          {{ remainder.minutes }}
          {{ $t('min') }}
          {{ remainder.seconds }}
          {{ $t('sec') }}
        </p>
      </div>
      <p v-if="isFinished">
        {{ $t('Finished') }}
      </p>
    </template>
    <p v-else-if="status.crop_type === null">{{ $t('Nothing happening') }}</p>
  </div>
</template>

<script setup lang="ts">
import type { Farmer } from '@/types/ProficiencyStatuses';
import { useCalculateTimer } from '@/ui/composables/useCalculateTimer';
import { formatLocationName } from '@/utilities/formatters';
import BaseItem from '../base/BaseItem.vue';
import BaseIcon from '../base/BaseIcon.vue';

interface Props {
  status: Farmer;
}

const { status } = defineProps<Props>();

const { remainder, calculate, isFinished } = useCalculateTimer();
calculate(new Date(status.crop_finishes_at ?? 0).getTime());
</script>
