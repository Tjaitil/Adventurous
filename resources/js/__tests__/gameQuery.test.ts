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

  test('query resolves with the provider result', async () => {
    const handler = vi.fn(() => position);
    unregisters.push(gameQuery.provide('PLAYER_POSITION', handler));

    await expect(gameQuery.query('PLAYER_POSITION')).resolves.toEqual(position);
    expect(handler).toHaveBeenCalledOnce();
  });

  test('query resolves with an async provider result', async () => {
    unregisters.push(
      gameQuery.provide('PLAYER_POSITION', () => Promise.resolve(position)),
    );

    await expect(gameQuery.query('PLAYER_POSITION')).resolves.toEqual(position);
  });

  test('query rejects when no provider is registered', async () => {
    await expect(gameQuery.query('PLAYER_POSITION')).rejects.toThrow(
      'No provider registered for query: PLAYER_POSITION',
    );
  });

  test('query rejects after the provider is unregistered', async () => {
    const unregister = gameQuery.provide('PLAYER_POSITION', () => position);
    unregister();

    await expect(gameQuery.query('PLAYER_POSITION')).rejects.toThrow();
  });

  test('providing the same query twice throws', () => {
    unregisters.push(gameQuery.provide('PLAYER_POSITION', () => position));

    expect(() => gameQuery.provide('PLAYER_POSITION', () => position)).toThrow(
      'Query provider already registered: PLAYER_POSITION',
    );
  });

  test('stale unregister does not remove a newer provider', async () => {
    const unregister = gameQuery.provide('PLAYER_POSITION', () => position);
    unregister();
    const newer = { x: 1, y: 2, currentMap: '2.2' };
    unregisters.push(gameQuery.provide('PLAYER_POSITION', () => newer));
    unregister();

    await expect(gameQuery.query('PLAYER_POSITION')).resolves.toEqual(newer);
  });
});
