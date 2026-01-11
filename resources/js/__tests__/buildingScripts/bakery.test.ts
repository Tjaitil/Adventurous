import { describe, it, expect, beforeEach, vi } from 'vitest';
import { bakeryDataLoader } from '@/buildingScripts/buildingLoaders';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import bakeryModule from '@/buildingScripts/bakery';

vi.mock('@/buildingScripts/buildingLoaders', () => ({
  bakeryDataLoader: {
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

describe('Bakery Building Script - Cache Integration', () => {
  beforeEach(() => {
    vi.clearAllMocks();
    buildingDataPreloader.clearCache();
  });

  it('should skip API call when bakery cache is valid', async () => {
    const mockedBakeryDataLoader = vi.mocked(bakeryDataLoader);
    mockedBakeryDataLoader.store_items.mockResolvedValue({
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

    await buildingDataPreloader.preloadBakery();

    const spy = vi.spyOn(mockedBakeryDataLoader, 'store_items');

    await bakeryModule.init();

    expect(spy).not.toHaveBeenCalled();
  });

  it('should call API and cache result when bakery cache is empty', async () => {
    const mockedBakeryDataLoader = vi.mocked(bakeryDataLoader);
    mockedBakeryDataLoader.store_items.mockResolvedValue({
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

    await buildingDataPreloader.preloadBakery();

    expect(mockedBakeryDataLoader.store_items).toHaveBeenCalled();
  });
});
