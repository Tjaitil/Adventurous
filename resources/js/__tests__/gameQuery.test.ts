import { gameQuery } from '@/gameQuery';
import { afterEach, describe, expect, test, vi } from 'vitest';

describe('gameQuery tests', () => {
  const unregisters: Array<() => void> = [];

  afterEach(() => {
    unregisters.forEach(unregister => {
      unregister();
    });
    unregisters.length = 0;
  });

  const position = { x: 10, y: 20, currentMap: '1.1' };

  test('query returns the provider result', () => {
    const handler = vi.fn(() => position);
    unregisters.push(gameQuery.provide('PLAYER_POSITION', handler));

    expect(gameQuery.query('PLAYER_POSITION')).toEqual(position);
    expect(handler).toHaveBeenCalledOnce();
  });

  test('query throws when no provider is registered', () => {
    expect(() => gameQuery.query('PLAYER_POSITION')).toThrow(
      'No provider registered for query: PLAYER_POSITION',
    );
  });

  test('query throws after the provider is unregistered', () => {
    const unregister = gameQuery.provide('PLAYER_POSITION', () => position);
    unregister();

    expect(() => gameQuery.query('PLAYER_POSITION')).toThrow();
  });

  test('providing the same query twice throws', () => {
    unregisters.push(gameQuery.provide('PLAYER_POSITION', () => position));

    expect(() => gameQuery.provide('PLAYER_POSITION', () => position)).toThrow(
      'Query provider already registered: PLAYER_POSITION',
    );
  });

  test('stale unregister does not remove a newer provider', () => {
    const unregister = gameQuery.provide('PLAYER_POSITION', () => position);
    unregister();
    const newer = { x: 1, y: 2, currentMap: '2.2' };
    unregisters.push(gameQuery.provide('PLAYER_POSITION', () => newer));
    unregister();

    expect(gameQuery.query('PLAYER_POSITION')).toEqual(newer);
  });
});
