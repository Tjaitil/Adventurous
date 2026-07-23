<template>
  <Teleport to="body">
    <div
      v-if="isCrashed"
      class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80"
    >
      <div
        v-if="isDev"
        class="mx-4 max-h-screen w-full max-w-3xl overflow-auto rounded border border-red-600 bg-gray-900 p-6 text-white"
      >
        <h1 class="mb-4 text-2xl font-bold text-red-500">Game Crashed</h1>

        <section class="mb-4">
          <h2 class="mb-1 text-sm font-semibold uppercase text-red-400">Error</h2>
          <pre class="whitespace-pre-wrap break-words text-xs text-red-300">{{
            crashInfo?.error.message
          }}</pre>
          <pre class="mt-2 whitespace-pre-wrap break-words text-xs text-gray-400">{{
            crashInfo?.error.stack
          }}</pre>
        </section>

        <section class="mb-4">
          <h2 class="mb-1 text-sm font-semibold uppercase text-yellow-400">
            Game State
          </h2>
          <pre class="whitespace-pre-wrap text-xs text-gray-300">{{
            JSON.stringify(crashInfo?.gameState, null, 2)
          }}</pre>
        </section>

        <button
          class="mt-2 rounded bg-red-700 px-4 py-2 text-sm text-white hover:bg-red-600"
          @click="reload"
        >
          Reload
        </button>
      </div>

      <div
        v-else
        class="mx-4 w-full max-w-md rounded border border-gray-700 bg-gray-900 p-8 text-center text-white"
      >
        <h1 class="mb-3 text-2xl font-bold">Game Crashed</h1>
        <p class="mb-6 text-gray-400">
          A report has been forwarded to the support team.
        </p>
        <button
          class="rounded bg-primary-700 px-6 py-2 text-white hover:bg-primary-600"
          @click="reload"
        >
          Reload
        </button>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import type { InventoryItem } from '@/types/InventoryItem';
import axios from 'axios';

interface GameState {
  map: string;
  coordinates: { x: number; y: number };
  inBuilding: boolean;
  building: string;
  inventory: InventoryItem[];
}

interface CrashDetail {
  error: Error;
  gameState: GameState;
}

const isDev = import.meta.env.DEV;

const isCrashed = ref(false);
const crashInfo = ref<CrashDetail | null>(null);

const onCrash = (e: Event) => {
  const detail = (e as CustomEvent<CrashDetail>).detail;
  crashInfo.value = detail;
  isCrashed.value = true;
  void axios.post('/crash-report', {
    error_message: detail.error.message,
    stack_trace: detail.error.stack,
    game_state: detail.gameState,
    environment: isDev ? 'dev' : 'prod',
  });
};

const reload = () => window.location.reload();

onMounted(() => window.addEventListener('game-crash', onCrash));
onUnmounted(() => window.removeEventListener('game-crash', onCrash));
</script>
