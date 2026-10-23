import {
  registerConversationHandlers,
  unregisterConversationHandlers,
} from '@/clientScripts/conversationHandler';
import { gameEventBus } from '@/gameEventsBus';
import { afterEach, beforeEach, describe, expect, test, vi } from 'vitest';

const mocks = vi.hoisted(() => ({
  properties: { inConversation: false },
  setGameState: vi.fn<(state: string) => void>(),
  reportError: vi.fn<(error: unknown) => void>(),
  hideOverlay: vi.fn<() => void>(),
  resumeGame: vi.fn<() => void>(),
  travel: vi.fn<(destination: string, person: string) => boolean>(),
  loadBuilding: vi.fn<(building: string) => void>(),
  addMessage: vi.fn<(message: string, log: boolean) => void>(),
}));

vi.mock('@/advclient', () => ({
  Game: {
    properties: mocks.properties,
    setGameState: mocks.setGameState,
    reportError: mocks.reportError,
  },
}));

vi.mock('@/clientScripts/clientOverlayInterface', () => ({
  ClientOverlayInterface: {
    hide: mocks.hideOverlay,
  },
}));

vi.mock('@/clientScripts/pause', () => ({
  pauseManager: {
    resumeGame: mocks.resumeGame,
  },
}));

vi.mock('@/clientScripts/gameTravel', () => ({
  gameTravel: {
    travel: mocks.travel,
  },
}));

vi.mock('@/conversationCallbacks/loadBuilding', () => ({
  loadBuildingCallback: mocks.loadBuilding,
}));

vi.mock('@/utilities/GameLogger', () => ({
  GameLogger: {
    addMessage: mocks.addMessage,
  },
}));

describe('conversationHandler', () => {
  beforeEach(() => {
    vi.clearAllMocks();
    mocks.properties.inConversation = false;
    registerConversationHandlers();
  });

  afterEach(() => {
    unregisterConversationHandlers();
  });

  describe('CONVERSATION_STARTED', () => {
    test('sets conversation state and hides overlay', () => {
      gameEventBus.emit('CONVERSATION_STARTED', { displayName: 'Pesr' });

      expect(mocks.properties.inConversation).toBe(true);
      expect(mocks.setGameState).toHaveBeenCalledExactlyOnceWith(
        'conversation',
      );
      expect(mocks.hideOverlay).toHaveBeenCalledOnce();
    });

    test('registering twice does not duplicate handlers', () => {
      registerConversationHandlers();

      gameEventBus.emit('CONVERSATION_STARTED', { displayName: 'Pesr' });

      expect(mocks.setGameState).toHaveBeenCalledOnce();
    });

    test('unregistered handlers are not called', () => {
      unregisterConversationHandlers();

      gameEventBus.emit('CONVERSATION_STARTED', { displayName: 'Pesr' });

      expect(mocks.setGameState).not.toHaveBeenCalled();
    });
  });

  describe('UI_CONVERSATION_ENDED', () => {
    test('resumes the game once even when emitted repeatedly', () => {
      gameEventBus.emit('CONVERSATION_STARTED', { displayName: 'Pesr' });

      gameEventBus.emit('UI_CONVERSATION_ENDED', undefined);
      gameEventBus.emit('UI_CONVERSATION_ENDED', undefined);

      expect(mocks.properties.inConversation).toBe(false);
      expect(mocks.resumeGame).toHaveBeenCalledOnce();
    });

    test('does nothing when no conversation is active', () => {
      gameEventBus.emit('UI_CONVERSATION_ENDED', undefined);

      expect(mocks.resumeGame).not.toHaveBeenCalled();
    });
  });

  describe('UI_CONVERSATION_CALLBACK', () => {
    test('GameTravelCallback travels to the location', () => {
      gameEventBus.emit('UI_CONVERSATION_CALLBACK', {
        callback: 'GameTravelCallback',
        values: { location: 'Golbak' },
        person: 'Pesr',
      });

      expect(mocks.travel).toHaveBeenCalledExactlyOnceWith('Golbak', 'Pesr');
    });

    test.for([null, {}, { location: 1 }])(
      'GameTravelCallback with invalid values %j logs an error',
      values => {
        gameEventBus.emit('UI_CONVERSATION_CALLBACK', {
          callback: 'GameTravelCallback',
          values,
          person: 'Pesr',
        });

        expect(mocks.travel).not.toHaveBeenCalled();
        expect(mocks.addMessage).toHaveBeenCalledExactlyOnceWith(
          'An error occured',
          true,
        );
      },
    );

    test('LoadZinsStoreCallback loads the zins store', () => {
      gameEventBus.emit('UI_CONVERSATION_CALLBACK', {
        callback: 'LoadZinsStoreCallback',
        values: null,
        person: 'Zins',
      });

      expect(mocks.loadBuilding).toHaveBeenCalledExactlyOnceWith('zinsstore');
    });

    test.for(['Unknown', 'toString', '__proto__'])(
      'unknown callback %s is reported',
      callback => {
        gameEventBus.emit('UI_CONVERSATION_CALLBACK', {
          callback,
          values: null,
          person: 'Pesr',
        });

        expect(mocks.reportError).toHaveBeenCalledOnce();
        expect(mocks.travel).not.toHaveBeenCalled();
        expect(mocks.loadBuilding).not.toHaveBeenCalled();
      },
    );
  });
});
