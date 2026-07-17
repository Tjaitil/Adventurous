import { mount, flushPromises } from '@vue/test-utils';
import { describe, test, expect, beforeEach, afterEach, vi } from 'vitest';
import { createPinia } from 'pinia';
import { i18n } from '@/ui/main';
import CropsPage from '@/ui/buildings/CropsPage.vue';
import { cropsDataLoader } from '@/buildingScripts/buildingLoaders';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { AdvApi } from '@/AdvApi';
import { useInventoryStore } from '@/ui/stores/InventoryStore';

vi.mock('@/buildingScripts/buildingLoaders', () => ({
  cropsDataLoader: {
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

const mockedCropsDataLoader = vi.mocked(cropsDataLoader);
const mockedAdvApi = vi.mocked(AdvApi);

const wheat = {
  crop_type: 'wheat',
  farmer_level: 1,
  time: 100,
  experience: 10,
  seed_required: 2,
  seed: 'wheat seed',
  location: 'towhar',
  min_crop_count: 1,
  max_crop_count: 5,
};

const mountCropsPage = () =>
  mount(CropsPage, {
    global: { plugins: [createPinia(), i18n] },
  });

beforeEach(() => {
  vi.clearAllMocks();
  buildingDataPreloader.clearCache();
  mockedCropsDataLoader.countdown.mockResolvedValue({
    crop_finishes_at: null,
    crop_type: null,
  });
  mockedCropsDataLoader.action_items.mockResolvedValue({
    workforce: { avail_workforce: 5, efficiency_level: 10 },
    crops: [wheat],
    minerals: [],
  });
});

describe('CropsPage', () => {
  test('fetches crop data on mount and renders the picker', async () => {
    const wrapper = mountCropsPage();
    await flushPromises();

    expect(mockedCropsDataLoader.action_items).toHaveBeenCalled();
    expect(wrapper.findAll('input[type="radio"]').length).toBe(1);
    expect(wrapper.text()).toContain('No crops growing');
  });

  test('starts growing when a crop and workforce are selected', async () => {
    mockedAdvApi.post.mockResolvedValue({
      data: { avail_workforce: 3, new_hunger: 80 },
      logs: [],
    });

    const wrapper = mountCropsPage();
    await flushPromises();

    await wrapper.find('input[type="radio"]').setValue();
    await wrapper.find('input[type="number"]').setValue(2);

    const growButton = wrapper
      .findAll('button')
      .find(button => button.text() === 'Grow');
    await growButton?.trigger('click');
    await flushPromises();

    expect(mockedAdvApi.post).toHaveBeenCalledWith('/crops/start', {
      crop_type: 'wheat',
      workforce_amount: 2,
    });
  });

  test('does not start growing without a selected crop or workforce', async () => {
    const wrapper = mountCropsPage();
    await flushPromises();

    const growButton = wrapper
      .findAll('button')
      .find(button => button.text() === 'Grow');

    expect(growButton?.attributes('disabled')).toBeDefined();
  });

  describe('with an active growth cycle', () => {
    beforeEach(() => {
      vi.useFakeTimers();
    });

    afterEach(() => {
      vi.useRealTimers();
    });

    test('shows the cancel button and cancels growth', async () => {
      mockedCropsDataLoader.countdown.mockResolvedValue({
        crop_finishes_at: Date.now() / 1000 + 60,
        crop_type: 'wheat',
      });
      mockedAdvApi.post.mockResolvedValue({
        data: { avail_workforce: 5 },
        logs: [],
      });

      const wrapper = mountCropsPage();
      await flushPromises();
      await vi.advanceTimersByTimeAsync(1000);

      expect(wrapper.text()).toContain('Cancel growing');

      const cancelButton = wrapper
        .findAll('button')
        .find(button => button.text() === 'Cancel growing');
      await cancelButton?.trigger('click');
      await flushPromises();

      expect(mockedAdvApi.post).toHaveBeenCalledWith('/crops/end', {
        is_cancelling: true,
      });
    });

    test('shows the harvest button once finished and harvests', async () => {
      mockedCropsDataLoader.countdown.mockResolvedValue({
        crop_finishes_at: Date.now() / 1000 - 60,
        crop_type: 'wheat',
      });
      mockedAdvApi.post.mockResolvedValue({
        data: { avail_workforce: 5 },
        logs: [],
      });

      const wrapper = mountCropsPage();
      await flushPromises();
      await vi.advanceTimersByTimeAsync(1000);

      const harvestButton = wrapper
        .findAll('button')
        .find(button => button.text() === 'Harvest');
      expect(harvestButton).toBeDefined();

      await harvestButton?.trigger('click');
      await flushPromises();

      expect(mockedAdvApi.post).toHaveBeenCalledWith('/crops/end', {
        is_cancelling: false,
      });
    });

    test('blocks harvesting when the inventory is full', async () => {
      mockedCropsDataLoader.countdown.mockResolvedValue({
        crop_finishes_at: Date.now() / 1000 - 60,
        crop_type: 'wheat',
      });

      const wrapper = mountCropsPage();
      await flushPromises();
      await vi.advanceTimersByTimeAsync(1000);

      const inventoryStore = useInventoryStore();
      inventoryStore.setInventoryItems(Array(18).fill({ item: 'wheat' }));

      const harvestButton = wrapper
        .findAll('button')
        .find(button => button.text() === 'Harvest');
      await harvestButton?.trigger('click');
      await flushPromises();

      expect(mockedAdvApi.post).not.toHaveBeenCalled();
    });
  });

  test('generates seeds from the selected inventory item', async () => {
    mockedAdvApi.post.mockResolvedValue({ data: {}, logs: [] });

    const wrapper = mountCropsPage();
    await flushPromises();

    const inventoryStore = useInventoryStore();
    inventoryStore.addSelectedItem('wheat');
    await wrapper.vm.$nextTick();

    const generateButton = wrapper
      .findAll('button')
      .find(button => button.text() === 'Generate');
    await generateButton?.trigger('click');
    await flushPromises();

    expect(mockedAdvApi.post).toHaveBeenCalledWith('/crops/collect-seeds', {
      item: 'wheat',
      amount: 1,
    });
  });
});
