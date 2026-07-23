import { createApp } from 'vue';
import { createPinia } from 'pinia';
import AppVue from './components/App.vue';
import SkillInfoList from './components/skills/SkillInfoList.vue';
import { createI18n } from 'vue-i18n';
import { getLanguageBundle } from './localeSetup';
import InventoryContainer from './components/Inventory/InventoryContainer.vue';
import { initErrorHandler } from '@/base/ErrorHandler';
import ui from '@nuxt/ui/vue-plugin';
import ConversationContainer from './components/ConversationContainer.vue';
import CrashScreen from './components/CrashScreen.vue';

export const i18n = createI18n({
  locale: 'en',
  messages: { en: await getLanguageBundle() },
  legacy: false,
  missingWarn: false,
  silentFallbackWarn: true,
  silentTranslationWarn: true,
});

export const pinia = createPinia();

const components = {
  SkillInfoList,
  ConversationContainer,
  InventoryContainer,
  CrashScreen,
};
const ErrorHandler = initErrorHandler();

document.querySelectorAll('.vue-app').forEach(element => {
  const app = createApp({ AppVue, components });

  app.config.errorHandler = (err, _vm, _info) => {
    const error = err instanceof Error ? err : new Error(String(err));
    ErrorHandler.logError({ text: error.message });
    window.dispatchEvent(new CustomEvent('game-crash', { detail: { error, gameState: null } }));
  };

  app.use(pinia).use(ui).use(i18n).mount(element);
});
