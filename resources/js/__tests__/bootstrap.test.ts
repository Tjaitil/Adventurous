import { setupEchoListeners } from '@/bootstrap';
import { useInventoryStore } from '@/ui/stores/InventoryStore';
import { createPinia, setActivePinia } from 'pinia';
import { beforeEach, describe, expect, test, vi } from 'vitest';

const { listeners, privateChannel } = vi.hoisted(() => {
  const listeners: Record<string, (e: unknown) => void> = {};
  const channel = {
    listen: vi.fn((event: string, callback: (e: unknown) => void) => {
      listeners[event] = callback;
      return channel;
    }),
  };
  return { listeners, privateChannel: vi.fn(() => channel) };
});

vi.mock('laravel-echo', () => ({
  default: vi.fn(function () {
    return { private: privateChannel };
  }),
}));
vi.mock('pusher-js', () => ({ default: vi.fn() }));

describe('setupEchoListeners tests', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    setupEchoListeners(7);
  });

  test('subscribes to the private game-state channel of the user', () => {
    expect(privateChannel).toHaveBeenCalledWith('game-state.7');
  });

  test('InventoryUpdated sets inventory items from the unwrapped payload', () => {
    const items = [
      { id: 1, item: 'potato seed', amount: 3 },
      { id: 2, item: 'wheat', amount: 5 },
    ];

    listeners.InventoryUpdated({ items });

    expect(useInventoryStore().inventoryItems).toEqual(items);
  });
});
