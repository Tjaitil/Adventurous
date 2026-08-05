import { describe, test, expect, beforeEach, vi, type Mock } from 'vitest';
import { render, screen, waitFor, fireEvent } from '@testing-library/vue';
import { createPinia } from 'pinia';
import '@testing-library/jest-dom';
import StockpilePage from '@/ui/buildings/StockpilePage.vue';
import { i18n } from '@/ui/main';
import { useInventoryStore } from '@/ui/stores/InventoryStore';
import type { StockpileDataResponse } from '@/types/Stockpile';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { AdvApi } from '@/AdvApi';
import { CustomFetchApi } from '@/CustomFetchApi';
import { mockPopoverApi } from '@/__tests__/mocks/popover';

vi.mock('@/AdvApi', () => ({
  AdvApi: {
    get: vi.fn(),
    post: vi.fn(),
  },
}));

vi.mock('@/CustomFetchApi', () => ({
  CustomFetchApi: {
    post: vi.fn(),
  },
}));

const renderStockpilePage = () =>
  render(StockpilePage, {
    global: {
      plugins: [createPinia(), i18n],
    },
  });

const stockpileResponse: StockpileDataResponse = {
  data: {
    items: [{ item: 'iron ore', amount: 12 }],
  },
  meta: {
    max_slots: 60,
  },
};

describe('StockpilePage.vue', () => {
  beforeEach(() => {
    mockPopoverApi();
    buildingDataPreloader.clearCache();
    vi.clearAllMocks();
  });

  test('renders stockpile items and item slots count', async () => {
    vi.mocked(AdvApi.get).mockResolvedValue(stockpileResponse);
    renderStockpilePage();

    await waitFor(() => {
      expect(screen.getByText('Item slots: 1 / 60')).toBeInTheDocument();
    });

    expect(screen.getByText('Iron Ore')).toBeInTheDocument();
  });

  test('sends withdraw all payload from stockpile menu and refreshes state', async () => {
    vi.mocked(AdvApi.get).mockResolvedValue(stockpileResponse);
    (CustomFetchApi.post as Mock).mockResolvedValue({
      data: {
        data: { items: [] },
        meta: { max_slots: 60 },
      },
    });

    renderStockpilePage();

    await waitFor(() => {
      expect(screen.getByText('Iron Ore')).toBeInTheDocument();
    });

    const stockpileItemButton = screen.getByRole('button');
    await fireEvent.click(stockpileItemButton);
    await fireEvent.click(screen.getByText('Withdraw all'));

    expect(CustomFetchApi.post).toHaveBeenCalledWith('/stockpile/update', {
      item: 'iron ore',
      amount: 12,
      insert: false,
    });

    const inventoryStore = useInventoryStore();
    expect(inventoryStore.shouldUpdateInventory).toBe(true);
  });
});
