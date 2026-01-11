import { describe, test, expect, beforeEach, vi } from 'vitest';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { gameEventBus } from '@/gameEventsBus';

vi.mock('@/AdvApi', () => ({
  get: vi.fn(),
}));

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
