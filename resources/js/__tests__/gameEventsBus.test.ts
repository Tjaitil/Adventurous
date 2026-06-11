import { gameEventBus } from '@/gameEventsBus';
import { describe, expect, test, vi, afterEach } from 'vitest';

describe('gameEventsBus tests', () => {
  const unsubscribers: Array<() => void> = [];

  afterEach(() => {
    unsubscribers.forEach(unsub => {
      unsub();
    });
    unsubscribers.length = 0;
  });

  test('emitted event is called', () => {
    const callback = vi.fn();
    const callback2 = vi.fn();

    unsubscribers.push(
      gameEventBus.subscribe('PLAYER_HUNTED_UPDATE', callback),
    );
    unsubscribers.push(
      gameEventBus.subscribe('PLAYER_HUNTED_UPDATE', callback2),
    );
    gameEventBus.emit('PLAYER_HUNTED_UPDATE', { isHunted: true });

    expect(callback).toHaveBeenCalledWith({ isHunted: true });
    expect(callback2).toHaveBeenCalledWith({ isHunted: true });
  });

  test('unsubscribe prevents further calls', () => {
    const callback = vi.fn();

    const unsub = gameEventBus.subscribe('PLAYER_HUNTED_UPDATE', callback);
    unsub();
    gameEventBus.emit('PLAYER_HUNTED_UPDATE', { isHunted: true });

    expect(callback).not.toHaveBeenCalled();
  });
});
