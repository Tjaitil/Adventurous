import {
  registerConversationHandlers,
  unregisterConversationHandlers,
} from '@/clientScripts/conversationHandler';
import { gameEventBus } from '@/gameEventsBus';
import { afterEach, beforeEach, describe, expect, test, vi } from 'vitest';

const mocks = vi.hoisted(() => ({
  setGameState: vi.fn<(state: string) => void>(),
  hideOverlay: vi.fn<() => void>(),
}));

vi.mock('@/advclient', () => ({
  Game: {
    setGameState: mocks.setGameState,
  },
}));

vi.mock('@/clientScripts/clientOverlayInterface', () => ({
  ClientOverlayInterface: {
    hide: mocks.hideOverlay,
  },
}));

describe('conversationHandler', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  afterEach(() => {
    unregisterConversationHandlers();
  });

  test('CONVERSATION_STARTED sets conversation state and hides overlay', () => {
    registerConversationHandlers();

    gameEventBus.emit('CONVERSATION_STARTED', { displayName: 'Pesr' });

    expect(mocks.setGameState).toHaveBeenCalledExactlyOnceWith('conversation');
    expect(mocks.hideOverlay).toHaveBeenCalledOnce();
  });

  test('registering twice does not duplicate handlers', () => {
    registerConversationHandlers();
    registerConversationHandlers();

    gameEventBus.emit('CONVERSATION_STARTED', { displayName: 'Pesr' });

    expect(mocks.setGameState).toHaveBeenCalledOnce();
  });

  test('unregistered handlers are not called', () => {
    registerConversationHandlers();
    unregisterConversationHandlers();

    gameEventBus.emit('CONVERSATION_STARTED', { displayName: 'Pesr' });

    expect(mocks.setGameState).not.toHaveBeenCalled();
  });
});
