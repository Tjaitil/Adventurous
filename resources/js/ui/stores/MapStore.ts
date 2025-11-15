import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useMapStore = defineStore('map', () => {
  const isMapVisible = ref(false);

  const currentMapType = ref<'local' | 'world'>('local');

  const toggleMapVisibility = (toggle: boolean | null = null) => {
    if (toggle !== null) {
      isMapVisible.value = toggle;
    } else {
      isMapVisible.value = !isMapVisible.value;
    }
  };

  const toggleMapType = () => {
    currentMapType.value = currentMapType.value === 'local' ? 'world' : 'local';
  };

  const isIconListVisible = ref(false);
  const toggleIconListVisibility = () => {
    isIconListVisible.value = !isIconListVisible.value;
  };

  return {
    isMapVisible,
    isIconListVisible,
    toggleIconListVisibility,
    toggleMapVisibility,
    currentMapType,
    toggleMapType,
  };
});
