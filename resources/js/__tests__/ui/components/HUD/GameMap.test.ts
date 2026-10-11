import { fireEvent, render } from '@testing-library/vue';
import { describe, expect, it, vi } from 'vitest';
import '@testing-library/jest-dom/vitest';
import { nextTick } from 'vue';
import { createPinia, setActivePinia } from 'pinia';
import GameMap from '@/ui/components/HUD/GameMap.vue';
import { useMapStore } from '@/ui/stores/MapStore';
import { i18n } from '@/ui/main';

vi.mock(import('@/advclient'), () => ({
  Game: { properties: { currentMap: '1.1' } },
}));
vi.mock(import('@/clientScripts/gamePieces'), () => ({
  GamePieces: { player: { xpos: 0, ypos: 0 } },
}));

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
});
