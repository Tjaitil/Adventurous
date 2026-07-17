<template>
  <div class="flex flex-col gap-4">
    <h1 class="page_title">{{ $t('Mine') }}</h1>

    <BaseLoadingIcon v-if="isLoading" class="h-6 w-6 justify-self-center" />
    <template v-else>
      <p>
        <span class="font-semibold">{{ $t('Your total permits') }}:</span>
        {{ permits }}
      </p>

      <ResourceProductionCountdownPanel
        :info-text="infoText"
        :remainder="remainder"
        :is-finished="isFinished"
        :is-action-active="isActionActive"
        :cancel-action-text="$t('Cancel mining')"
        :finish-action-text="$t('Fetch minerals')"
        @cancel="updateMine(true)"
        @finish="updateMine(false)"
      />

      <div class="flex flex-col gap-4 md:flex-row">
        <ResourceProductionGrid
          v-model="pickedType"
          :items="mineralItems"
          class="md:w-1/2"
        />

        <div
          class="flex flex-col gap-4 border-black px-1 md:w-1/2 md:border-l-2"
        >
          <ResourceProductionDetailsPanel
            :action-type-label="$t('Minerals')"
            skill="miner"
            :selected-type="pickedType"
            :level-required="pickedMineral?.miner_level ?? null"
            :time="pickedMineral?.time ?? null"
            :experience="pickedMineral?.experience ?? null"
            :location="pickedMineral?.location ?? null"
            :efficiency-level="workforceData?.efficiency_level ?? 0"
          >
            <template #cost>
              <p>
                <span class="font-semibold">{{ $t('Permit cost') }}:</span>
                {{ pickedMineral?.permit_cost ?? 0 }}
              </p>
            </template>
          </ResourceProductionDetailsPanel>

          <WorkforceInput v-model="workforceAmount" :max="availableWorkforce" />

          <UButton :disabled="!canMine" @click="startMining">
            {{ $t('Mine') }}
          </UButton>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import type { MineralResource } from '@/types/MineralResource';
import { mineDataLoader } from '@/buildingScripts/buildingLoaders';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { useResourceProduction } from '@/ui/composables/useResourceProduction';
import { AdvApi } from '@/AdvApi';
import { updateHunger } from '@/clientScripts/hunger';
import { GameLogger } from '@/utilities/GameLogger';
import type { advAPIResponse } from '@/types/Responses/AdvResponse';
import BaseLoadingIcon from '../components/base/BaseLoadingIcon.vue';
import ResourceProductionGrid from '../components/resource-production/ResourceProductionGrid.vue';
import ResourceProductionDetailsPanel from '../components/resource-production/ResourceProductionDetailsPanel.vue';
import ResourceProductionCountdownPanel from '../components/resource-production/ResourceProductionCountdownPanel.vue';
import WorkforceInput from '../components/resource-production/WorkforceInput.vue';

interface MinerWorkforceData {
  avail_workforce: number;
  efficiency_level: number;
}

interface StartMiningRequest {
  mineral_ore: string;
  workforce_amount: number;
}

interface UpdateMiningRequest {
  is_cancelling: boolean;
}

type StartMiningResponse = advAPIResponse<{
  avail_workforce: number;
  new_permits: number;
  new_hunger: number;
}>;

type FinishMiningResponse = advAPIResponse<{
  avail_workforce: number;
  new_hunger: number;
}>;

const isLoading = ref(false);
const minerals = ref<MineralResource[]>([]);
const workforceData = ref<MinerWorkforceData | null>(null);
const permits = ref(0);
const pickedType = ref<string | null>(null);

const {
  workforceAmount,
  availableWorkforce,
  remainder,
  isFinished,
  isActionActive,
  infoText,
  startCountdown,
  clearCountdown,
  setAvailableWorkforce,
} = useResourceProduction({
  actionText: 'Mining for',
  noActionText: 'No miners at work',
});

const mineralItems = computed(() =>
  minerals.value.map(mineral => ({ type: mineral.mineral_ore })),
);

const pickedMineral = computed(
  () =>
    minerals.value.find(mineral => mineral.mineral_ore === pickedType.value) ??
    null,
);

const canMine = computed(
  () => pickedType.value !== null && workforceAmount.value > 0,
);

const refreshCountdown = async () => {
  const response = await mineDataLoader.countdown();
  startCountdown(
    response.mining_finishes_at ? response.mining_finishes_at * 1000 : null,
    response.mineral_ore,
  );
};

const fetchData = async () => {
  const cache = buildingDataPreloader.getBuildingCache('mine');
  if (cache) {
    minerals.value = cache.action_items.minerals;
    workforceData.value = cache.action_items.workforce;
    permits.value = cache.action_items.permits ?? 0;
    setAvailableWorkforce(cache.action_items.workforce.avail_workforce);
    startCountdown(
      cache.countdown.mining_finishes_at
        ? cache.countdown.mining_finishes_at * 1000
        : null,
      cache.countdown.mineral_ore,
    );
    return;
  }

  const [actionItems] = await Promise.all([
    mineDataLoader.action_items(),
    refreshCountdown(),
  ]);
  minerals.value = actionItems.minerals;
  workforceData.value = actionItems.workforce;
  permits.value = actionItems.permits ?? 0;
  setAvailableWorkforce(actionItems.workforce.avail_workforce);
};

onMounted(async () => {
  isLoading.value = true;
  await fetchData();
  isLoading.value = false;
});

const startMining = async () => {
  if (workforceAmount.value === 0) {
    GameLogger.addMessage('You need to select the amount of workers', true);
    return;
  }
  if (pickedType.value === null) {
    GameLogger.addMessage('You need to select at least one mineral', true);
    return;
  }

  const data: StartMiningRequest = {
    mineral_ore: pickedType.value,
    workforce_amount: workforceAmount.value,
  };

  try {
    const response = await AdvApi.post<StartMiningResponse>(
      '/mine/start',
      data,
    );
    updateHunger(response.data.new_hunger);
    setAvailableWorkforce(response.data.avail_workforce);
    permits.value = response.data.new_permits;
    await refreshCountdown();
  } catch {
    // errors are surfaced via GameLogger through the AdvApi response interceptor
  }
};

const updateMine = async (cancel: boolean) => {
  const data: UpdateMiningRequest = { is_cancelling: cancel };

  try {
    const response = await AdvApi.post<FinishMiningResponse>('/mine/end', data);
    setAvailableWorkforce(response.data.avail_workforce);
    updateHunger(response.data.new_hunger);
    if (cancel) {
      clearCountdown();
    } else {
      await refreshCountdown();
    }
  } catch {
    // errors are surfaced via GameLogger through the AdvApi response interceptor
  }
};
</script>
