import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { ProficiencyStatuses } from '@/types/ProficiencyStatuses';

export const useProfiencyStatusStore = defineStore('profiencyStatus', () => {
  const profiencyStatuses = ref<ProficiencyStatuses | null>(null);

  const setProfiencyStatuses = (statuses: ProficiencyStatuses) => {
    profiencyStatuses.value = statuses;
  };

  const setMinerPermits = (location: string, permits: number) => {
    const matchedMiner = profiencyStatuses.value?.miners?.find(
      miner => miner.location === location,
    );
    if (matchedMiner === undefined) {
      return;
    }
    matchedMiner.permits = permits;
  };

  return {
    profiencyStatuses,
    setProfiencyStatuses,
    setMinerPermits,
  };
});
