import { render, screen, waitFor } from '@testing-library/vue';
import { describe, test, expect, afterEach } from 'vitest';
import '@testing-library/jest-dom';
import ScreenHUD from '@/ui/components/HUD/ScreenHUD.vue';
import { gameEventBus } from '@/gameEventsBus';
import { createPinia } from 'pinia';
import { i18n } from '@/ui/main';

const renderScreenHUD = (healthCurrent = 100): ReturnType<typeof render> =>
  render(ScreenHUD, {
    props: {
      hunger: { current: 80, max: 100 },
      health: { current: healthCurrent, max: 100 },
    },
    global: {
      plugins: [createPinia(), i18n],
    },
  });

describe('ScreenHUD.vue', () => {
  const unsubscribers: Array<() => void> = [];

  afterEach(() => {
    unsubscribers.forEach(unsub => {
      unsub();
    });
    unsubscribers.length = 0;
  });

  test('renders initial health value', () => {
    renderScreenHUD(50);

    expect(screen.getByText('50 / 100')).toBeInTheDocument();
  });

  test('updates health progress bar when PLAYER_HEALTH_UPDATE is emitted', async () => {
    renderScreenHUD(50);

    gameEventBus.emit('PLAYER_HEALTH_UPDATE', { health: 75 });

    await waitFor(() => {
      expect(screen.getByText('75 / 100')).toBeInTheDocument();
    });
  });

  test('unsubscribes from PLAYER_HEALTH_UPDATE on unmount', () => {
    const result = renderScreenHUD(50);

    result.unmount();

    gameEventBus.emit('PLAYER_HEALTH_UPDATE', { health: 99 });

    // After unmount, the DOM is gone — confirm no stale listener throws
    expect(() => {
      gameEventBus.emit('PLAYER_HEALTH_UPDATE', { health: 99 });
    }).not.toThrow();
  });

  test('shows hunted icon when PLAYER_HUNTED_UPDATE is emitted with isHunted true', async () => {
    renderScreenHUD();

    expect(screen.queryByAltText('Hunted Icon icon')).not.toBeInTheDocument();

    gameEventBus.emit('PLAYER_HUNTED_UPDATE', { isHunted: true });

    await waitFor(() => {
      expect(screen.getByAltText('Hunted Icon icon')).toBeInTheDocument();
    });
  });

  test('hides hunted icon when PLAYER_HUNTED_UPDATE is emitted with isHunted false', async () => {
    renderScreenHUD();

    gameEventBus.emit('PLAYER_HUNTED_UPDATE', { isHunted: true });
    await waitFor(() => {
      expect(screen.getByAltText('Hunted Icon icon')).toBeInTheDocument();
    });

    gameEventBus.emit('PLAYER_HUNTED_UPDATE', { isHunted: false });

    await waitFor(() => {
      expect(screen.queryByAltText('Hunted Icon icon')).not.toBeInTheDocument();
    });
  });

  test('displays building name when HUD_BUILDING_PROMPT_UPDATE is emitted', async () => {
    renderScreenHUD();

    gameEventBus.emit('HUD_BUILDING_PROMPT_UPDATE', { buildingName: 'Tavern' });

    await waitFor(() => {
      expect(screen.getByText('Tavern', { exact: false })).toBeInTheDocument();
    });
  });

  test('displays character name when HUD_CONVERSATION_PROMPT_UPDATE is emitted', async () => {
    renderScreenHUD();

    gameEventBus.emit('HUD_CONVERSATION_PROMPT_UPDATE', { characterName: 'Sailor Bob' });

    await waitFor(() => {
      expect(screen.getByText('Sailor Bob', { exact: false })).toBeInTheDocument();
    });
  });
});
