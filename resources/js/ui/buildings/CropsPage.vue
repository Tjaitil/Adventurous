<template>
  <div class="flex flex-col gap-4">
    <h1 class="page_title">{{ $t('Crops') }}</h1>

    <BaseLoadingIcon v-if="isLoading" class="h-6 w-6 justify-self-center" />
    <template v-else>
      <ResourceProductionCountdownPanel
        :info-text="infoText"
        :remainder="remainder"
        :is-finished="isFinished"
        :is-action-active="isActionActive"
        :cancel-action-text="$t('Cancel growing')"
        :finish-action-text="$t('Harvest')"
        @cancel="updateCrop(true)"
        @finish="updateCrop(false)"
      />

      <div class="flex flex-col gap-4 md:flex-row">
        <ResourceProductionGrid
          v-model="pickedType"
          :items="cropItems"
          class="md:w-1/2"
        />

        <div
          class="flex flex-col gap-4 border-black px-1 md:w-1/2 md:border-l-2"
        >
          <ResourceProductionDetailsPanel
            :action-type-label="$t('Crops')"
            skill="farmer"
            :selected-type="pickedType"
            :level-required="pickedCrop?.farmer_level ?? null"
            :time="pickedCrop?.time ?? null"
            :experience="pickedCrop?.experience ?? null"
            :location="pickedCrop?.location ?? null"
            :efficiency-level="workforceData?.efficiency_level ?? 0"
          >
            <template #cost>
              <p>
                <span class="font-semibold">{{ $t('Seeds required') }}:</span>
                {{ pickedCrop?.seed_required ?? 0 }}
              </p>
            </template>
          </ResourceProductionDetailsPanel>

          <WorkforceInput v-model="workforceAmount" :max="availableWorkforce" />

          <UButton :disabled="!canGrow" @click="grow">
            {{ $t('Grow') }}
          </UButton>
        </div>
      </div>

      <div class="flex flex-col gap-2 border-t-2 border-black pt-4">
        <p>
          {{ $t('Select an item to get seeds from. The amount will be 1') }}
        </p>
        <BaseSelectedItem
          v-model:item="inventoryStore.currentSelectedItem"
          v-model:amount="seedAmount"
          :show-amount-input="true"
        >
          {{ $t('Select amount of seeds to generate') }}
        </BaseSelectedItem>
        <UButton
          :disabled="inventoryStore.currentSelectedItem === null"
          @click="seedGenerator"
        >
          {{ $t('Generate') }}
        </UButton>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import type { CropResource } from '@/types/CropResource';
import { cropsDataLoader } from '@/buildingScripts/buildingLoaders';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { useResourceProduction } from '@/ui/composables/useResourceProduction';
import { useInventoryStore } from '@/ui/stores/InventoryStore';
import { AdvApi } from '@/AdvApi';
import { updateHunger } from '@/clientScripts/hunger';
import { GameLogger, commonMessages } from '@/utilities/GameLogger';
import type {
  StartGrowingRequest,
  UpdateCropsRequest,
  SeedGeneratorRequest,
} from '@/types/requests/CropsRequests';
import type { advAPIResponse } from '@/types/Responses/AdvResponse';
import BaseLoadingIcon from '../components/base/BaseLoadingIcon.vue';
import BaseSelectedItem from '../components/base/BaseSelectedItem.vue';
import ResourceProductionGrid from '../components/resource-production/ResourceProductionGrid.vue';
import ResourceProductionDetailsPanel from '../components/resource-production/ResourceProductionDetailsPanel.vue';
import ResourceProductionCountdownPanel from '../components/resource-production/ResourceProductionCountdownPanel.vue';
import WorkforceInput from '../components/resource-production/WorkforceInput.vue';

interface FarmerWorkforceData {
  avail_workforce: number;
  efficiency_level: number;
}

type StartGrowingResponse = advAPIResponse<{
  avail_workforce: number;
  new_hunger: number;
}>;

type HarvestCropsResponse = advAPIResponse<{
  avail_workforce: number;
}>;

const isLoading = ref(false);
const crops = ref<CropResource[]>([]);
const workforceData = ref<FarmerWorkforceData | null>(null);
const pickedType = ref<string | null>(null);
const seedAmount = ref(1);

const inventoryStore = useInventoryStore();

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
  actionText: 'Growing',
  noActionText: 'No crops growing',
});

const cropItems = computed(() =>
  crops.value.map(crop => ({ type: crop.crop_type })),
);

const pickedCrop = computed(
  () => crops.value.find(crop => crop.crop_type === pickedType.value) ?? null,
);

const canGrow = computed(
  () => pickedType.value !== null && workforceAmount.value > 0,
);

const refreshCountdown = async () => {
  const response = await cropsDataLoader.countdown();
  startCountdown(
    response.crop_finishes_at ? response.crop_finishes_at * 1000 : null,
    response.crop_type,
  );
};

const fetchData = async () => {
  const cache = buildingDataPreloader.getBuildingCache('crops');
  if (cache) {
    crops.value = cache.action_items.crops;
    workforceData.value = cache.action_items.workforce;
    setAvailableWorkforce(cache.action_items.workforce.avail_workforce);
    startCountdown(
      cache.countdown.crop_finishes_at
        ? cache.countdown.crop_finishes_at * 1000
        : null,
      cache.countdown.crop_type,
    );
    return;
  }

  const [actionItems] = await Promise.all([
    cropsDataLoader.action_items(),
    refreshCountdown(),
  ]);
  crops.value = actionItems.crops;
  workforceData.value = actionItems.workforce;
  setAvailableWorkforce(actionItems.workforce.avail_workforce);
};

onMounted(async () => {
  isLoading.value = true;
  inventoryStore.registerSelectItemHandler();
  await fetchData();
  isLoading.value = false;
});

onUnmounted(() => {
  inventoryStore.reset();
});

const grow = async () => {
  if (workforceAmount.value === 0) {
    GameLogger.addMessage('You need to select the amount of workers', true);
    return;
  }
  if (pickedType.value === null) {
    GameLogger.addMessage(
      'You need to select the crop you are trying to grow',
      true,
    );
    return;
  }

  const data: StartGrowingRequest = {
    crop_type: pickedType.value,
    workforce_amount: workforceAmount.value,
  };

  try {
    const response = await AdvApi.post<StartGrowingResponse>(
      '/crops/start',
      data,
    );
    updateHunger(response.data.new_hunger);
    setAvailableWorkforce(response.data.avail_workforce);
    await refreshCountdown();
  } catch {
    // errors are surfaced via GameLogger through the AdvApi response interceptor
  }
};

const updateCrop = async (cancel: boolean) => {
  if (inventoryStore.isInventoryFull && !cancel) {
    GameLogger.addMessage(commonMessages.inventoryFull, true);
    return;
  }

  const data: UpdateCropsRequest = { is_cancelling: cancel };

  try {
    const response = await AdvApi.post<HarvestCropsResponse>(
      '/crops/end',
      data,
    );
    setAvailableWorkforce(response.data.avail_workforce);
    if (cancel) {
      clearCountdown();
    } else {
      await refreshCountdown();
    }
  } catch {
    // errors are surfaced via GameLogger through the AdvApi response interceptor
  }
};

const seedGenerator = async () => {
  const item = inventoryStore.currentSelectedItem;
  if (item === null) {
    GameLogger.addMessage('Please select a valid item', true);
    return;
  }

  const data: SeedGeneratorRequest = { item, amount: seedAmount.value };

  try {
    await AdvApi.post('/crops/collect-seeds', data);
    inventoryStore.resetSelectedItems();
  } catch {
    // errors are surfaced via GameLogger through the AdvApi response interceptor
  }
};
</script>
