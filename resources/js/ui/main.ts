import { createApp } from 'vue';
import { createPinia } from 'pinia';
import AppVue from './components/App.vue';
import SkillInfoList from './components/skills/SkillInfoList.vue';
import { createI18n } from 'vue-i18n';
import { getLanguageBundle } from './localeSetup';
import 'baks-components-vue/dist/style.css';
import '../../css/overwrite.css';
import InventoryContainer from './components/Inventory/InventoryContainer.vue';

const i18n = createI18n({
    locale: 'en',
    messages: { en: await getLanguageBundle() },
    legacy: false,
});

const components = {
    SkillInfoList,
    InventoryContainer,
};

document.querySelectorAll('.vue-app').forEach(element => {
    const app = createApp({ AppVue, components });
    const pinia = createPinia();

    app.use(pinia).use(i18n).mount(element);
});
