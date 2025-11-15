<template>
  <div
    id="map_container"
    class="border-ridge border-primary-800 absolute top-0 z-50 flex h-[75vh] w-full max-w-7xl flex-col overflow-hidden rounded-lg border-8 transition-all duration-200"
    :class="{
      'left-0': mapStore.isMapVisible,
      'invisible left-full': !mapStore.isMapVisible,
    }"
  >
    <div id="map_container_header" class="m-0 flex bg-orange-50 px-1 py-2">
      <img
        id="toggle_icon_list_image"
        class="cur-pointer"
        src="/images/symbol icon.png"
        alt="Toggle icon"
        @click="mapStore.toggleIconListVisibility()"
      />
      <div class="relative">
        <div
          id="map_type_toggle_overlay"
          class="invisible absolute top-0 left-0 z-10 h-[48px] w-[48px] cursor-pointer rounded-md bg-cyan-700 opacity-60"
        ></div>
        <img
          id="toggle_world_image"
          src="/images/globe.png"
          class="h-[48px] w-[48px] max-w-none cursor-pointer"
          alt="World"
          @click="mapStore.toggleMapType()"
        />
      </div>
      <h2 id="map_header" class="w-full text-3xl">{{ $t('Local map') }}</h2>
      <img
        id="close_map_button"
        class="cont_exit"
        src="/images/exit.png"
        width="20px"
        height="20px"
        alt="Close"
        @click="mapStore.toggleMapVisibility()"
      />
      <div
        id="map_icon_list"
        class="w bg-primary-500 absolute top-[64px] z-50 max-w-[180px] text-left transition-all duration-200"
        :class="[mapStore.isIconListVisible ? 'left-0' : '-left-full']"
      >
        <ul class="list-none p-4">
          <MapListIcon src="boat travel icon.png" text="Boat travel" />
          <MapListIcon src="pesr travel icon.png" text="Pesr travel" />
          <MapListIcon src="combat icon.png" text="Combat" />
        </ul>
      </div>
    </div>
    <div class="relative overflow-scroll">
      <span
        id="map_player_marker"
        class="absolute z-10 h-6 w-6 bg-red-500"
        :style="playerMarkerStyle"
      ></span>
      <div
        id="map_local_img_container"
        class="relative"
        :class="[mapStore.currentMapType === 'local' ? '' : 'hidden']"
      >
        <img
          id="local_img"
          :src="`/images/${mapLocation}m.png`"
          class="h-[1600px] w-[1600px] max-w-none"
          alt="Local map"
        />
      </div>
      <div
        id="map_world_img_container"
        class="bg-primary-500 relative h-full grid-cols-[repeat(9,200px)]"
        :class="[mapStore.currentMapType === 'world' ? 'grid' : 'hidden']"
      >
        <img
          v-for="(src, i) in worldMapImages"
          :key="i"
          class="image-auto-render max-w-none"
          alt="map img"
          height="200px"
          width="200px"
          :src="`/images/${src}.png`"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Game } from '@/advclient';
import { GamePieces } from '@/clientScripts/gamePieces';
import { useMapStore } from '@/ui/stores/MapStore';
import { computed, defineProps, ref } from 'vue';

defineProps<{ mapLocation: string }>();

// Exception array from Blade
const imagesWithoutM = [
  '1.1',
  '2.1',
  '3.1',
  '4.1',
  '5.1',
  '6.1',
  '7.1',
  '8.1',
  '9.1',
  '1.2',
  '9.2',
  '9.3',
  '1.4',
  '2.4',
  '9.4',
  '1.5',
  '8.5',
  '9.5',
  '1.6',
  '8.6',
  '9.6',
  '1.7',
  '2.7',
  '3.7',
  '7.7',
  '8.7',
  '9.7',
  '1.8',
  '2.8',
  '4.8',
  '5.8',
  '6.8',
  '7.8',
  '8.8',
  '9.8',
  '1.9',
  '5.9',
  '6.9',
  '7.9',
  '8.9',
  '4.10',
  '5.10',
  '6.10',
  '7.10',
  '8.10',
  '9.10',
];

// Generate world map image sources
const worldMapImages = computed(() => {
  const arr: string[] = [];
  let x = 1,
    y = 1;
  for (let i = 0; i < 90; i++) {
    let src = `${String(x)}.${String(y)}`;
    if (imagesWithoutM.includes(src)) {
      src = '1.1';
    } else {
      src = `${String(x)}.${String(y)}m`;
    }
    arr.push(src);
    x++;
    if (x === 10) {
      x = 1;
      y++;
    }
  }
  return arr;
});

// Inline MapListIcon component
const MapListIcon = {
  props: ['src', 'text'],
  template: `<li class="flex items-center gap-2 mb-2"><img :src="'/images/' + src" :alt="text" class="h-6 w-6" /><span>{{ text }}</span></li>`,
};

const mapStore = useMapStore();

const playerX = ref(0);
const playerY = ref(0);

const playerMarkerStyle = computed(() => {
  let x = playerX.value;
  let y = playerY.value;
  if (mapStore.currentMapType === 'local') {
    x = x / 2;
    y = y / 2;
  } else {
    const map = Game.properties.currentMap.split('.');
    x = x / 16 + (parseInt(map[0]) - 1) * 200 - 12.5;
    y = y / 16 + (parseInt(map[1]) - 1) * 200 - 12.5;
  }
  return {
    left: String(x) + 'px',
    top: String(y) + 'px',
  };
});

mapStore.$onAction(({ name }) => {
  if (name === 'toggleMapVisibility') {
    playerX.value = GamePieces.player.xpos;
    playerY.value = GamePieces.player.ypos;
  }
});
</script>
