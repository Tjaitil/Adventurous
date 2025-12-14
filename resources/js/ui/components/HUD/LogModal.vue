<template>
  <div
    ref="logModal"
    class="bg-primary-200 border-primary-900 fixed right-0 bottom-0 z-50 flex h-0 w-full max-w-[500px] items-center justify-center overflow-hidden overflow-x-scroll border-4 py-4 opacity-0 transition-all duration-200"
  >
    <div class="flex items-center">
      <GameLogItem
        v-if="currentMessageComp"
        :log="currentMessageComp"
        component="p"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { gameEventBus } from '@/gameEventsBus';
import { GameLog, GameLogTypes } from '@/types/GameLog';
import { parseGameLog } from '@/utilities/parseGameLog';
import GameLogItem from '../GameLogItem.vue';

const messages = ref<GameLog[]>([]);
const currentMessage = ref<GameLog | null>({
  message: 'Welcome to Adventurous!',
  type: GameLogTypes.INFO,
});
const isShowing = ref(false);
const logModal = ref<HTMLDivElement>();

const currentMessageComp = computed(() => {
  if (!currentMessage.value) return currentMessage.value;

  return {
    ...currentMessage.value,
    message: parseGameLog(currentMessage.value.message || ''),
  };
});

const showNextMessage = () => {
  if (messages.value.length === 0) {
    isShowing.value = false;
    if (logModal.value) {
      logModal.value.classList.remove('h-fit');
      logModal.value.classList.remove('opacity-100');
      logModal.value.classList.add('h-0');
      logModal.value.classList.add('opacity-0');
    }
    return;
  }

  const msg = messages.value.shift();
  if (!msg) return;
  currentMessage.value = msg;
  isShowing.value = true;

  if (logModal.value) {
    logModal.value.classList.remove('h-0');
    logModal.value.classList.remove('opacity-0');
    logModal.value.classList.add('h-fit');
    logModal.value.classList.add('opacity-100');
  }

  setTimeout(() => {
    showNextMessage();
  }, 4000);
};

gameEventBus.subscribe('GAMELOGGER_MESSAGE_LOGGED', eventData => {
  messages.value.push(eventData.message);
  if (!isShowing.value) {
    showNextMessage();
  }
});
</script>
