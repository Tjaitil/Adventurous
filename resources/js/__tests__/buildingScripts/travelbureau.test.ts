import { describe, expect, beforeEach, vi, test } from 'vitest';
import travelBureauModule from '@/buildingScripts/travelbureau';
import { travelbureauDataLoader } from '@/buildingScripts/buildingLoaders';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';

vi.mock('@/buildingScripts/buildingLoaders', () => ({
  travelbureauDataLoader: {
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

describe('Travel Bureau Building Script - Cache Integration', () => {
  beforeEach(() => {
    vi.clearAllMocks();
    buildingDataPreloader.clearCache();
  });

  describe('Cache Integration', () => {
    test('should skip API call when travelbureau cache is valid', async () => {
      const travelbureauDataLoaderMock = vi.mocked(travelbureauDataLoader);

      travelbureauDataLoaderMock.store_items.mockResolvedValue({
        logs: [],
        data: {
          store_items: [
            {
              name: 'ticket',
              amount: 1,
              store_value: 10,
              store_buy_price: 5,
              required_items: [],
              item_multiplier: 1,
              adjusted_store_value: 0,
              adjusted_difference: 0,
              skill_requirements: [],
              information: '',
            },
          ],
        },
      });

      await buildingDataPreloader.preloadTravelBureau();

      const spy = vi.spyOn(travelbureauDataLoader, 'store_items');

      await travelBureauModule.init();

      expect(spy).not.toHaveBeenCalled();
    });

    test('should call API when travelbureau cache is empty', async () => {
      const travelbureauDataLoaderMock = vi.mocked(travelbureauDataLoader);

      travelbureauDataLoaderMock.store_items.mockResolvedValue({
        logs: [],
        data: {
          store_items: [
            {
              name: 'ticket',
              amount: 1,
              store_value: 10,
              store_buy_price: 5,
              required_items: [],
              item_multiplier: 1,
              adjusted_store_value: 0,
              adjusted_difference: 0,
              skill_requirements: [],
              information: '',
            },
          ],
        },
      });

      await travelBureauModule.init();

      expect(travelbureauDataLoader.store_items).toHaveBeenCalled();
    });
  });
});
