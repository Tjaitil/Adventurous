import { beforeEach, describe, expect, test } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import { useHungerStore } from '@/ui/stores/HungerStore';

describe('HungerStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
  });

  test('currentHunger defaults to null', () => {
    const hungerStore = useHungerStore();

    expect(hungerStore.currentHunger).toBeNull();
  });

  test('setCurrentHunger stores the given value', () => {
    const hungerStore = useHungerStore();

    hungerStore.setCurrentHunger(42);

    expect(hungerStore.currentHunger).toBe(42);
  });

  test('setCurrentHunger overwrites a previous value', () => {
    const hungerStore = useHungerStore();

    hungerStore.setCurrentHunger(42);
    hungerStore.setCurrentHunger(10);

    expect(hungerStore.currentHunger).toBe(10);
  });
});
