<template>
  <div
    v-if="crashStore.isCrashed"
    class="fixed inset-0 z-9999 flex items-center justify-center bg-black/80"
  >
    <div
      class="bg-inverted w-md max-w-full space-y-6 rounded border border-gray-700 p-8 text-center text-white"
    >
      <div v-if="isDev" class="w-full max-w-3xl overflow-auto">
        <h1 class="mb-4 text-2xl font-bold text-red-500">
          {{ t('Game Crashed') }}
        </h1>

        <section class="mb-4 text-left">
          <h2 class="mb-1 text-sm font-semibold text-red-400 uppercase">
            {{ t('Error') }}
          </h2>
          <pre
            class="wrap-break-words text-xs whitespace-pre-wrap text-red-300"
            >{{ crashStore.crashInfo?.error.message }}</pre
          >
          <pre
            class="wrap-break-words mt-2 max-h-40 truncate overflow-auto text-left text-xs whitespace-pre-wrap"
            >{{ crashStore.crashInfo?.error.stack }}</pre
          >
        </section>

        <section v-if="crashStore.crashInfo?.gameState" class="mb-4 text-left">
          <h2 class="mb-1 text-sm font-semibold text-yellow-400 uppercase">
            {{ t('Game State') }}
          </h2>
          <pre class="text-xs whitespace-pre-wrap text-gray-300">{{
            JSON.stringify(crashStore.crashInfo?.gameState, null, 2)
          }}</pre>
        </section>
      </div>

      <template v-else>
        <h1 class="mb-3 text-2xl font-bold">{{ t('Game Crashed') }}</h1>
        <p>
          {{
            t(
              'Oh no! Something went wrong. The game has crashed. Please reload the page to try again.',
            )
          }}
        </p>
        <p>
          {{ t('A report has been forwarded to the support team.') }}
        </p>
      </template>
      <UButton
        color="primary"
        class="bg-primary-700 hover:bg-primary-600 mb-2 w-full"
        @click="reload"
      >
        {{ t('Reload') }}
      </UButton>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useCrashStore } from '@/ui/stores/CrashStore';

const isDev = import.meta.env.DEV;

const { t } = useI18n();

const crashStore = useCrashStore();

const reload = () => {
  window.location.reload();
};
</script>
