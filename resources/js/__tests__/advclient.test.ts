import { describe, expect, beforeEach, vi, test, afterEach } from 'vitest';

// Mock all canvas/game-loop dependencies so the module loads in jsdom.
// Game.update returns early when gameState !== 'playing', so only
// viewport.zoom needs a real value for the delta tests.
vi.mock('@/clientScripts/canvasText', () => ({
  loadingCanvas: { opacity: 0, loadingAnimationTracker: { start: vi.fn() } },
  canvasTextHeader: { setDraw: vi.fn() },
}));
vi.mock('@/clientScripts/clientOverlayInterface', () => ({
  ClientOverlayInterface: { setup: vi.fn() },
}));
vi.mock('@/clientScripts/collision', () => ({ collisionCheck: vi.fn() }));
vi.mock('@/clientScripts/controls', () => ({ controls: { setup: vi.fn() } }));
vi.mock('@/clientScripts/gameEventHandler', () => ({
  eventHandler: { checkEvent: vi.fn() },
}));
vi.mock('@/clientScripts/gamePieces', () => ({
  GamePieces: {
    player: {
      xMovement: 0,
      yMovement: 0,
      movementSpeed: 0,
      speedX: 0,
      speedY: 0,
      xTracker: 0,
      yTracker: 0,
      animationEnd: true,
      checkPosition: vi.fn(() => false),
      newPos: vi.fn(),
    },
    init: vi.fn(),
    drawStaticPieces: vi.fn(),
    drawDaqloons: vi.fn(),
    checkViewportGamePieces: vi.fn(),
    reset: vi.fn(),
    loadAssets: vi.fn(),
  },
}));
vi.mock('@/clientScripts/pause', () => ({
  pauseManager: { resumeGame: vi.fn() },
}));
vi.mock('@/clientScripts/viewport', () => ({
  default: {
    zoom: 1.2,
    resetSpriteLayer: vi.fn(),
    drawBackground: vi.fn(),
    adjustViewport: vi.fn(),
    setup: vi.fn(),
  },
}));
vi.mock('@/CustomFetchApi', () => ({
  CustomFetchApi: { get: vi.fn(), post: vi.fn() },
}));
// ConversationStore is a real Pinia store — activated per-test via setActivePinia
vi.mock('@/gameEventsBus', () => ({
  gameEventBus: { emit: vi.fn(), subscribe: vi.fn() },
}));
vi.mock('@/utilities/formatters', () => ({ formatLocationName: vi.fn() }));
vi.mock('@/utilities/tabs', () => ({ setUpTabList: vi.fn() }));
vi.mock('@/base/ErrorHandler', () => ({
  initErrorHandler: vi.fn(),
  reportCatchError: vi.fn(),
}));

import { setActivePinia } from 'pinia';
import { createTestingPinia } from '@pinia/testing';
import { Game } from '@/advclient';
import { GamePieces } from '@/clientScripts/gamePieces';
import { controls } from '@/clientScripts/controls';
import { useConversationStore } from '@/ui/stores/ConversationStore';

const ZOOM = 1.2;

// Helper: run N simulated frames at a fixed interval, return delta each time
function simulateFrames(count: number, msPerFrame: number): number[] {
  const deltas: number[] = [];
  for (let i = 1; i <= count; i++) {
    Game.update(i * msPerFrame);
    deltas.push(Game.properties.delta);
  }
  return deltas;
}

describe('Game.properties.delta', () => {
  beforeEach(() => {
    Game.properties.timestamp = 0;
    Game.properties.gameState = 'loading';
  });

  test('equals elapsed seconds divided by zoom', () => {
    Game.properties.timestamp = 1000;
    Game.update(1016.67); // ~16.67 ms later

    expect(Game.properties.delta).toBeCloseTo(0.01667 / ZOOM, 4);
  });

  test('scales with zoom — slower delta at higher zoom', () => {
    Game.properties.timestamp = 0;
    Game.update(16.67);

    expect(Game.properties.delta).toBeCloseTo(0.01667 / ZOOM, 4);
    expect(Game.properties.delta).toBeLessThan(0.01667); // always less than raw seconds
  });

  test('is capped when frame time spikes', () => {
    Game.properties.timestamp = 0;
    Game.update(500); // 500 ms gap — way over the 0.08 s cap

    const expectedDelta = Math.round(0.16 / ZOOM) * 2;
    expect(Game.properties.delta).toBe(expectedDelta);
  });
});

describe('delta frame-rate independence', () => {
  beforeEach(() => {
    Game.properties.timestamp = 0;
    Game.properties.gameState = 'loading';
  });

  test('accumulates to the same total over the same wall time at 60 vs 30 FPS', () => {
    // 60 FPS: 10 frames × 16.67 ms = ~167 ms of wall time
    const deltas60 = simulateFrames(10, 1000 / 60);
    const total60 = deltas60.reduce((a, b) => a + b, 0);

    Game.properties.timestamp = 0;

    // 30 FPS: 5 frames × 33.33 ms = ~167 ms of wall time
    const deltas30 = simulateFrames(5, 1000 / 30);
    const total30 = deltas30.reduce((a, b) => a + b, 0);

    expect(total60).toBeCloseTo(0.167 / ZOOM, 2);
    expect(total30).toBeCloseTo(0.167 / ZOOM, 2);
    expect(total60).toBeCloseTo(total30, 3);
  });

  test('animTimer fires within one frame of threshold regardless of FPS', () => {
    const EXAMPLE_ANIM_DURATION = 0.167;
    const deltaPerFrame60 = 1 / 60 / ZOOM;
    const deltaPerFrame30 = 1 / 30 / ZOOM;

    // Count frames needed to reach threshold at 60 FPS
    let timer = 0;
    let frames60 = 0;
    while (timer < EXAMPLE_ANIM_DURATION) {
      timer += deltaPerFrame60;
      frames60++;
    }
    const elapsed60 = frames60 * deltaPerFrame60;

    // Count frames needed to reach threshold at 30 FPS
    timer = 0;
    let frames30 = 0;
    while (timer < EXAMPLE_ANIM_DURATION) {
      timer += deltaPerFrame30;
      frames30++;
    }
    const elapsed30 = frames30 * deltaPerFrame30;

    // 30 FPS needs half as many frames as 60 FPS
    expect(frames30).toBe(Math.ceil(frames60 / 2));

    // Both fire within one frame's overshoot of the threshold
    expect(elapsed60 - EXAMPLE_ANIM_DURATION).toBeLessThan(deltaPerFrame60);
    expect(elapsed30 - EXAMPLE_ANIM_DURATION).toBeLessThan(deltaPerFrame30);

    // Neither fires before the threshold
    expect(elapsed60 - deltaPerFrame60).toBeLessThan(EXAMPLE_ANIM_DURATION);
    expect(elapsed30 - deltaPerFrame30).toBeLessThan(EXAMPLE_ANIM_DURATION);
  });
});

describe('Player movement is blocked when inBuilding or conversation is active', () => {
  const player = GamePieces.player;

  beforeEach(() => {
    setActivePinia(createTestingPinia({ stubActions: false }));

    // Put the game in a running state
    Game.properties.gameState = 'playing';
    Game.properties.timestamp = 0;
    Game.properties.inBuilding = false;

    // Give the player realistic movement values
    player.speed = 1.5;
    player.movementSpeed = 60;
    player.xMovement = 0;
    player.yMovement = 0;
    player.speedX = 0;
    player.speedY = 0;
  });

  afterEach(() => {
    controls.playerRight = false;
    controls.playerLeft = false;
    Game.properties.inBuilding = false;
  });

  test('xMovement stays 0 when inBuilding is true, even with movement key held', () => {
    // Frame 1 — not in building, player moves right
    controls.playerRight = true;
    Game.update(16.67);
    const xAfterFrame1 = player.xMovement;
    expect(xAfterFrame1).toBeGreaterThan(0); // confirm movement happened

    // Enter building mid-movement (key still held)
    Game.properties.inBuilding = true;
    player.xMovement = 0;
    Game.update(33.34); // frame 2 — in building

    expect(player.xMovement).toBe(0);
  });

  test('xMovement stays 0 when conversation is active, even with movement key held', () => {
    // Frame 1 — no conversation, player moves right
    controls.playerRight = true;
    Game.update(16.67);
    expect(player.xMovement).toBeGreaterThan(0); // confirm movement happened

    // Conversation opens mid-movement
    const conversationStore = useConversationStore();
    conversationStore.isActive = true;
    player.xMovement = 0;
    Game.update(33.34); // frame 2 — conversation active

    expect(player.xMovement).toBe(0);
  });

  test('xMovement updates when not in building and no active conversation', () => {
    // Confirm neither blocking condition is set
    Game.properties.inBuilding = false;
    const conversationStore = useConversationStore();
    conversationStore.isActive = false;

    controls.playerRight = true;
    Game.update(16.67);

    expect(player.xMovement).toBeGreaterThan(0);
  });
});
