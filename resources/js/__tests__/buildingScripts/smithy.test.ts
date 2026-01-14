import { describe, expect, beforeEach, vi, test } from 'vitest';
import smithyModule from '@/buildingScripts/smithy';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { AdvApi } from '@/AdvApi';

vi.mock('@/AdvApi', () => ({
  AdvApi: {
    get: vi.fn(),
    post: vi.fn(),
  },
}));

vi.mock('@/gameEventsBus', () => ({
  gameEventBus: {
    subscribe: vi.fn(),
    emit: vi.fn(),
  },
}));

vi.mock('@/utilities/storeContainer', () => ({
  default: {
    init: vi.fn(),
    addSelectTrade: vi.fn(),
    addSelectedItemButtonEvent: vi.fn(),
    setStoreItems: vi.fn(),
    getSelectedTrade: vi.fn(),
    checkItemTooltip: vi.fn(),
  },
}));

describe('Smithy Building Script - Cache Integration', () => {
  beforeEach(() => {
    vi.clearAllMocks();
    buildingDataPreloader.clearCache();
  });

  test('should skip API call when smithy cache is valid', async () => {
    const mockedAdvApi = vi.mocked(AdvApi);
    mockedAdvApi.get.mockResolvedValue({
      data: { store_items: [{ item: 'new_sword', amount: 1 }] },
    });

    await buildingDataPreloader.preloadSmithy();

    const spy = vi.spyOn(mockedAdvApi, 'get');

    await smithyModule.init();

    expect(spy).not.toHaveBeenCalled();
  });

  test('should call API and cache result when smithy cache is empty', async () => {
    const mockedAdvApi = vi.mocked(AdvApi);
    mockedAdvApi.get.mockResolvedValue({
      data: { store_items: [{ item: 'test_sword', amount: 1 }] },
    });

    await smithyModule.init();

    // Verify API was called
    expect(mockedAdvApi.get).toHaveBeenCalledWith('/smithy/store');
  });
});
