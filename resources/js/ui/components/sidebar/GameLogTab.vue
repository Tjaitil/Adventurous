<template>
  <div id="log_container" class="w-full">
    <div id="log_container" class="w-full">
      <div
        id="log"
        class="darkTextColor relative m-0 h-52 overflow-y-scroll bg-orange-50"
      >
        <ul id="game_messages" class="mt-0 w-full p-2">
          <GameLogItem
            v-for="(message, index) in messages"
            :key="index"
            :log="message"
          />
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { gameEventBus } from '@/gameEventsBus';
import type { GameLog, ParsedGameLog } from '@/types/GameLog';
import { parseGameLog } from '@/utilities/parseGameLog';
import { ref } from 'vue';
import GameLogItem from '../GameLogItem.vue';

interface Props {
  initMessages: GameLog[];
}
const { initMessages } = defineProps<Props>();

const messages = ref<ParsedGameLog[]>(initMessages);

gameEventBus.subscribe('GAMELOGGER_MESSAGE_LOGGED', ({ message }) => {
  const parsedGameLog = {
    ...message,
    message: parseGameLog(message.message),
    timestamp: message.timestamp ?? new Date().toLocaleTimeString(),
  };

  messages.value = [...messages.value, parsedGameLog];
});
</script>
