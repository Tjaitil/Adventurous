import { computed, ref } from 'vue';
import { useCalculateTimer } from './useCalculateTimer';
import { jsUcWords } from '@/utilities/uppercase';

interface UseResourceProductionOptions {
  actionText: string;
  noActionText: string;
}

/**
 * Vue-reactive shared logic for the farmer/miner production cycle
 * (workforce, countdown, start/cancel/finish), replacing SkillActionContainer.
 */
export function useResourceProduction({
  actionText,
  noActionText,
}: UseResourceProductionOptions) {
  const { remainder, isFinished, calculate, stopTimer } = useCalculateTimer();

  const workforceAmount = ref(0);
  const availableWorkforce = ref(0);
  const selectedType = ref<string | null>(null);
  const isActionActive = ref(false);

  const infoText = computed(() => {
    if (!isActionActive.value) {
      return noActionText;
    }
    if (isFinished.value) {
      return 'Finished';
    }
    return `${actionText} ${jsUcWords(selectedType.value ?? '')}`;
  });

  function startCountdown(endTime: number | null, type: string | null) {
    selectedType.value = type;

    if (endTime === null || type === null) {
      isActionActive.value = false;
      return;
    }

    isActionActive.value = true;
    calculate(endTime);
  }

  function clearCountdown() {
    isActionActive.value = false;
    selectedType.value = null;
    stopTimer();
  }

  function setAvailableWorkforce(amount: number) {
    availableWorkforce.value = amount;
  }

  return {
    workforceAmount,
    availableWorkforce,
    selectedType,
    remainder,
    isFinished,
    isActionActive,
    infoText,
    startCountdown,
    clearCountdown,
    setAvailableWorkforce,
  };
}
