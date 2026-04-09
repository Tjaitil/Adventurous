import type { DiplomacyResource } from '@/types/Diplomacy';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useDiplomacyStore = defineStore('diplomacy', () => {

  const diplomacyData = ref<DiplomacyResource>();

  const setDiplomacyData = (data: DiplomacyResource) => {
    diplomacyData.value = data;
  };


  return {
    diplomacyData,
    setDiplomacyData, 
  }
});
