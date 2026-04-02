<template>
  <div id="game_hud" class="absolute left-1.5 z-10 h-full w-full">
    <UProgress
      id="hunger_progressBar"
      v-model="currentHunger"
      :max="hunger.max"
      :ui="{
        root: 'absolute left-4 top-2 w-68 h-6',
        base: 'h-6 absolute  border-primary-800 border-3 pixelated-corners-sm rounded-none',
        indicator: 'rounded-none',
        status: 'progress-status-overlay-text text-black',
      }"
    >
      <template #status>
        <span> {{ currentHunger }} / {{ hunger.max }} </span>
      </template>
    </UProgress>
    <UProgress
      id="health_progressBar"
      v-model="currentHealth"
      :max="health.max"
      :ui="{
        root: 'absolute left-[19rem] top-2 w-68 h-6',
        base: 'h-6 absolute border-primary-800 border-3 pixelated-corners-sm rounded-none',
        indicator: 'rounded-none',
        status: 'progress-status-overlay-text text-black',
      }"
    >
      <template #status>
        <span> {{ currentHealth }} / {{ health.max }} </span>
      </template>
    </UProgress>
    <div v-if="showHuntedIcon" class="absolute top-10 left-4">
      <img
        id="HUD_hunted_icon"
        :src="'/images/hunted icon.png'"
        :alt="$t('Hunted Icon icon')"
      />
      <p id="HUD_hunted_locater" class="absolute text-white"></p>
    </div>
    <img
      id="toggle_map_icon"
      class="absolute top-2 right-4 z-10 cursor-pointer"
      :src="'/images/globe.png'"
      :alt="$t('Map icon')"
      @click="useMapStore().toggleMapVisibility(true)"
    />
    <div id="control_text" class="absolute bottom-4 left-4 text-white">
      <p class="extendedControls my-0 text-left">
        C - {{ $t('Toggle Attack Mode') }}
      </p>
      <p class="extendedControls my-0 text-left">P - {{ $t('Pause') }}</p>
      <p class="my-0 text-left">A - {{ $t('Attack') }}</p>
      <p id="control_text_building" class="my-0 text-left">
        E -
        <template v-if="buildingName">
          {{ $t('Enter') }}
          {{ ` ${buildingName}` }}
        </template>
      </p>
      <p id="control_text_conversation" class="my-0 text-left">
        W -
        <template v-if="characterName">
          {{ $t('Talk to') }}
          {{ ` ${characterName}` }}
        </template>
      </p>
    </div>
    <div id="game_text" class="absolute text-left text-white"></div>
    <div id="control" class="invisble absolute">
      <button id="control_button" :aria-label="$t('Control button')"></button>
    </div>
  </div>
</template>

<script setup lang="ts" vapor>
import { gameEventBus } from '@/gameEventsBus';
import { useMapStore } from '@/ui/stores/MapStore';
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
const buildingName = ref<string | null>(null);
const characterName = ref<string | null>(null);

gameEventBus.subscribe('PLAYER_HUNTED_UPDATE', ({ isHunted }) => {
  if (isHunted) {
    showHuntedIcon.value = true;
  } else {
    showHuntedIcon.value = false;
  }
});

gameEventBus.subscribe('HUD_BUILDING_PROMPT_UPDATE', payload => {
  buildingName.value = payload.buildingName;
});

gameEventBus.subscribe('HUD_CONVERSATION_PROMPT_UPDATE', payload => {
  characterName.value = payload.characterName;
});
</script>
