import { describe, test, expect, beforeEach, vi } from 'vitest';
import { render, screen, fireEvent } from '@testing-library/vue';
import '@testing-library/jest-dom';
import { createPinia, setActivePinia } from 'pinia';
import StoreContainer from '@/ui/components/store/StoreContainer.vue';
import type { StoreItemResource } from '@/types/StoreItemResource';
import { i18n } from '@/ui/main';
import { useInventoryStore } from '@/ui/stores/InventoryStore';
import { useSkillsStore } from '@/ui/stores/SkillsStore';

// ── Fixtures ─────────────────────────────────────────────────────────────────

const makeItem = (overrides: Partial<StoreItemResource> = {}): StoreItemResource => ({
  name: 'iron sword',
  amount: 5,
  store_value: 100,
  store_buy_price: 80,
  adjusted_store_value: 100,
  adjusted_difference: 0,
  item_multiplier: 1,
  required_items: [],
  skill_requirements: [],
  information: '',
  ...overrides,
});

// ── Render helper ─────────────────────────────────────────────────────────────

interface RenderProps {
  storeItems?: StoreItemResource[];
  buttonText?: string;
  showItemInformation?: boolean;
  showAmountInput?: boolean;
  showRequirements?: boolean;
}

const renderStore = (props: RenderProps = {}) => {
  const pinia = createPinia();
  const result = render(StoreContainer, {
    props: {
      storeItems: [makeItem()],
      buttonText: 'Smith',
      ...props,
    },
    global: {
      plugins: [pinia, i18n],
      stubs: {
        BaseItem: {
          template: '<span class="base-item-stub" :data-item="item">{{ item }}</span>',
          props: ['item', 'amount', 'showAmount', 'disableTooltip'],
        },
        BaseIcon: {
          template: '<img class="base-icon-stub" :data-icon="icon" :alt="`${icon} icon`" />',
          props: ['icon'],
        },
        UButton: {
          template: '<button class="u-button-stub" :disabled="disabled" @click="$emit(\'click\')"><slot /></button>',
          props: ['disabled'],
          emits: ['click'],
        },
        UInput: {
          template: '<input class="u-input-stub" :value="modelValue" @change="$emit(\'update:modelValue\', Number($event.target.value))" type="number" min="1" />',
          props: ['modelValue', 'type', 'min', 'id', 'ui'],
          emits: ['update:modelValue'],
        },
      },
    },
  });
  // Return stores so tests can seed inventory / skill data
  const inventoryStore = useInventoryStore(pinia);
  const skillsStore = useSkillsStore(pinia);
  return { ...result, inventoryStore, skillsStore };
};

const clickFirstItem = async () => {
  const item = document.querySelector('.store-container-item')!;
  await fireEvent.click(item);
};

// ── Tests ─────────────────────────────────────────────────────────────────────

describe('StoreContainer.vue', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    vi.clearAllMocks();
  });

  // ── Initial state ──────────────────────────────────────────────────────────

  describe('Initial State', () => {
    test('shows placeholder text when no item is selected', () => {
      renderStore();
      expect(screen.getByText('Select an item in the list')).toBeInTheDocument();
    });

    test('does not show the trade button before an item is selected', () => {
      renderStore();
      expect(screen.queryByText('Smith')).not.toBeInTheDocument();
    });
  });

  // ── Item list ──────────────────────────────────────────────────────────────

  describe('Item List', () => {
    test('renders all supplied items', () => {
      renderStore({
        storeItems: [
          makeItem({ name: 'iron sword' }),
          makeItem({ name: 'steel axe' }),
          makeItem({ name: 'bronze dagger' }),
        ],
      });

      expect(screen.getByText('iron sword')).toBeInTheDocument();
      expect(screen.getByText('steel axe')).toBeInTheDocument();
      expect(screen.getByText('bronze dagger')).toBeInTheDocument();
    });

    test('shows base store_value price', () => {
      renderStore({ storeItems: [makeItem({ store_value: 250 })] });
      expect(screen.getByText('250')).toBeInTheDocument();
    });

    test('shows line-through original + able-color adjusted price when discounted', () => {
      renderStore({
        storeItems: [makeItem({ store_value: 100, adjusted_store_value: 80, adjusted_difference: 20 })],
      });
      expect(screen.getByText('100')).toHaveClass('line-through');
      expect(screen.getByText('80')).toHaveClass('able-color');
    });

    test('shows line-through original + not-able-color adjusted price when worse', () => {
      renderStore({
        storeItems: [makeItem({ store_value: 100, adjusted_store_value: 120, adjusted_difference: -20 })],
      });
      expect(screen.getByText('100')).toHaveClass('line-through');
      expect(screen.getByText('120')).toHaveClass('not-able-color');
    });

    test('shows stock count when amount > -1', () => {
      renderStore({ storeItems: [makeItem({ amount: 7 })] });
      expect(screen.getByText('x 7')).toBeInTheDocument();
    });

    test('does not show stock count when amount is -1', () => {
      renderStore({ storeItems: [makeItem({ amount: -1 })] });
      expect(screen.queryByText(/x -1/)).not.toBeInTheDocument();
    });
  });

  // ── Item selection ─────────────────────────────────────────────────────────

  describe('Item Selection', () => {
    test('clicking an item hides the placeholder', async () => {
      renderStore();
      await clickFirstItem();
      expect(screen.queryByText('Select an item in the list')).not.toBeInTheDocument();
    });

    test('clicking an item shows the trade button with correct text', async () => {
      renderStore({ buttonText: 'Fletch' });
      await clickFirstItem();
      expect(screen.getByText('Fletch')).toBeInTheDocument();
    });

    test('shows item_multiplier badge when multiplier > 1', async () => {
      renderStore({ storeItems: [makeItem({ item_multiplier: 3 })] });
      await clickFirstItem();
      expect(screen.getByText('3')).toBeInTheDocument();
    });

    test('does not show item_multiplier badge when multiplier is 1', async () => {
      renderStore({ storeItems: [makeItem({ item_multiplier: 1 })] });
      await clickFirstItem();
      expect(document.querySelector('.item_amount')).not.toBeInTheDocument();
    });
  });

  // ── Trade emit ─────────────────────────────────────────────────────────────

  describe('Trade Emit', () => {
    test('emits "trade" event with selected item and amount 1 by default', async () => {
      const { emitted } = renderStore({
        storeItems: [makeItem({ name: 'iron sword' })],
        // No requirements so button is enabled
        showRequirements: false,
      });

      await clickFirstItem();
      await fireEvent.click(screen.getByText('Smith'));

      const trades = emitted('trade') as [{ item: StoreItemResource; amount: number }][];
      expect(trades).toHaveLength(1);
      expect(trades[0][0].item.name).toBe('iron sword');
      expect(trades[0][0].amount).toBe(1);
    });
  });

  // ── showAmountInput ────────────────────────────────────────────────────────

  describe('showAmountInput prop', () => {
    test('shows amount label by default', async () => {
      renderStore();
      await clickFirstItem();
      expect(screen.getByText('Select your Amount')).toBeInTheDocument();
    });

    test('hides amount label when showAmountInput=false', async () => {
      renderStore({ showAmountInput: false });
      await clickFirstItem();
      expect(screen.queryByText('Select your Amount')).not.toBeInTheDocument();
    });
  });

  // ── showRequirements ───────────────────────────────────────────────────────

  describe('showRequirements prop', () => {
    test('shows Materials heading when item has required_items', async () => {
      renderStore({
        storeItems: [makeItem({ required_items: [makeItem({ name: 'oak log', amount: 5 })] })],
      });
      await clickFirstItem();
      expect(screen.getByText('Materials')).toBeInTheDocument();
    });

    test('shows Skills heading when item has skill_requirements', async () => {
      renderStore({
        storeItems: [makeItem({ skill_requirements: [{ skill: 'miner', level: 10 }] })],
      });
      await clickFirstItem();
      expect(screen.getByText('Skills')).toBeInTheDocument();
    });

    test('hides requirements panel entirely when showRequirements=false', async () => {
      renderStore({
        showRequirements: false,
        storeItems: [makeItem({ required_items: [makeItem({ name: 'oak log', amount: 5 })] })],
      });
      await clickFirstItem();
      expect(screen.queryByText('Materials')).not.toBeInTheDocument();
      expect(screen.queryByText('Skills')).not.toBeInTheDocument();
    });

    test('shows "No requirements" when item has neither materials nor skills', async () => {
      renderStore({
        storeItems: [makeItem({ required_items: [], skill_requirements: [] })],
      });
      await clickFirstItem();
      expect(screen.getByText('No requirements')).toBeInTheDocument();
    });
  });

  // ── showItemInformation ────────────────────────────────────────────────────

  describe('showItemInformation prop', () => {
    const itemWithInfo = makeItem({ information: 'Crafted from iron ore.' });

    test('shows information text when showItemInformation=true', async () => {
      renderStore({ storeItems: [itemWithInfo], showItemInformation: true });
      await clickFirstItem();
      expect(screen.getByText('Crafted from iron ore.')).toBeInTheDocument();
    });

    test('does not show information text by default', async () => {
      renderStore({ storeItems: [itemWithInfo] });
      await clickFirstItem();
      expect(screen.queryByText('Crafted from iron ore.')).not.toBeInTheDocument();
    });
  });

  // ── Met / Unmet — Materials ───────────────────────────────────────────────

  describe('Material requirement met/unmet state', () => {
    const itemNeedingLogs = makeItem({
      required_items: [makeItem({ name: 'oak log', amount: 10 })],
    });

    test('shows able-color count when player has enough', async () => {
      const { inventoryStore } = renderStore({ storeItems: [itemNeedingLogs] });
      inventoryStore.setInventoryItems([{ id: 1, username: 'u', item: 'oak log', amount: 15 }]);
      await clickFirstItem();

      const count = screen.getByText('15 / 10');
      expect(count).toHaveClass('able-color');
    });

    test('shows not-able-color count when player does not have enough', async () => {
      const { inventoryStore } = renderStore({ storeItems: [itemNeedingLogs] });
      inventoryStore.setInventoryItems([{ id: 1, username: 'u', item: 'oak log', amount: 3 }]);
      await clickFirstItem();

      const count = screen.getByText('3 / 10');
      expect(count).toHaveClass('not-able-color');
    });

    test('shows 0 / N when player has none of the item', async () => {
      renderStore({ storeItems: [itemNeedingLogs] });
      await clickFirstItem();

      expect(screen.getByText('0 / 10')).toBeInTheDocument();
    });
  });

  // ── Met / Unmet — Skills ─────────────────────────────────────────────────

  describe('Skill requirement met/unmet state', () => {
    const itemNeedingMiner = makeItem({
      skill_requirements: [{ skill: 'miner', level: 30 }],
    });

    test('shows able-color row when player meets the skill level', async () => {
      const { skillsStore } = renderStore({ storeItems: [itemNeedingMiner] });
      skillsStore.UserLevelsResource.miner_level = 35;
      await clickFirstItem();

      const row = document.querySelector('.skill-requirements .flex, [class*="able-color"]');
      // The skill row wrapper should carry able-color
      const skillRow = screen.getByText('Lv 30').closest('div')!;
      expect(skillRow).toHaveClass('able-color');
    });

    test('shows not-able-color row when player does not meet the skill level', async () => {
      const { skillsStore } = renderStore({ storeItems: [itemNeedingMiner] });
      skillsStore.UserLevelsResource.miner_level = 10;
      await clickFirstItem();

      const skillRow = screen.getByText('Lv 30').closest('div')!;
      expect(skillRow).toHaveClass('not-able-color');
    });

    test('shows player current level next to requirement', async () => {
      const { skillsStore } = renderStore({ storeItems: [itemNeedingMiner] });
      skillsStore.UserLevelsResource.miner_level = 22;
      await clickFirstItem();

      expect(screen.getByText('(you: 22)')).toBeInTheDocument();
    });

    test('shows skill name as text', async () => {
      renderStore({ storeItems: [itemNeedingMiner] });
      await clickFirstItem();
      expect(screen.getByText('miner')).toBeInTheDocument();
    });

    test('shows required level', async () => {
      renderStore({ storeItems: [itemNeedingMiner] });
      await clickFirstItem();
      expect(screen.getByText('Lv 30')).toBeInTheDocument();
    });

    test('maps adventurer skill to adventurer_respect correctly', async () => {
      const { skillsStore } = renderStore({
        storeItems: [makeItem({ skill_requirements: [{ skill: 'adventurer', level: 50 }] })],
      });
      skillsStore.UserLevelsResource.adventurer_respect = 60;
      await clickFirstItem();

      expect(screen.getByText('(you: 60)')).toBeInTheDocument();
      const skillRow = screen.getByText('Lv 50').closest('div')!;
      expect(skillRow).toHaveClass('able-color');
    });
  });

  // ── Trade button disabled ──────────────────────────────────────────────────

  describe('Trade button disabled state', () => {
    test('button is enabled when there are no requirements', async () => {
      renderStore({ storeItems: [makeItem()] });
      await clickFirstItem();

      const btn = screen.getByText('Smith').closest('button')!;
      expect(btn).not.toBeDisabled();
    });

    test('button is disabled when a material requirement is not met', async () => {
      renderStore({
        storeItems: [makeItem({ required_items: [makeItem({ name: 'oak log', amount: 10 })] })],
        // inventoryStore is empty by default → 0 oak logs
      });
      await clickFirstItem();

      const btn = screen.getByText('Smith').closest('button')!;
      expect(btn).toBeDisabled();
    });

    test('button is enabled when all material requirements are met', async () => {
      const { inventoryStore } = renderStore({
        storeItems: [makeItem({ required_items: [makeItem({ name: 'oak log', amount: 10 })] })],
      });
      inventoryStore.setInventoryItems([{ id: 1, username: 'u', item: 'oak log', amount: 10 }]);
      await clickFirstItem();

      const btn = screen.getByText('Smith').closest('button')!;
      expect(btn).not.toBeDisabled();
    });

    test('button is disabled when a skill requirement is not met', async () => {
      renderStore({
        storeItems: [makeItem({ skill_requirements: [{ skill: 'miner', level: 30 }] })],
        // skillsStore defaults to level 0
      });
      await clickFirstItem();

      const btn = screen.getByText('Smith').closest('button')!;
      expect(btn).toBeDisabled();
    });

    test('button is always enabled when showRequirements=false', async () => {
      renderStore({
        showRequirements: false,
        storeItems: [makeItem({ required_items: [makeItem({ name: 'oak log', amount: 999 })] })],
      });
      await clickFirstItem();

      const btn = screen.getByText('Smith').closest('button')!;
      expect(btn).not.toBeDisabled();
    });
  });
});
