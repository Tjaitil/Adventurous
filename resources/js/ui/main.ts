import { createApp } from 'vue';
import { createPinia } from 'pinia';
import AppVue from './components/App.vue';
import SkillInfoList from './components/skills/SkillInfoList.vue';
import { createI18n } from 'vue-i18n';
import { getLanguageBundle } from './localeSetup';
import 'baks-components-vue/dist/style.css';
import '../../css/overwrite.css';
import InventoryContainer from './components/Inventory/InventoryContainer.vue';
import { initErrorHandler } from '@/base/ErrorHandler';

export const i18n = createI18n({
    locale: 'en',
    messages: { en: await getLanguageBundle() },
    legacy: false,
});

const components = {
    SkillInfoList,
    InventoryContainer,
};
const ErrorHandler = initErrorHandler();

document.querySelectorAll('.vue-app').forEach(element => {
    const app = createApp({ AppVue, components });

    app.config.errorHandler = async (err, vm, info) => {
        const errorToLog = err + ' ' + JSON.stringify(vm) + ' ' + info;

        ErrorHandler.logError({ text: errorToLog });
    };
    const pinia = createPinia();

    app.use(pinia).use(i18n).mount(element);
});
