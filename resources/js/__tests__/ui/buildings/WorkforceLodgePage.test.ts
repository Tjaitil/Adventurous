import { beforeEach, vi, describe, expect, it } from 'vitest';
import { type Mock } from 'vitest';
import { render, screen, waitFor, fireEvent } from '@testing-library/vue';
import { createPinia } from 'pinia';
import '@testing-library/jest-dom/vitest';
import WorkforceLodgePage from '@/ui/buildings/WorkforceLodgePage.vue';
import { i18n } from '@/ui/main';
import { useInventoryStore } from '@/ui/stores/InventoryStore';
import type { WorkforceLodgeDataResponse } from '@/types/WorkforceLodge';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { AdvApi } from '@/AdvApi';
import { CustomFetchApi } from '@/CustomFetchApi';

vi.mock(import('@/AdvApi'), () => ({
  AdvApi: {
    get: vi.fn(),
    post: vi.fn(),
  },
}));

vi.mock(import('@/CustomFetchApi'), () => ({
  CustomFetchApi: {
    post: vi.fn(),
  },
}));

const renderPage = () =>
  render(WorkforceLodgePage, {
    global: {
      plugins: [createPinia(), i18n],
    },
  });

const lodgeResponse: WorkforceLodgeDataResponse = {
  data: {
    farmer: {
      skill: 'farmer',
      efficiency_level: 1,
      efficiency_upgrade_price: 100,
      workforce_total: 3,
      avail_workforce: 2,
      max_workers: 5,
      can_recruit: 2,
      locations: [
        { name: 'Towhar', amount: 1 },
        { name: 'Krasnur', amount: 0 },
      ],
    },
    miner: {
      skill: 'miner',
      efficiency_level: 2,
      efficiency_upgrade_price: 250,
      workforce_total: 4,
      avail_workforce: 4,
      max_workers: 4,
      can_recruit: 0,
      locations: [
        { name: 'Golbak', amount: 0 },
        { name: 'Snerpiir', amount: 0 },
      ],
    },
  },
};

describe('workforceLodgePage.vue', () => {
  beforeEach(() => {
    buildingDataPreloader.clearCache();
    vi.clearAllMocks();
  });

  it('renders both workforces', async () => {
    vi.mocked(AdvApi.get).mockResolvedValue(lodgeResponse);
    renderPage();

    await waitFor(() => {
      expect(screen.getByText('Farmer workforce')).toBeInTheDocument();
    });

    expect(screen.getByText('Miner workforce')).toBeInTheDocument();
    expect(screen.getByText('Towhar workforce')).toBeInTheDocument();
    expect(screen.getByText('Golbak workforce')).toBeInTheDocument();
    expect(screen.getByText('3 / 5')).toBeInTheDocument();
    expect(screen.getByText('4 / 4')).toBeInTheDocument();
  });

  it('shows upgrade cost of the selected skill', async () => {
    vi.mocked(AdvApi.get).mockResolvedValue(lodgeResponse);
    renderPage();

    await waitFor(() => {
      expect(screen.getByText('100')).toBeInTheDocument();
    });

    await fireEvent.click(screen.getByRole('radio', { name: 'Miner' }));

    expect(screen.getByText('250')).toBeInTheDocument();
  });

  it('upgrades efficiency for selected skill and updates state', async () => {
    vi.mocked(AdvApi.get).mockResolvedValue(lodgeResponse);
    (CustomFetchApi.post as Mock).mockResolvedValue({
      data: {
        logs: [],
        data: { efficiency_level: 2, new_efficiency_price: 300 },
      },
    });
    renderPage();

    await waitFor(() => {
      expect(screen.getByText('100')).toBeInTheDocument();
    });

    await fireEvent.click(
      screen.getByRole('button', { name: 'Upgrade efficiency' }),
    );

    await waitFor(() => {
      expect(screen.getByText('300')).toBeInTheDocument();
    });

    expect(CustomFetchApi.post).toHaveBeenCalledWith(
      '/workforcelodge/efficiency/upgrade',
      { skill: 'farmer' },
    );
    expect(useInventoryStore().shouldUpdateInventory).toBe(true);
  });
});
