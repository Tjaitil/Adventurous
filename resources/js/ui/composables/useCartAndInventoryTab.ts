import { ref } from 'vue';

type CartInventoryTabName = 'cart' | 'inventory';

const activeTab = ref<CartInventoryTabName>('inventory');

export const useCartAndInventoryTab = () => {
  const setTab = (tab: CartInventoryTabName) => {
    activeTab.value = tab;
  };

  const resetTab = () => {
    activeTab.value = 'inventory';
  };

  return {
    activeTab,
    setTab,
    resetTab,
  };
};
