<template>
  <div id="game_hud" class="absolute z-10 h-full w-full">
    <UProgress
      id="hunger_progressBar"
      v-model="currentHunger"
      :max="hunger.max"
      :ui="{
        base: 'h-6 absolute border-primary-800 border-3 pixelated-corners-sm rounded-none w-68',
        indicator: 'rounded-none',
      }"
    />
    <UProgress
      id="health_progressBar"
      v-model="currentHealth"
      :max="health.max"
      :ui="{
        base: 'h-6 absolute border-primary-800 border-3 pixelated-corners-sm rounded-none w-68',
        indicator: 'rounded-none',
      }"
    />
    <img
      v-show="showHuntedIcon"
      id="HUD_hunted_icon"
      :src="'/images/hunted icon.png'"
      class="absolute top-8 left-4"
      :alt="$t('Hunted Icon icon')"
    />
    <p id="HUD_hunted_locater" class="absolute text-white"></p>
    <div id="HUD-left-icons-container" class="absolute top-[10px] right-2 flex">
      <img
        id="toggle_map_icon"
        class="z-10 cursor-pointer"
        :src="'/images/globe.png'"
        :alt="$t('Map icon')"
      />
    </div>
    <div id="control_text" class="absolute bottom-18 left-8 text-white">
      <p class="extendedControls my-0 text-left">
        C - {{ $t('Toggle Attack Mode') }}
      </p>
      <p class="extendedControls my-0 text-left">P - {{ $t('Pause') }}</p>
      <p class="my-0 text-left">A - {{ $t('Attack') }}</p>
      <p id="control_text_building" class="my-0 text-left">
        E - {{ $t('Build') }}
      </p>
      <p id="control_text_conversation" class="my-0 text-left">
        W - {{ $t('Talk') }}
      </p>
    </div>
    <div id="game_text" class="absolute text-left text-white"></div>
    <div id="inv_toggle_button_container">
      <button id="inv_toggle_button">INV</button>
    </div>
    <div id="control" class="invisble absolute">
      <button id="control_button" :aria-label="$t('Control button')"></button>
    </div>
  </div>
</template>

<script setup lang="ts" vapor>
import { gameEventBus } from '@/gameEventsBus';
import { ref } from 'vue';

interface Props {
  hunger: {
    current: number;
    max: number;
  };
  health: {
    current: number;
    max: number;
  };
}

const { hunger, health } = defineProps<Props>();
const currentHunger = ref(hunger.current);
const currentHealth = ref(health.current);

const showHuntedIcon = ref(false);

gameEventBus.subscribe('PLAYER_HUNTED_UPDATE', ({ isHunted }) => {
  showHuntedIcon.value = isHunted;
});
</script>
