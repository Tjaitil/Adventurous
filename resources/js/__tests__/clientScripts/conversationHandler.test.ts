import { handleConversationCallback } from '@/clientScripts/conversationHandler';
import { beforeEach, describe, expect, test, vi } from 'vitest';

const mocks = vi.hoisted(() => ({
  reportError: vi.fn<(error: unknown) => void>(),
  travel: vi.fn<(destination: string, person: string) => boolean>(),
  loadBuilding: vi.fn<(building: string) => void>(),
  addMessage: vi.fn<(message: string, log: boolean) => void>(),
}));

vi.mock('@/ui/errorReporting', () => ({
  reportError: mocks.reportError,
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

describe('handleConversationCallback', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  test('GameTravelCallback travels to the location', () => {
    handleConversationCallback(
      'GameTravelCallback',
      { location: 'Golbak' },
      'Pesr',
    );

    expect(mocks.travel).toHaveBeenCalledExactlyOnceWith('Golbak', 'Pesr');
  });

  test.for([null, {}, { location: 1 }])(
    'GameTravelCallback with invalid values %j logs an error',
    values => {
      handleConversationCallback('GameTravelCallback', values, 'Pesr');

      expect(mocks.travel).not.toHaveBeenCalled();
      expect(mocks.addMessage).toHaveBeenCalledExactlyOnceWith(
        'An error occured',
        true,
      );
    },
  );

  test('LoadZinsStoreCallback loads the zins store', () => {
    handleConversationCallback('LoadZinsStoreCallback', null, 'Zins');

    expect(mocks.loadBuilding).toHaveBeenCalledExactlyOnceWith('zinsstore');
  });

  test.for(['Unknown', 'toString', '__proto__'])(
    'unknown callback %s is reported',
    callback => {
      handleConversationCallback(callback, null, 'Pesr');

      expect(mocks.reportError).toHaveBeenCalledOnce();
      expect(mocks.travel).not.toHaveBeenCalled();
      expect(mocks.loadBuilding).not.toHaveBeenCalled();
    },
  );
});
