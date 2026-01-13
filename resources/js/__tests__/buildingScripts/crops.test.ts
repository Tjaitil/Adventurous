import { describe, expect, beforeEach, vi, test } from 'vitest';
import { cropsDataLoader } from '@/buildingScripts/buildingLoaders';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import CropsModule from '@/buildingScripts/crops';

vi.mock('@/buildingScripts/buildingLoaders', () => ({
  cropsDataLoader: {
    countdown: vi.fn(),
    action_items: vi.fn(),
  },
}));

vi.mock('@/ItemSelector', () => ({
  ItemSelector: {
    setup: vi.fn(),
  },
}));

describe('Crops Building Script - Cache Integration', () => {
  beforeEach(() => {
    document.body.innerHTML = `
      <input id="workforce_amount" />
      <button id="cancel-action"></button>
      <button id="do-action"></button>
      <button id="finish-action"></button>
      <div id="info-action-element"></div>
      <div id="time"></div>
      <input id="selected-action-type" />
      <button id="seed_generator_action"></button>
      <div id="data_container_avail_workforce"></div>
      <div id="data_form"></div>
      <div id="data"></div>
      <input name="level" />
      <input name="seeds" />
      <input name="experience" />
      <input name="location" />
      <div id="selected_item"></div>
    `;
    vi.clearAllMocks();
    buildingDataPreloader.clearCache();
  });

  test('should skip API call when crops cache is valid', async () => {
    const mockedCropsDataLoader = vi.mocked(cropsDataLoader);

    mockedCropsDataLoader.countdown.mockResolvedValue({
      crop_finishes_at: null,
      crop_type: null,
    });

    mockedCropsDataLoader.action_items.mockResolvedValue({
      workforce: {
        avail_workforce: 0,
      },
      crops: [],
      minerals: [],
    });

    await buildingDataPreloader.preloadCrops();

    const spy = vi.spyOn(mockedCropsDataLoader, 'action_items');

    new CropsModule().init();

    expect(spy).not.toHaveBeenCalled();
  });

  test('should call API and cache result when crops cache is empty', () => {
    const mockedCropsDataLoader = vi.mocked(cropsDataLoader);
    mockedCropsDataLoader.action_items.mockResolvedValue({
      workforce: {
        avail_workforce: 0,
      },
      crops: [],
      minerals: [],
    });

    new CropsModule().init();

    expect(mockedCropsDataLoader.action_items).toHaveBeenCalled();
  });
});
