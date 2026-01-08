<template>
  <UCard variant="soft">
    <img
      src="/images/warrior icon.png"
      :alt="$t('Warrior icon')"
      class="mx-auto w-12"
    />
    <h3 class="mt-4 space-y-6 text-lg font-bold">{{ $t('Warrior(s)') }}</h3>
    <div class="grid grid-cols-2 gap-3">
      <div v-for="item in statusItems" :key="item.label" class="text-center">
        <div class="mb-2 border-b-2 border-stone-200">
          <p class="px-2 pb-0.5 text-stone-200">
            {{ item.label }}
          </p>
        </div>
        <p
          class="text-lg font-bold"
          :class="computedDiplomacyClasses[item.label]"
        >
          {{ item.value }}
        </p>
      </div>
    </div>
  </UCard>
</template>

<script setup lang="ts">
import type { WarriorStatuses } from '@/types/ProficiencyStatuses';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
  status: WarriorStatuses;
}
const { status } = defineProps<Props>();

const { t } = useI18n();

const statusItems = computed(() => [
  { label: t('Training'), value: status.statuses.training },
  { label: t('On mission'), value: status.statuses.on_mission },
  { label: t('Resting'), value: status.statuses.resting },
  { label: t('Idle'), value: status.statuses.idle },
  { label: t('Finished Training'), value: status.statuses.finished_training },
]);

const computedDiplomacyClasses = computed(() => ({
  // Add classes based on key if needed
}));
</script>
