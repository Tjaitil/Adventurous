import { beforeEach, describe, expect, test } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import { useProfiencyStatusStore } from '@/ui/stores/ProfiencyStatusStore';
import type { ProficiencyStatuses, Miner } from '@/types/ProficiencyStatuses';

const createMiner = (location: string, permits: number): Miner => ({
  id: 1,
  username: 'test',
  location,
  mineral_ore: null,
  mining_finishes_at: null,
  permits,
});

const createStatuses = (miners: Miner[] | null): ProficiencyStatuses =>
  ({ farmers: null, miners }) as ProficiencyStatuses;

describe('ProfiencyStatusStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
  });

  test('setMinerPermits updates permits for the matching location only', () => {
    const store = useProfiencyStatusStore();
    store.setProfiencyStatuses(
      createStatuses([createMiner('golbak', 1), createMiner('snerpiir', 2)]),
    );

    store.setMinerPermits('snerpiir', 52);

    expect(store.profiencyStatuses?.miners?.map(m => m.permits)).toEqual([
      1, 52,
    ]);
  });

  test('setMinerPermits does nothing when statuses are not set', () => {
    const store = useProfiencyStatusStore();

    store.setMinerPermits('golbak', 5);

    expect(store.profiencyStatuses).toBeNull();
  });

  test('setMinerPermits ignores unknown locations and null miners', () => {
    const store = useProfiencyStatusStore();
    store.setProfiencyStatuses(createStatuses(null));

    store.setMinerPermits('golbak', 5);

    expect(store.profiencyStatuses?.miners).toBeNull();
  });
});
