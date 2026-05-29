import { describe, test, expect, beforeEach, vi } from 'vitest';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { gameEventBus } from '@/gameEventsBus';

vi.mock('@/AdvApi', () => ({
  get: vi.fn(),
}));

const mockStoreResponse = (name = 'iron sword') => ({
  data: {
    store_items: [
      {
        name,
        amount: 5,
        store_value: 100,
        store_buy_price: 80,
        adjusted_store_value: 90,
        adjusted_difference: 10,
        item_multiplier: 1,
        required_items: [],
        skill_requirements: [],
        information: '',
      },
    ],
    store_value_modifier_as_percentage: 10,
    is_discount_active: true,
  },
});

describe('BuildingDataPreloader - Core Functionality', () => {
  beforeEach(() => {
    buildingDataPreloader.clearCache();
    vi.clearAllMocks();
  });

  test('should return undefined for non-existent cache', () => {
    const result = buildingDataPreloader.getBuildingCache('bakery');
    expect(result).toBeUndefined();
  });

  test('should clear cache without errors', () => {
    expect(() => {
      buildingDataPreloader.clearCache();
    }).not.toThrow();
  });

  test('should get armory data safely', () => {
    const result = buildingDataPreloader.getArmoryData();
    expect(result).toBeNull();
  });
});

describe('BuildingDataPreloader - Data Preloading', () => {
  beforeEach(() => {
    vi.resetAllMocks();
  });

  test('should invalidate cache when location is changed', () => {
    const spy = vi.spyOn(buildingDataPreloader, 'preloadCrops');

    gameEventBus.emit('CHANGED_LOCATION', { locationName: 'towhar' });

    expect(spy).toHaveBeenCalled();
  });

  test('should invalidate cache when location is changed', () => {
    const spy = vi.spyOn(buildingDataPreloader, 'preloadMine');

    gameEventBus.emit('CHANGED_LOCATION', { locationName: 'golbak' });

    expect(spy).toHaveBeenCalled();
  });
});

describe('BuildingDataPreloader - Smithy preload (Vue page, no Blade view)', () => {
  beforeEach(() => {
    buildingDataPreloader.clearCache();
    vi.clearAllMocks();
  });

  test('preloadSmithy fetches store items from smithyDataLoader', async () => {
    const { smithyDataLoader } = await import('@/buildingScripts/buildingLoaders');
    const spy = vi.spyOn(smithyDataLoader, 'store_items').mockResolvedValue(mockStoreResponse() as any);

    await buildingDataPreloader.preloadSmithy();

    expect(spy).toHaveBeenCalledOnce();
  });

  test('preloadSmithy only calls smithyDataLoader.store_items — not the Blade view route', async () => {
    const { smithyDataLoader } = await import('@/buildingScripts/buildingLoaders');
    const spy = vi.spyOn(smithyDataLoader, 'store_items').mockResolvedValue(mockStoreResponse() as any);

    await buildingDataPreloader.preloadSmithy();

    // Exactly one call — to /smithy/store (via smithyDataLoader), never to /smithy (blade view)
    expect(spy).toHaveBeenCalledOnce();
    expect(spy).toHaveBeenCalledWith(); // called with no arguments
  });

  test('preloadSmithy populates cache with store_items', async () => {
    const { smithyDataLoader } = await import('@/buildingScripts/buildingLoaders');
    vi.spyOn(smithyDataLoader, 'store_items').mockResolvedValue(mockStoreResponse('bronze sword') as any);

    await buildingDataPreloader.preloadSmithy();

    const cached = buildingDataPreloader.getBuildingCache('smithy');
    expect(cached).toBeDefined();
    expect(cached!.store_items[0].name).toBe('bronze sword');
  });

  test('preloadSmithy populates cache with discount fields', async () => {
    const { smithyDataLoader } = await import('@/buildingScripts/buildingLoaders');
    vi.spyOn(smithyDataLoader, 'store_items').mockResolvedValue(mockStoreResponse() as any);

    await buildingDataPreloader.preloadSmithy();

    const cached = buildingDataPreloader.getBuildingCache('smithy');
    expect(cached!.store_value_modifier_as_percentage).toBe(10);
    expect(cached!.is_discount_active).toBe(true);
  });

  test('preloadSmithy does not fetch again when cache is still valid', async () => {
    const { smithyDataLoader } = await import('@/buildingScripts/buildingLoaders');
    const spy = vi.spyOn(smithyDataLoader, 'store_items').mockResolvedValue(mockStoreResponse() as any);

    await buildingDataPreloader.preloadSmithy();
    await buildingDataPreloader.preloadSmithy();

    expect(spy).toHaveBeenCalledOnce();
  });

  test('smithy cache does not have a view property', async () => {
    const { smithyDataLoader } = await import('@/buildingScripts/buildingLoaders');
    vi.spyOn(smithyDataLoader, 'store_items').mockResolvedValue(mockStoreResponse() as any);

    await buildingDataPreloader.preloadSmithy();

    const cached = buildingDataPreloader.getBuildingCache('smithy');
    expect(cached).not.toHaveProperty('view');
  });
});
