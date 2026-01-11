import { describe, it, expect, beforeEach, vi } from 'vitest';
import { archeryShopDataLoader } from '@/buildingScripts/buildingLoaders';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import archeryShopModule from '@/buildingScripts/archeryshop';

vi.mock('@/buildingScripts/buildingLoaders', () => ({
  archeryShopDataLoader: {
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

describe('ArcheryShop Building Script - Cache Integration', () => {
  beforeEach(() => {
    vi.clearAllMocks();
    buildingDataPreloader.clearCache();
  });

  it('should skip API call when archeryshop cache is valid', async () => {
    const mockedArcheryShopDataLoader = vi.mocked(archeryShopDataLoader);
    mockedArcheryShopDataLoader.store_items.mockResolvedValue({
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

    await buildingDataPreloader.preloadArcheryShop();

    const spy = vi.spyOn(mockedArcheryShopDataLoader, 'store_items');

    await archeryShopModule.init();

    expect(spy).not.toHaveBeenCalled();
  });

  it('should call API and cache result when archeryshop cache is empty', async () => {
    const mockedArcheryShopDataLoader = vi.mocked(archeryShopDataLoader);
    mockedArcheryShopDataLoader.store_items.mockResolvedValue({
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

    await buildingDataPreloader.preloadArcheryShop();

    expect(mockedArcheryShopDataLoader.store_items).toHaveBeenCalled();
  });
});
