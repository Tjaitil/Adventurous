import { describe, test, expect, beforeEach, vi } from 'vitest';
import { zinsStoreDataLoader } from '@/buildingScripts/buildingLoaders';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import zinsStoreModule from '@/buildingScripts/zinsstore';

vi.mock('@/buildingScripts/buildingLoaders', () => ({
  zinsStoreDataLoader: {
    store_items: vi.fn(),
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

describe('ZinsStore Building Script - Cache Integration', () => {
  beforeEach(() => {
    vi.clearAllMocks();
    buildingDataPreloader.clearCache();
  });

  test('should skip API call when zinsstore cache is valid', async () => {
    const mockedZinsStoreDataLoader = vi.mocked(zinsStoreDataLoader);
    mockedZinsStoreDataLoader.store_items.mockResolvedValue({
      logs: [],
      data: {
        store_items: [
          {
            name: 'new_bow',
            amount: 1,
            store_value: 0,
            store_buy_price: 0,
            required_items: [],
            item_multiplier: 0,
            adjusted_store_value: 0,
            adjusted_difference: 0,
            skill_requirements: [],
            information: '',
          },
        ],
      },
    });

    await buildingDataPreloader.preloadZinsStore();

    const spy = vi.spyOn(mockedZinsStoreDataLoader, 'store_items');

    await zinsStoreModule.init();

    expect(spy).not.toHaveBeenCalled();
  });

  test('should call API and cache result when zinsstore cache is empty', async () => {
    const mockedZinsStoreDataLoader = vi.mocked(zinsStoreDataLoader);
    mockedZinsStoreDataLoader.store_items.mockResolvedValue({
      logs: [],
      data: {
        store_items: [
          {
            name: 'test_bow',
            amount: 1,
            store_value: 0,
            store_buy_price: 0,
            required_items: [],
            item_multiplier: 0,
            adjusted_store_value: 0,
            adjusted_difference: 0,
            skill_requirements: [],
            information: '',
          },
        ],
      },
    });

    await buildingDataPreloader.preloadZinsStore();

    expect(mockedZinsStoreDataLoader.store_items).toHaveBeenCalled();
  });
});
