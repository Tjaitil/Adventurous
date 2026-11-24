import { describe, expect, beforeEach, vi, test } from 'vitest';
import { gameEventBus } from '@/gameEventsBus';
import { Player } from '@/gamepieces/Player';
import { Game } from '@/advclient';
import { canvasTextHeader } from '@/clientScripts/canvasText';

vi.mock('@/clientScripts/viewport', () => ({
  default: {
    drawPlayer: vi.fn(),
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

describe('Player newPos', () => {
  let player: Player;

  beforeEach(() => {
    player = new Player();
    player.load(100, 200, null);
  });

  test('sets position to loaded position plus movement', () => {
    player.xMovement = 15;
    player.yMovement = -25;

    player.newPos(0);

    expect(player.xpos).toBe(115);
    expect(player.ypos).toBe(175);
    expect(player.diameterUp).toBe(195);
    expect(player.diameterRight).toBe(147);
    expect(player.diameterDown).toBe(211);
    expect(player.diameterLeft).toBe(119);
  });

  test('does not accumulate movement across calls', () => {
    player.xMovement = 15;

    player.newPos(0);
    player.newPos(0);

    expect(player.xpos).toBe(115);
  });

  test('uses the new position and resets movement after loading again', () => {
    player.xMovement = 15;
    player.yMovement = 15;
    player.newPos(0);

    player.load(500, 600, null);
    player.newPos(0);

    expect(player.xpos).toBe(500);
    expect(player.ypos).toBe(600);
  });

  test('keeps position when newPos is false', () => {
    player.xMovement = 15;
    player.yMovement = 15;

    player.newPos(0, false);

    expect(player.xpos).toBe(100);
    expect(player.ypos).toBe(200);
  });
});

describe('Player death event', () => {
  let player: Player;

  beforeEach(() => {
    vi.clearAllMocks();
    vi.useFakeTimers();
    player = new Player();
    player.health = 100;
  });

  test('takeDamage emits death when health reaches 0', () => {
    const onDeath = vi.fn();
    player.events.on('death', onDeath);

    player.takeDamage(100);

    expect(onDeath).toHaveBeenCalledOnce();
  });

  test('takeDamage does not emit death when the player survives', () => {
    const onDeath = vi.fn();
    player.events.on('death', onDeath);

    player.takeDamage(99);

    expect(onDeath).not.toHaveBeenCalled();
  });

  test('takeDamage emits death once per lethal hit', () => {
    const onDeath = vi.fn();
    player.events.on('death', onDeath);

    player.takeDamage(500);

    expect(onDeath).toHaveBeenCalledOnce();
  });

  test('takeDamage does not emit death for zero or NaN damage', () => {
    const onDeath = vi.fn();
    player.events.on('death', onDeath);

    player.takeDamage(0);
    player.takeDamage(NaN);

    expect(onDeath).not.toHaveBeenCalled();
  });

  test('death handlers see health already reset to 100', () => {
    let healthOnDeath = -1;
    player.events.on('death', () => {
      healthOnDeath = player.health;
    });

    player.takeDamage(100);

    expect(healthOnDeath).toBe(100);
  });

  test('takeDamage shows the death text only when the player dies', () => {
    player.takeDamage(50);
    expect(canvasTextHeader.setDraw).not.toHaveBeenCalled();

    player.takeDamage(50);
    expect(canvasTextHeader.setDraw).toHaveBeenCalledExactlyOnceWith(
      'You died!',
      2,
    );
  });

  test('player can die again after the health reset', () => {
    const onDeath = vi.fn();
    player.events.on('death', onDeath);

    player.takeDamage(100);
    player.takeDamage(100);

    expect(onDeath).toHaveBeenCalledTimes(2);
  });

  test('takeDamage does not request a respawn itself', () => {
    player.takeDamage(100);
    vi.advanceTimersByTime(5000);

    expect(Game.setWorld).not.toHaveBeenCalled();
  });
});
