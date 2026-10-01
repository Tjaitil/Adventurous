import { describe, expect, beforeEach, vi, test } from 'vitest';
import { itemTitle } from '@/utilities/itemTitle';

vi.mock('@/advclient', () => ({
  Game: { properties: { building: '' } },
}));

vi.mock('@/clientScripts/inventory', () => ({
  itemPrices: { findItem: vi.fn(() => 10) },
}));

describe('itemTitle tooltip visibility', () => {
  beforeEach(() => {
    document.body.innerHTML = `
      <div id="item_tooltip" class="hidden">
        <ul><li></li><li><span id="tooltip_item_price"></span></li></ul>
      </div>
      <div id="item">
        <figure><img /><figcaption><span class="tooltip_item">Bread</span></figcaption></figure>
      </div>`;
    itemTitle.tooltipElement = document.getElementById('item_tooltip');
    itemTitle.currentTitle = null;
  });

  test('show removes hidden class', () => {
    const target = document.querySelector('figure') as HTMLElement;
    itemTitle.show({ target } as unknown as MouseEvent);

    const tooltip = document.getElementById('item_tooltip') as HTMLElement;
    expect(tooltip.classList.contains('hidden')).toBe(false);
    expect(tooltip.classList.contains('invisible')).toBe(false);
  });

  test('hide adds hidden class', () => {
    const tooltip = document.getElementById('item_tooltip') as HTMLElement;
    tooltip.classList.remove('hidden');

    itemTitle.hide();

    expect(tooltip.classList.contains('hidden')).toBe(true);
    expect(itemTitle.currentTitle).toBeNull();
  });
});
