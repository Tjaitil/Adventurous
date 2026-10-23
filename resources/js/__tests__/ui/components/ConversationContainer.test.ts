import { fireEvent, render, screen, waitFor } from '@testing-library/vue';
import { describe, test, expect, vi, type Mock } from 'vitest';
import '@testing-library/jest-dom/vitest';
import ConversationContainer from '@/ui/components/ConversationContainer.vue';
import { createTestingPinia } from '@pinia/testing';
import { CustomFetchApi } from '@/CustomFetchApi';
import { useConversationStore } from '@/ui/stores/ConversationStore';
import { gameEventBus } from '@/gameEventsBus';
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

vi.mock('@/conversationCallbacks/loadBuilding', () => {
  return {
    loadBuildingCallback: vi.fn(),
  };
});

describe('ConversationContainer.vue', () => {
  test('renders the component correctly in loading state', () => {
    const { container } = render(ConversationContainer, {
      global: {
        plugins: [createTestingPinia({ createSpy: vi.fn })],
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
        plugins: [createTestingPinia({ stubActions: false, createSpy: vi.fn })],
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

  test('CONVERSATION_STARTED event loads the conversation', async () => {
    const { container } = render(ConversationContainer, {
      global: {
        plugins: [createTestingPinia({ stubActions: false, createSpy: vi.fn })],
      },
    });

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

    gameEventBus.emit('CONVERSATION_STARTED', { displayName: 'Pesr' });

    await waitFor(() => {
      expect(useConversationStore().currentPerson).toBe('Pesr');
      expect(
        container
          .querySelector('#conversation-container')
          ?.classList.contains('invisible'),
      ).toBe(false);
      expect(screen.getByText('Option 1')).toBeInTheDocument();
    });
    expect((CustomFetchApi.post as Mock).mock.calls[0]).toEqual([
      'conversation/next',
      { person: 'Pesr', is_starting: true },
    ]);
  });

  test('CONVERSATION_END_REQUESTED event ends the conversation', async () => {
    const { container } = render(ConversationContainer, {
      global: {
        plugins: [createTestingPinia({ stubActions: false, createSpy: vi.fn })],
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
      expect(screen.getByText('Option 1')).toBeInTheDocument();
    });

    gameEventBus.emit('CONVERSATION_END_REQUESTED', undefined);

    await waitFor(() => {
      expect(conversationStore.isActive).toBe(false);
      expect(
        container
          .querySelector('#conversation-container')
          ?.classList.contains('invisible'),
      ).toBe(true);
    });
  });

  test('calls conversation with multiple options hides button', async () => {
    const { container } = render(ConversationContainer, {
      global: {
        plugins: [createTestingPinia({ stubActions: false, createSpy: vi.fn })],
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
        plugins: [createTestingPinia({ stubActions: false, createSpy: vi.fn })],
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
    const { container } = render(ConversationContainer, {
      global: {
        plugins: [createTestingPinia({ stubActions: false, createSpy: vi.fn })],
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
  test('LoadZinsStoreCallback with Zins is invoked', async () => {
    const { container } = render(ConversationContainer, {
      global: {
        plugins: [createTestingPinia({ stubActions: false, createSpy: vi.fn })],
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
