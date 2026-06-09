import { describe, expect, beforeEach, vi, test } from 'vitest';
import { gameEventBus } from '@/gameEventsBus';
import { Player } from '@/gamepieces/Player';

vi.mock('@/clientScripts/viewport', () => ({
  default: {
    drawPlayer: vi.fn(),
    resetPlayerLayer: vi.fn(),
    drawAttackCoolDown: vi.fn(),
  },
}));

vi.mock('@/clientScripts/canvasText', () => ({
  canvasTextHeader: {
    setDraw: vi.fn(),
  },
}));

vi.mock('@/advclient', () => ({
  Game: {
    setWorld: vi.fn(),
    properties: { duration: 0 },
  },
}));

vi.mock('@/clientScripts/controls', () => ({
  controls: {},
}));

vi.mock('@/clientScripts/gamePieces', () => ({
  GamePieces: { daqloon: [] },
}));

vi.mock('@/devtools/ModuleTester', () => ({
  addModuleTester: vi.fn(),
}));

describe('Player events', () => {
  let player: Player;

  beforeEach(() => {
    player = new Player();
    player.health = 100;
    vi.useFakeTimers();
  });

  test('takeDamage emits PLAYER_HEALTH_UPDATE with reduced health', () => {
    const callback = vi.fn();
    gameEventBus.subscribe('PLAYER_HEALTH_UPDATE', callback);

    player.takeDamage(20);

    expect(callback).toHaveBeenCalledWith({ health: 80 });
  });

  test('takeDamage does not emit when damage is 0', () => {
    const callback = vi.fn();
    gameEventBus.subscribe('PLAYER_HEALTH_UPDATE', callback);

    player.takeDamage(0);

    expect(callback).not.toHaveBeenCalled();
  });

  test('takeDamage does not emit when damage is NaN', () => {
    const callback = vi.fn();
    gameEventBus.subscribe('PLAYER_HEALTH_UPDATE', callback);

    player.takeDamage(NaN);

    expect(callback).not.toHaveBeenCalled();
  });

  test('takeDamage emits PLAYER_HEALTH_UPDATE with health reset to 100 on death', () => {
    const callback = vi.fn();
    gameEventBus.subscribe('PLAYER_HEALTH_UPDATE', callback);

    // When health drops to 0 the player dies and health is immediately reset to 100
    player.takeDamage(150);

    expect(callback).toHaveBeenCalledWith({ health: 100 });
  });

  test('regenerateHealth emits PLAYER_HEALTH_UPDATE with increased health', () => {
    const callback = vi.fn();
    player.health = 60;
    gameEventBus.subscribe('PLAYER_HEALTH_UPDATE', callback);

    player.regenerateHealth();

    expect(callback).toHaveBeenCalledWith({ health: 70 });
  });

  test('regenerateHealth clamps health at 100 and emits', () => {
    const callback = vi.fn();
    player.health = 95;
    gameEventBus.subscribe('PLAYER_HEALTH_UPDATE', callback);

    player.regenerateHealth();

    expect(callback).toHaveBeenCalledWith({ health: 100 });
  });

  test('load emits PLAYER_HEALTH_UPDATE to sync HUD on respawn', () => {
    const callback = vi.fn();
    player.health = 100;
    gameEventBus.subscribe('PLAYER_HEALTH_UPDATE', callback);

    player.load(100, 200, null);

    expect(callback).toHaveBeenCalledWith({ health: 100 });
  });

  test('regenerateHealth does not emit when health is 0', () => {
    const callback = vi.fn();
    player.health = 0;
    gameEventBus.subscribe('PLAYER_HEALTH_UPDATE', callback);

    player.regenerateHealth();

    expect(callback).not.toHaveBeenCalled();
  });
});
