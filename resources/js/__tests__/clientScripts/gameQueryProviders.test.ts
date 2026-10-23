import {
  registerGameQueryProviders,
  unregisterGameQueryProviders,
} from '@/clientScripts/gameQueryProviders';
import { gameQuery } from '@/gameQuery';
import { afterEach, describe, expect, test, vi } from 'vitest';

vi.mock('@/advclient', () => ({
  Game: { properties: { currentMap: '4.5' } },
}));

vi.mock('@/clientScripts/gamePieces', () => ({
  GamePieces: { player: { xpos: 120, ypos: 340 } },
}));

describe('gameQueryProviders', () => {
  afterEach(() => {
    unregisterGameQueryProviders();
  });

  test('PLAYER_POSITION returns the player position and current map', async () => {
    registerGameQueryProviders();

    await expect(gameQuery.query('PLAYER_POSITION')).resolves.toEqual({
      x: 120,
      y: 340,
      currentMap: '4.5',
    });
  });

  test('registering twice does not throw', () => {
    registerGameQueryProviders();

    expect(() => {
      registerGameQueryProviders();
    }).not.toThrow();
  });

  test('queries reject after unregistering', async () => {
    registerGameQueryProviders();
    unregisterGameQueryProviders();

    await expect(gameQuery.query('PLAYER_POSITION')).rejects.toThrow(
      'No provider registered',
    );
  });
});
