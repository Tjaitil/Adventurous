import { describe, it, expect, beforeEach, vi } from 'vitest';
import {
  bakeryDataLoader,
  cropsDataLoader,
} from '@/buildingScripts/buildingLoaders';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import bakeryModule from '@/buildingScripts/bakery';

vi.mock('@/buildingScripts/buildingLoaders', () => ({
  bakeryDataLoader: {
    store_items: vi.fn(),
  },
}));

describe('Bakery Building Script - Cache Integration', () => {
  beforeEach(() => {
    vi.clearAllMocks();
    buildingDataPreloader.clearCache();
  });

  it('should skip API call when bakery cache is valid', async () => {
    const mockedCropsDataLoader = vi.mocked(cropsDataLoader);

    mockedCropsDataLoader.countdown.mockResolvedValue({
      crop_finishes_at: null,
      crop_type: null,
    });

    await buildingDataPreloader.preloadBakery();

    const spy = vi.spyOn(mockedCropsDataLoader, 'store_items');

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
