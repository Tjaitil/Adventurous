import { fireEvent, render } from '@testing-library/vue';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import '@testing-library/jest-dom/vitest';
import { nextTick } from 'vue';
import { createPinia, setActivePinia } from 'pinia';
import GameMap from '@/ui/components/HUD/GameMap.vue';
import { useMapStore } from '@/ui/stores/MapStore';
import { i18n } from '@/ui/main';
import { gameQuery } from '@/gameQuery';
import { reportError } from '@/ui/errorReporting';

vi.mock(import('@/ui/errorReporting'), () => ({
  reportError: vi.fn<(error: unknown) => void>(),
  registerErrorHandler: vi.fn<(callback: (error: unknown) => void) => void>(),
}));

const unregisters: Array<() => void> = [];

const providePlayerPosition = (position: {
  x: number;
  y: number;
  currentMap: string;
}) => {
  unregisters.push(gameQuery.provide('PLAYER_POSITION', () => position));
};

beforeEach(() => {
  vi.clearAllMocks();
  providePlayerPosition({ x: 0, y: 0, currentMap: '1.1' });
});

afterEach(() => {
  unregisters.forEach(unregister => {
    unregister();
  });
  unregisters.length = 0;
});

const renderGameMap = () => {
  const pinia = createPinia();
  setActivePinia(pinia);
  return render(GameMap, {
    props: { mapLocation: 'Tasnobil' },
    global: { plugins: [pinia, i18n] },
    attachTo: document.body,
  });
};

const mapContainer = (): HTMLElement =>
  document.getElementById('map_container') as HTMLElement;

describe('gameMap.vue', () => {
  it('closes map when clicking outside', async () => {
    expect.hasAssertions();

    renderGameMap();
    const mapStore = useMapStore();
    mapStore.toggleMapVisibility(true);
    await nextTick();

    await fireEvent.pointerDown(document.body);
    await fireEvent.click(document.body);

    expect(mapContainer()).toHaveClass('invisible');
  });

  it('stays open when clicking inside', async () => {
    expect.hasAssertions();

    const { container } = renderGameMap();
    const mapStore = useMapStore();
    mapStore.toggleMapVisibility(true);
    await nextTick();

    const map = container.querySelector('#map_container') as HTMLElement;
    await fireEvent.pointerDown(map);
    await fireEvent.click(map);

    expect(mapContainer()).not.toHaveClass('invisible');
  });

  it('focuses container when opened', async () => {
    expect.hasAssertions();

    const { container } = renderGameMap();
    const mapStore = useMapStore();

    mapStore.toggleMapVisibility(true);
    await nextTick();
    await nextTick();

    expect(container.querySelector('#map_container')).toHaveFocus();
  });

  it('closes on focusout to outside element', async () => {
    expect.hasAssertions();

    const { container } = renderGameMap();
    const mapStore = useMapStore();
    mapStore.toggleMapVisibility(true);
    await nextTick();

    const map = container.querySelector('#map_container') as HTMLElement;
    await fireEvent.focusOut(map, { relatedTarget: document.body });

    expect(mapContainer()).toHaveClass('invisible');
  });

  it('stays open on focusout to element inside', async () => {
    expect.hasAssertions();

    const { container } = renderGameMap();
    const mapStore = useMapStore();
    mapStore.toggleMapVisibility(true);
    await nextTick();

    const map = container.querySelector('#map_container') as HTMLElement;
    const inner = map.querySelector('#toggle_icon_list_image') as HTMLElement;
    await fireEvent.focusOut(map, { relatedTarget: inner });

    expect(mapContainer()).not.toHaveClass('invisible');
  });

  it('places the local marker from the queried player position', async () => {
    expect.hasAssertions();

    unregisters.pop()?.();
    providePlayerPosition({ x: 400, y: 200, currentMap: '3.2' });
    renderGameMap();

    useMapStore().toggleMapVisibility(true);
    await vi.waitFor(() => {
      expect(document.getElementById('map_player_marker')).toHaveStyle({
        left: '200px',
        top: '100px',
      });
    });
  });

  it('places the world marker from the queried position and map', async () => {
    expect.hasAssertions();

    unregisters.pop()?.();
    providePlayerPosition({ x: 1600, y: 800, currentMap: '3.2' });
    renderGameMap();
    const mapStore = useMapStore();
    mapStore.toggleMapType();

    mapStore.toggleMapVisibility(true);
    await vi.waitFor(() => {
      expect(document.getElementById('map_player_marker')).toHaveStyle({
        left: String(1600 / 16 + 2 * 200 - 12.5) + 'px',
        top: String(800 / 16 + 1 * 200 - 12.5) + 'px',
      });
    });
  });

  it('reports an error when no position provider is registered', async () => {
    expect.hasAssertions();

    unregisters.pop()?.();
    renderGameMap();

    useMapStore().toggleMapVisibility(true);
    await vi.waitFor(() => {
      expect(reportError).toHaveBeenCalledOnce();
    });
  });
});
