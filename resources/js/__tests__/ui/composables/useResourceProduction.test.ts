import { describe, test, expect, beforeEach, afterEach, vi } from 'vitest';
import { useResourceProduction } from '@/ui/composables/useResourceProduction';

describe('useResourceProduction', () => {
  beforeEach(() => {
    vi.useFakeTimers();
  });

  afterEach(() => {
    vi.useRealTimers();
  });

  test('starts with no action running', () => {
    const { infoText, isActionActive } = useResourceProduction({
      actionText: 'Growing',
      noActionText: 'No crops growing',
    });

    expect(isActionActive.value).toBe(false);
    expect(infoText.value).toBe('No crops growing');
  });

  test('startCountdown with a future end time activates the action', () => {
    const { infoText, isActionActive, startCountdown } = useResourceProduction({
      actionText: 'Growing',
      noActionText: 'No crops growing',
    });

    startCountdown(Date.now() + 60_000, 'wheat');
    vi.advanceTimersByTime(1_000);

    expect(isActionActive.value).toBe(true);
    expect(infoText.value).toBe('Growing Wheat');
  });

  test('startCountdown with a null end time does not activate the action', () => {
    const { infoText, isActionActive, startCountdown } = useResourceProduction({
      actionText: 'Growing',
      noActionText: 'No crops growing',
    });

    startCountdown(null, null);

    expect(isActionActive.value).toBe(false);
    expect(infoText.value).toBe('No crops growing');
  });

  test('infoText becomes Finished once the timer runs out', () => {
    const { infoText, isFinished, startCountdown } = useResourceProduction({
      actionText: 'Mining for',
      noActionText: 'No miners at work',
    });

    startCountdown(Date.now() - 1_000, 'iron ore');
    vi.advanceTimersByTime(1_000);

    expect(isFinished.value).toBe(true);
    expect(infoText.value).toBe('Finished');
  });

  test('clearCountdown resets state back to no-action', () => {
    const {
      infoText,
      isActionActive,
      selectedType,
      startCountdown,
      clearCountdown,
    } = useResourceProduction({
      actionText: 'Growing',
      noActionText: 'No crops growing',
    });

    startCountdown(Date.now() + 60_000, 'wheat');
    clearCountdown();

    expect(isActionActive.value).toBe(false);
    expect(selectedType.value).toBe(null);
    expect(infoText.value).toBe('No crops growing');
  });

  test('setAvailableWorkforce updates availableWorkforce', () => {
    const { availableWorkforce, setAvailableWorkforce } = useResourceProduction(
      {
        actionText: 'Growing',
        noActionText: 'No crops growing',
      },
    );

    setAvailableWorkforce(12);

    expect(availableWorkforce.value).toBe(12);
  });
});
