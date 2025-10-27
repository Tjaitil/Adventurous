import { gameEventBus } from '@/gameEventsBus';
import { describe, expect, test, vi } from 'vitest';

describe('gameEventsBus tests', () => {
  test('emitted event is called', () => {
    const eventBus = gameEventBus;
    const callback = vi.fn();
    const callback2 = vi.fn();

    eventBus.subscribe('PLAYER_HUNTED_UPDATE', callback);
    eventBus.subscribe('PLAYER_HUNTED_UPDATE', callback2);
    eventBus.emit('PLAYER_HUNTED_UPDATE', { isHunted: true });

    expect(callback).toHaveBeenCalledWith({ isHunted: true });
    expect(callback2).toHaveBeenCalledWith({ isHunted: true });
  });
});
