<template>
  <div class="space-y-6 px-4">
    <UCard variant="soft">
      <img
        src="/images/farmer icon.png"
        :alt="$t('Farmer icon')"
        class="mx-auto w-12"
      />
      <FarmerStatus
        v-for="farmer in profiencyStatuses.farmers"
        :key="farmer.id"
        :status="farmer"
      />
    </UCard>

    <UCard variant="soft">
      <img
        src="/images/miner icon.png"
        :alt="$t('Miner icon')"
        class="mx-auto w-12"
      />
      <MinerStatus
        v-for="miner in profiencyStatuses.miners"
        :key="miner.id"
        :status="miner"
      >
      </MinerStatus>
    </UCard>
    <TraderStatus :status="profiencyStatuses.trader"></TraderStatus>
    <WarriorStatus :status="profiencyStatuses.warrior_statuses" />
  </div>
</template>

<script setup lang="ts">
import type { ProficiencyStatuses } from '@/types/ProficiencyStatuses';
import FarmerStatus from '../profiency-status/FarmerStatus.vue';
import MinerStatus from '../profiency-status/MinerStatus.vue';
import TraderStatus from '../profiency-status/TraderStatus.vue';
import WarriorStatus from '../profiency-status/WarriorStatus.vue';
import { reactive } from 'vue';

interface Props {
  initProfiencyStatuses: ProficiencyStatuses;
}

const { initProfiencyStatuses } = defineProps<Props>();

const profiencyStatuses = reactive<ProficiencyStatuses>(initProfiencyStatuses);

const updateProfiencyStatuses = () => {
  // update
};
</script>
