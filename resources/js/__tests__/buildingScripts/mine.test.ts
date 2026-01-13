import { describe, expect, beforeEach, vi, test } from 'vitest';
import { mineDataLoader } from '@/buildingScripts/buildingLoaders';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import MineModule from '@/buildingScripts/mine';

vi.mock('@/buildingScripts/buildingLoaders', () => ({
  mineDataLoader: {
    countdown: vi.fn(),
    action_items: vi.fn(),
  },
}));

vi.mock('@/ItemSelector', () => ({
  ItemSelector: {
    setup: vi.fn(),
  },
}));

describe('Mine Building Script - Cache Integration', () => {
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

  test('should skip API call when mine cache is valid', async () => {
    const mockedMineDataLoader = vi.mocked(mineDataLoader);

    mockedMineDataLoader.countdown.mockResolvedValue({
      mining_finishes_at: null,
      mineral_ore: null,
    });

    mockedMineDataLoader.action_items.mockResolvedValue({
      workforce: {
        avail_workforce: 0,
      },
      crops: [],
      minerals: [],
    });

    await buildingDataPreloader.preloadMine();

    const spy = vi.spyOn(mockedMineDataLoader, 'action_items');

    new MineModule().init();

    expect(spy).not.toHaveBeenCalled();
  });

  test('should call API and cache result when mine cache is empty', () => {
    const mockedMineDataLoader = vi.mocked(mineDataLoader);
    mockedMineDataLoader.action_items.mockResolvedValue({
      workforce: {
        avail_workforce: 0,
      },
      crops: [],
      minerals: [],
    });

    new MineModule().init();

    expect(mockedMineDataLoader.action_items).toHaveBeenCalled();
  });
});
