import { mount, flushPromises } from '@vue/test-utils';
import { describe, test, expect, beforeEach, afterEach, vi } from 'vitest';
import { createPinia } from 'pinia';
import { i18n } from '@/ui/main';
import MinerPage from '@/ui/buildings/MinerPage.vue';
import { mineDataLoader } from '@/buildingScripts/buildingLoaders';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { AdvApi } from '@/AdvApi';

vi.mock('@/buildingScripts/buildingLoaders', () => ({
  mineDataLoader: {
    countdown: vi.fn(),
    action_items: vi.fn(),
  },
}));

vi.mock('@/AdvApi', () => ({
  AdvApi: {
    post: vi.fn(),
  },
}));

vi.mock('@/clientScripts/hunger', () => ({
  updateHunger: vi.fn(),
}));

const mockedMineDataLoader = vi.mocked(mineDataLoader);
const mockedAdvApi = vi.mocked(AdvApi);

const ironOre = {
  mineral_type: 'ore',
  mineral_ore: 'iron ore',
  miner_level: 1,
  experience: 10,
  time: 100,
  min_per_period: 1,
  max_per_period: 5,
  permit_cost: 2,
  location: 'golbak',
};

const mountMinerPage = () =>
  mount(MinerPage, {
    global: { plugins: [createPinia(), i18n] },
  });

beforeEach(() => {
  vi.clearAllMocks();
  buildingDataPreloader.clearCache();
  mockedMineDataLoader.countdown.mockResolvedValue({
    mining_finishes_at: null,
    mineral_ore: null,
  });
  mockedMineDataLoader.action_items.mockResolvedValue({
    workforce: { avail_workforce: 5, efficiency_level: 10 },
    crops: [],
    minerals: [ironOre],
    permits: 4,
  });
});

describe('MinerPage', () => {
  test('fetches mine data on mount and renders the picker and permits', async () => {
    const wrapper = mountMinerPage();
    await flushPromises();

    expect(mockedMineDataLoader.action_items).toHaveBeenCalled();
    expect(wrapper.findAll('input[type="radio"]').length).toBe(1);
    expect(wrapper.text()).toContain('No miners at work');
    expect(wrapper.text()).toContain('4');
  });

  test('starts mining when a mineral and workforce are selected', async () => {
    mockedAdvApi.post.mockResolvedValue({
      data: { avail_workforce: 3, new_permits: 2, new_hunger: 80 },
      logs: [],
    });

    const wrapper = mountMinerPage();
    await flushPromises();

    await wrapper.find('input[type="radio"]').setValue();
    await wrapper.find('input[type="number"]').setValue(2);

    const mineButton = wrapper
      .findAll('button')
      .find(button => button.text() === 'Mine');
    await mineButton?.trigger('click');
    await flushPromises();

    expect(mockedAdvApi.post).toHaveBeenCalledWith('/mine/start', {
      mineral_ore: 'iron ore',
      workforce_amount: 2,
    });
  });

  test('does not start mining without a selected mineral or workforce', async () => {
    const wrapper = mountMinerPage();
    await flushPromises();

    const mineButton = wrapper
      .findAll('button')
      .find(button => button.text() === 'Mine');

    expect(mineButton?.attributes('disabled')).toBeDefined();
  });

  describe('with an active mining cycle', () => {
    beforeEach(() => {
      vi.useFakeTimers();
    });

    afterEach(() => {
      vi.useRealTimers();
    });

    test('shows the cancel button and cancels mining', async () => {
      mockedMineDataLoader.countdown.mockResolvedValue({
        mining_finishes_at: Date.now() / 1000 + 60,
        mineral_ore: 'iron ore',
      });
      mockedAdvApi.post.mockResolvedValue({
        data: { avail_workforce: 5, new_hunger: 80 },
        logs: [],
      });

      const wrapper = mountMinerPage();
      await flushPromises();
      await vi.advanceTimersByTimeAsync(1000);

      expect(wrapper.text()).toContain('Cancel mining');

      const cancelButton = wrapper
        .findAll('button')
        .find(button => button.text() === 'Cancel mining');
      await cancelButton?.trigger('click');
      await flushPromises();

      expect(mockedAdvApi.post).toHaveBeenCalledWith('/mine/end', {
        is_cancelling: true,
      });
    });

    test('shows the fetch button once finished and fetches minerals', async () => {
      mockedMineDataLoader.countdown.mockResolvedValue({
        mining_finishes_at: Date.now() / 1000 - 60,
        mineral_ore: 'iron ore',
      });
      mockedAdvApi.post.mockResolvedValue({
        data: { avail_workforce: 5, new_hunger: 80 },
        logs: [],
      });

      const wrapper = mountMinerPage();
      await flushPromises();
      await vi.advanceTimersByTimeAsync(1000);

      const finishButton = wrapper
        .findAll('button')
        .find(button => button.text() === 'Fetch minerals');
      expect(finishButton).toBeDefined();

      await finishButton?.trigger('click');
      await flushPromises();

      expect(mockedAdvApi.post).toHaveBeenCalledWith('/mine/end', {
        is_cancelling: false,
      });
    });
  });
});
