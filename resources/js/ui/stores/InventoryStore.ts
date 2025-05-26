import { InventoryItem } from '@/types/InventoryItem';
import { InventoryItemEvent } from '@/types/InventoryItemEvent';
import { defineStore } from 'pinia';

interface State {
  inventoryItems: InventoryItem[];
  selectedItem: string | null;
  shouldUpdateInventory: boolean;
  inventoryItemEvent: InventoryItemEvent;
}

export const useInventoryStore = defineStore('inventory', {
  state: (): State => ({
    inventoryItems: [],
    selectedItem: null,
    shouldUpdateInventory: false,
    inventoryItemEvent: null,
  }),
  getters: {
    isInventoryFull: state => state.inventoryItems.length >= 18,
  },
  actions: {
    resetSelectedItem() {
      this.selectedItem = null;
    },
    setSelectedItem(item: string) {
      this.selectedItem = item;
    },
    setInventoryItemEvent(event: InventoryItemEvent) {
      this.inventoryItemEvent = event;
    },
    async setShouldUpdateInventory(val: boolean) {
      this.shouldUpdateInventory = val;
    },
    setInventoryItems(items: InventoryItem[]) {
      this.inventoryItems = items;
    },
  },
});
