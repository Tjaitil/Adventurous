import { defineStore } from 'pinia';
import { markRaw, ref, shallowRef } from 'vue';
import { useInventoryStore } from './InventoryStore';

interface ItemActionMenu {
  isOpen: boolean;
  item: string | null;
  insert: boolean;
}

export const useItemActionMenuStore = defineStore('itemActionMenu', () => {
  const isActive = ref(false);
  const menu = ref<ItemActionMenu>({
    isOpen: false,
    item: null,
    insert: false,
  });
  const anchorTarget = shallowRef<HTMLElement | null>(null);
  const inventoryStore = useInventoryStore();

  const addMenuEvent = (): void => {
    isActive.value = true;
    const inventoryStore = useInventoryStore();
    inventoryStore.registerCustomHandler((e, item) => {
      if (!(e.currentTarget instanceof HTMLElement)) {
        throw new Error('Expected event target to be an HTMLElement');
      }
      inventoryStore.addSelectedItem(item);
      openMenu({ item, insert: true, target: e.currentTarget });
    });
  };

  const removeMenuEvent = (): void => {
    isActive.value = false;
    closeMenu();
    inventoryStore.reset();
  };

  const openMenu = (payload: {
    item: string;
    insert: boolean;
    target: HTMLElement;
  }): void => {
    if (!isActive.value) {
      return;
    }

    if (menu.value.isOpen) {
      closeMenu();
      return;
    }

    anchorTarget.value = markRaw(payload.target);
    menu.value = {
      isOpen: true,
      item: payload.item,
      insert: payload.insert,
    };
  };

  const closeMenu = () => {
    menu.value.isOpen = false;
  };

  return {
    isActive,
    menu,
    anchorTarget,
    addMenuEvent,
    removeMenuEvent,
    openMenu,
    closeMenu,
  };
});
