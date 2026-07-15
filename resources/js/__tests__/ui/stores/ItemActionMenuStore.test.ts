import { beforeEach, describe, expect, test } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import { useInventoryStore } from '@/ui/stores/InventoryStore';
import { useItemActionMenuStore } from '@/ui/stores/ItemActionMenuStore';

describe('ItemActionMenuStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
  });

  test('addMenuEvent registers stockpile inventory handler', () => {
    const inventoryStore = useInventoryStore();
    const itemActionMenuStore = useItemActionMenuStore();

    itemActionMenuStore.addMenuEvent();

    expect(itemActionMenuStore.isActive).toBe(true);

    const target = document.createElement('div');
    const event = new MouseEvent('click');
    Object.defineProperty(event, 'currentTarget', { value: target });
    inventoryStore.handleItemClick(event, 'iron ore');

    expect(itemActionMenuStore.menu.isOpen).toBe(true);
    expect(itemActionMenuStore.menu.item).toBe('iron ore');
  });
});
