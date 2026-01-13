import { fireEvent, render, screen, waitFor } from '@testing-library/vue';
import { describe, test, expect, vi, beforeEach, type Mock } from 'vitest';
import '@testing-library/jest-dom';
import ConversationContainer from '@/ui/components/ConversationContainer.vue';
import { createTestingPinia } from '@pinia/testing';
import { CustomFetchApi } from '@/CustomFetchApi';
import { useConversationStore } from '@/ui/stores/ConversationStore';
import { GamePieces } from '@/clientScripts/gamePieces';
import { gameTravel } from '@/clientScripts/gameTravel';
import { PesrSelectLocationResponse } from '@/mocks/responses/conversations/Pesr';
import { openZinsStoreResponse } from '@/mocks/responses/conversations/Zins';
import { loadBuildingCallback } from '@/conversationCallbacks/loadBuilding';

vi.mock('@/CustomFetchApi', () => {
  return {
    CustomFetchApi: {
      post: vi.fn(),
    },
  };
});

vi.mock('@/advclient', () => ({
  Game: {
    setGameState: vi.fn(),
    properties: {
      gameState: 'playing',
    },
  },
}));

vi.mock('@/clientScripts/clientOverlayInterface', () => {
  return {
    ClientOverlayInterface: {
      show: vi.fn(),
      hide: vi.fn(),
    },
  };
});

describe('ConversationContainer.vue', () => {
  beforeEach(() => {
    GamePieces.characters = [
      {
        height: 32,
        id: false,
        type: 'character',
        visible: true,
        width: 32,
        x: 1728.5,
        y: 2145,
        sprite: new Image(),
        drawX: 0,
        drawY: 0,
        diameterDown: 2177,
        diameterLeft: 1728.5,
        diameterRight: 1760.5,
        diameterUp: 0,
        displayName: 'Pesr',
        noCollision: false,
        src: 'pesr.png',
        conversation: true,
      },
      {
        height: 32,
        id: false,
        type: 'character',
        visible: true,
        width: 32,
        x: 1728.5,
        y: 2145,
        sprite: new Image(),
        drawX: 0,
        drawY: 0,
        diameterDown: 2177,
        diameterLeft: 1728.5,
        diameterRight: 1760.5,
        diameterUp: 0,
        displayName: 'Zins',
        noCollision: false,
        src: 'zins.png',
        conversation: true,
      },
      {
        height: 32,
        id: false,
        type: 'character',
        visible: true,
        width: 32,
        x: 1728.5,
        y: 2145,
        sprite: new Image(),
        drawX: 0,
        drawY: 0,
        diameterDown: 2177,
        diameterLeft: 1728.5,
        diameterRight: 1760.5,
        diameterUp: 0,
        displayName: 'Kapys',
        noCollision: false,
        src: 'kapys.png',
        conversation: true,
      },
    ];
  });
  test('renders the component correctly in loading state', () => {
    const { container } = render(ConversationContainer, {
      global: {
        plugins: [createTestingPinia()],
      },
    });

    expect(
      container
        .querySelector('#conversation-container')
        ?.classList.contains('invisible'),
    ).toBe(true);
    expect(screen.getByText('Loading...')).toBeInTheDocument();
  });

  test('calls loadConversation when store.isActive is true', async () => {
    const { container } = render(ConversationContainer, {
      global: {
        plugins: [createTestingPinia({ stubActions: false })],
      },
    });

    const conversationStore = useConversationStore();
    conversationStore.triggerLoadConversation('Pesr');

    (CustomFetchApi.post as Mock).mockResolvedValue({
      data: {
        conversation_segment: {
          index: 'kps',
          options: [
            {
              id: 1,
              text: 'Option 1',
              person: 'Test Character',
              container: 'A',
            },
          ],
        },
      },
    });

    await waitFor(() => {
      expect(
        container
          .querySelector('#conversation-container')
          ?.classList.contains('invisible'),
      ).toBe(false);

      expect(screen.getByText('Option 1')).toBeInTheDocument();
    });
  });

  test('calls conversation with multiple options hides button', async () => {
    const { container } = render(ConversationContainer, {
      global: {
        plugins: [createTestingPinia({ stubActions: false })],
      },
    });

    const conversationStore = useConversationStore();
    conversationStore.triggerLoadConversation('Kapys');

    (CustomFetchApi.post as Mock).mockResolvedValue({
      data: {
        conversation_segment: {
          index: 'kps',
          options: [
            {
              id: 1,
              text: 'Option 1',
              person: 'Test Character',
              container: 'A',
            },
            {
              id: 2,
              text: 'Option 2',
              person: 'Test Character',
              container: 'A',
            },
          ],
        },
      },
    });

    await waitFor(() => {
      expect(
        container
          .querySelector('#conversation-container')
          ?.classList.contains('invisible'),
      ).toBe(false);

      expect(screen.getByText('Option 1')).toBeInTheDocument();
      expect(screen.getByText('Option 2')).toBeInTheDocument();
      expect(screen.queryByText('Click here to continue')).toBeNull();
    });
  });

  test('ends conversation correctly', async () => {
    const { container } = render(ConversationContainer, {
      global: {
        plugins: [createTestingPinia({ stubActions: false })],
      },
    });

    const closeButton = screen.getByAltText('exit symbol');

    await fireEvent.click(closeButton);

    expect(
      container
        .querySelector('#conversation-container')
        ?.classList.contains('invisible'),
    ).toBe(true);
  });
});

describe('Pesr Conversation test', () => {
  beforeEach(() => {
    GamePieces.characters = [
      {
        height: 32,
        id: false,
        type: 'character',
        visible: true,
        width: 32,
        x: 1728.5,
        y: 2145,
        sprite: new Image(),
        drawX: 0,
        drawY: 0,
        diameterDown: 2177,
        diameterLeft: 1728.5,
        diameterRight: 1760.5,
        diameterUp: 0,
        displayName: 'Pesr',
        noCollision: false,
        src: 'pesr.png',
        conversation: true,
      },
    ];
  });
  test.for([
    'Golbak',
    'Khanz',
    'Krasnur',
    'Tasnobil',
    'Fagna',
    'Snerpiir',
    'Cruendo',
    'Ter',
    'Towhar',
  ])('GameTravelCallback with Pesr is invoked when pressing %s', async text => {
    vi.mock('@/clientScripts/gameTravel', () => {
      return {
        gameTravel: {
          travel: vi.fn(),
        },
      };
    });

    vi.mock('@/clientScripts/pause', () => {
      return {
        pauseManager: {
          resumeGame: vi.fn(),
        },
      };
    });

    const { container } = render(ConversationContainer, {
      global: {
        plugins: [createTestingPinia({ stubActions: false })],
      },
    });

    const conversationStore = useConversationStore();
    conversationStore.triggerLoadConversation('Pesr');

    (CustomFetchApi.post as Mock).mockResolvedValue(PesrSelectLocationResponse);
    await waitFor(() => {
      expect(screen.getByText(text)).toBeInTheDocument();
    }).then(async () => {
      expect(
        container
          .querySelector('#conversation-container')
          ?.classList.contains('invisible'),
      ).toBe(false);

      await fireEvent.click(screen.getByText(text));
      expect(gameTravel.travel).toHaveBeenCalledWith(text, 'Pesr');
      expect(gameTravel.travel).toHaveBeenCalledTimes(1);
      vi.resetAllMocks();
    });
  });
});

describe('Zins Conversation test', () => {
  beforeEach(() => {
    GamePieces.characters = [
      {
        height: 32,
        id: false,
        type: 'character',
        visible: true,
        width: 32,
        x: 1728.5,
        y: 2145,
        sprite: new Image(),
        drawX: 0,
        drawY: 0,
        diameterDown: 2177,
        diameterLeft: 1728.5,
        diameterRight: 1760.5,
        diameterUp: 0,
        displayName: 'Zins',
        noCollision: false,
        src: 'zins.png',
        conversation: true,
      },
    ];
  });
  test('LoadZinsStoreCallback with Zins is invoked', async () => {
    vi.mock('@/conversationCallbacks/loadBuilding', () => {
      return {
        loadBuildingCallback: vi.fn(),
      };
    });
    const { container } = render(ConversationContainer, {
      global: {
        plugins: [createTestingPinia({ stubActions: false })],
      },
    });
    const conversationStore = useConversationStore();

    conversationStore.triggerLoadConversation('Zins');

    (CustomFetchApi.post as Mock).mockResolvedValue(openZinsStoreResponse);
    await waitFor(() => {
      expect(
        screen.getByText('Great! I will buy them from you.'),
      ).toBeInTheDocument();
    }).then(async () => {
      expect(
        container
          .querySelector('#conversation-container')
          ?.classList.contains('invisible'),
      ).toBe(false);

      await fireEvent.click(screen.getByText('Click here to continue'));
      expect(loadBuildingCallback).toHaveBeenCalledTimes(1);
      expect(loadBuildingCallback).toHaveBeenCalledWith('zinsstore');
    });
  });
});
