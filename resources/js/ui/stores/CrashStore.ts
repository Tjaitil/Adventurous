import axios from 'axios';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

interface CrashGameState {
  map: string;
  coordinates: { x: number; y: number };
  inBuilding: boolean;
  building: string;
}

interface CrashDetail {
  error: Error;
  gameState: CrashGameState;
}

const isDev = import.meta.env.DEV;

export const useCrashStore = defineStore('crash', () => {
  const crashInfo = ref<CrashDetail | null>(null);
  const isCrashed = computed(() => crashInfo.value !== null);

  const reportCrash = (detail: CrashDetail) => {
    crashInfo.value = detail;
    void axios.post('/crash-report', {
      error_message: detail.error.message,
      stack_trace: detail.error.stack,
      game_state: detail.gameState,
      environment: isDev ? 'dev' : 'prod',
    });
  };

  return {
    crashInfo,
    isCrashed,
    reportCrash,
  };
});
