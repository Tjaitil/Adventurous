import { createApp } from 'vue';
import { createPinia } from 'pinia';
import AppVue from './components/App.vue';
import SkillInfoList from './components/skills/SkillInfoList.vue';
import { createI18n } from 'vue-i18n';
import { getLanguageBundle } from './localeSetup';
import InventoryContainer from './components/Inventory/InventoryContainer.vue';
import { initErrorHandler } from '@/base/ErrorHandler';
import ConversationContainer from './components/ConversationContainer.vue';

export const i18n = createI18n({
    locale: 'en',
    messages: { en: await getLanguageBundle() },
    legacy: false,
});

export const pinia = createPinia();

const components = {
    SkillInfoList,
    ConversationContainer,
    InventoryContainer,
};
const ErrorHandler = initErrorHandler();

document.querySelectorAll('.vue-app').forEach(element => {
    const app = createApp({ AppVue, components });

    app.config.errorHandler = async (err, vm, info) => {
        const errorToLog = err + ' ' + JSON.stringify(vm) + ' ' + info;

        ErrorHandler.logError({ text: errorToLog });
    };

    app.use(pinia).use(i18n).mount(element);
});
