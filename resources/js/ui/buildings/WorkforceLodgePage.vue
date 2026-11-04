<template>
  <div class="flex flex-col gap-4">
    <h1 class="page_title">{{ $t('Workforce Lodge') }}</h1>

    <BaseLoadingIcon v-if="isLoading" class="h-6 w-6 justify-self-center" />

    <template v-else-if="workforces !== null">
      <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
        <UCard v-for="skill in skills" :id="`${skill}_workers`" :key="skill">
          <h2 class="mb-1 text-xl">
            {{ $t(`${capitalize(skill)} workforce`) }}
          </h2>
          <p class="mb-3 text-sm">
            {{ $t('Current efficiency level') }}:
            <span :id="`${skill}-efficiency-level-el`">
              {{ workforces[skill].efficiency_level }}
            </span>
          </p>
          <table class="w-full text-left text-sm">
            <tbody>
              <tr v-for="row in getRows(workforces[skill])" :key="row.label">
                <th class="py-1 font-normal">{{ row.label }}</th>
                <td
                  class="py-1 text-right"
                  :class="{ 'text-green-700': row.highlight }"
                >
                  {{ row.value }}
                </td>
              </tr>
            </tbody>
          </table>
        </UCard>
      </div>

      <UCard>
        <form
          class="mx-auto flex w-1/2 max-w-80 flex-col gap-3"
          @submit.prevent="upgradeEfficiency"
        >
          <div class="flex items-center gap-2">
            <h2 class="text-xl">{{ $t('Upgrade efficiency') }}</h2>
            <HelpTooltip>
              <span class="block w-64 whitespace-normal">
                {{
                  $t(
                    'Higher efficiency level lowers time workers spend mining or growing crops',
                  )
                }}
              </span>
            </HelpTooltip>
          </div>
          <URadioGroup
            v-model="selectedSkill"
            :items="skillItems"
            orientation="horizontal"
            :legend="$t('Profiency')"
          />
          <div id="efficiency-upgrade-info" class="text-sm">
            <p class="flex items-center gap-1">
              {{ $t('Cost') }}:
              <span id="upgrade-info-cost-el">
                {{ selectedWorkforce.efficiency_upgrade_price }}
              </span>
              <img class="gold" src="/images/gold.png" :alt="$t('gold icon')" />
            </p>
          </div>
          <UButton type="submit" :loading="isUpgrading" class="self-start">
            {{ $t('Upgrade efficiency to level') }}
            {{ selectedWorkforce.efficiency_level + 1 }}
          </UButton>
        </form>
      </UCard>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { workforceLodgeDataLoader } from '@/buildingScripts/buildingLoaders';
import { CustomFetchApi } from '@/CustomFetchApi';
import type {
  UpgradeEfficiencyRequest,
  UpgradeEfficiencyResponse,
  WorkforceData,
  WorkforceLodgeDataResponse,
  WorkforceSkill,
} from '@/types/WorkforceLodge';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { useInventoryStore } from '../stores/InventoryStore';
import BaseLoadingIcon from '../components/base/BaseLoadingIcon.vue';
import HelpTooltip from '../components/elements/HelpTooltip.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const inventoryStore = useInventoryStore();

const skills: WorkforceSkill[] = ['farmer', 'miner'];
const skillItems = skills.map(skill => ({
  label: capitalize(skill),
  value: skill,
}));

const isLoading = ref(false);
const isUpgrading = ref(false);
const workforces = ref<WorkforceLodgeDataResponse['data'] | null>(null);
const selectedSkill = ref<WorkforceSkill>('farmer');

const selectedWorkforce = computed(() => {
  if (workforces.value === null) {
    throw new Error('Workforce data not loaded');
  }
  return workforces.value[selectedSkill.value];
});

function capitalize(value: string): string {
  return value.charAt(0).toUpperCase() + value.slice(1);
}

interface WorkforceRow {
  label: string;
  value: string | number;
  highlight?: boolean;
}

const getRows = (workforce: WorkforceData): WorkforceRow[] => [
  {
    label: t('Total workforce / Max workforce'),
    value: `${workforce.workforce_total} / ${workforce.max_workers}`,
    highlight: workforce.can_recruit > 0,
  },
  ...workforce.locations.map(location => ({
    label: t(`${location.name} workforce`),
    value: location.amount,
  })),
  { label: t('Available workforce'), value: workforce.avail_workforce },
];

const fetchData = async (): Promise<void> => {
  const cachedData = buildingDataPreloader.getWorkforceLodgeData();
  if (cachedData) {
    workforces.value = cachedData.data;
    return;
  }

  try {
    isLoading.value = true;
    workforces.value = (await workforceLodgeDataLoader.res()).data;
  } finally {
    isLoading.value = false;
  }
};

const upgradeEfficiency = async (): Promise<void> => {
  const skill = selectedSkill.value;

  try {
    isUpgrading.value = true;
    const response = await CustomFetchApi.post<
      UpgradeEfficiencyResponse,
      UpgradeEfficiencyRequest
    >('/workforcelodge/efficiency/upgrade', { skill });

    if (workforces.value !== null) {
      workforces.value[skill].efficiency_level =
        response.data.data.efficiency_level;
      workforces.value[skill].efficiency_upgrade_price =
        response.data.data.new_efficiency_price;
    }
    inventoryStore.setShouldUpdateInventory(true);
  } catch {
    return;
  } finally {
    isUpgrading.value = false;
  }
};

onMounted(() => {
  void fetchData();
});
</script>
