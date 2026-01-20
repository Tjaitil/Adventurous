import { defineStore } from 'pinia';
import { ref } from 'vue';

export const usePlayerStore = defineStore('player', () => {
  const username = ref<string>('player');

  const location = ref<string>('unknown');

  return {
    username,
    location,
  };
});
