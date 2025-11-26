<template>
  <div>
    <div class="mt-1">
      <img src="/images/proficiency icon.png" :alt="t('Proficiency icon')" />
    </div>

    <table class="lightTextColor middle-align">
      <thead>
        <tr>
          <td>{{ t('proficiency.skill') }}</td>
          <td>{{ t('proficiency.level') }}</td>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, idx) in entries" :key="item.key + idx">
          <td>{{ labelForKey(item.key) }}</td>
          <td>{{ item.value }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import type { ProficiencyStatuses } from '@/types/ProficiencyStatuses';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
  profiencyStatuses: ProficiencyStatuses;
}

const { profiencyStatuses } = defineProps<Props>();

const { t } = useI18n();

type ProficiencyKey = keyof ProficiencyStatuses;

const entries = computed(() => {
  const keys: ProficiencyKey[] = [
    'Farmers',
    'Trader',
    'Miners',
    'warrior_statuses',
  ];
  return keys.map(key => ({ key, value: profiencyStatuses[key] }));
});

const prettyLabel = (key: string) => {
  const spaced = key.replace(/([a-z])([A-Z])/g, '$1 $2').replace(/[_-]+/g, ' ');

  return spaced
    .split(' ')
    .map(s => s.charAt(0).toUpperCase() + s.slice(1))
    .join(' ');
};

const labelForKey = (key: string) => {
  const lookup = `proficiency.${key}`;
  const translated = t(lookup);
  return translated === lookup ? prettyLabel(key) : translated;
};
</script>
