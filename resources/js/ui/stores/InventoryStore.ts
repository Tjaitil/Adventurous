import type { InventoryItem } from '@/types/InventoryItem';
import { defineStore } from 'pinia';

type ItemHandler = (e: Event, itemName: string) => void;

let clickHandler: ItemHandler | null = null;

interface State {
  inventoryItems: InventoryItem[];
  selectedItems: string[];
  shouldUpdateInventory: boolean;
  showTooltips: boolean;
}

export const useInventoryStore = defineStore('inventory', {
  state: (): State => ({
    inventoryItems: [],
    selectedItems: [],
    shouldUpdateInventory: false,
    showTooltips: true,
  }),
  getters: {
    isInventoryFull: state => state.inventoryItems.length >= 18,
    currentSelectedItem: (state): string | null =>
      state.selectedItems.slice(-1)[0] ?? null,
  },
  actions: {
    resetSelectedItems() {
      this.selectedItems = [];
    },
    addSelectedItem(item: string) {
      if (!this.selectedItems.includes(item)) {
        this.selectedItems.push(item);
      }
    },
    removeSelectedItem(item: string) {
      this.selectedItems = this.selectedItems.filter(
        selectedItem => selectedItem !== item,
      );
    },
    registerCustomHandler(handler: ItemHandler | null) {
      clickHandler = handler;
    },
    registerSelectItemHandler() {
      clickHandler = (e: Event, itemName: string) => {
        this.addSelectedItem(itemName);
      };
    },
    resetClickHandler() {
      clickHandler = null;
    },
    reset() {
      this.resetSelectedItems();
      this.resetClickHandler();
    },
    handleItemClick(e: Event, itemName: string) {
      if (clickHandler !== null) {
        clickHandler(e, itemName);
      }
      return;
    },
    setShouldUpdateInventory(val: boolean) {
      this.shouldUpdateInventory = val;
    },
    setInventoryItems(items: InventoryItem[]) {
      this.inventoryItems = items;
    },
  },
});
