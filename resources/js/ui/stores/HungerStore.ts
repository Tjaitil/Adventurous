import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useHungerStore = defineStore('hunger', () => {
  const currentHunger = ref<number | null>(null);

  const setCurrentHunger = (value: number) => {
    currentHunger.value = value;
  };

  return {
    currentHunger,
    setCurrentHunger,
  };
});
