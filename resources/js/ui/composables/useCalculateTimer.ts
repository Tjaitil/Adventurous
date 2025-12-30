import { computed, reactive, ref, onUnmounted } from 'vue';

export function useCalculateTimer() {
  const remainder = reactive({
    days: 0,
    hours: 0,
    minutes: 0,
    seconds: 0,
  });

  const isFinished = computed(() => {
    return (
      remainder.days <= 0 &&
      remainder.hours <= 0 &&
      remainder.minutes <= 0 &&
      remainder.seconds <= 0
    );
  });

  const timeoutId = ref<number | null>(null);

  const calculate = (endTime: number) => {
    timeoutId.value = window.setTimeout(() => {
      const now = new Date().getTime();
      const remainderTime = endTime - now;
      const days = Math.floor(remainderTime / (1000 * 60 * 60 * 24));
      const hours = Math.floor(
        (remainderTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60),
      );
      const minutes = Math.floor(
        (remainderTime % (1000 * 60 * 60)) / (1000 * 60),
      );
      const seconds = Math.floor((remainderTime % (1000 * 60)) / 1000);

      if (remainderTime <= 0) {
        remainder.days = 0;
        remainder.hours = 0;
        remainder.minutes = 0;
        remainder.seconds = 0;
        return;
      }

      remainder.days = days;
      remainder.hours = hours;
      remainder.minutes = minutes;
      remainder.seconds = seconds;
      calculate(endTime);
    }, 1000);
  };

  const stopTimer = () => {
    if (timeoutId.value !== null) {
      clearTimeout(timeoutId.value);
      timeoutId.value = null;
    }
  };

  onUnmounted(() => {
    stopTimer();
  });

  return {
    isFinished,
    remainder,
    calculate,
    stopTimer,
  };
}
