import { createApp } from 'vue';
import { createPinia } from 'pinia';
import AppVue from './components/App.vue';

import SkillInfoList from './components/skills/SkillInfoList.vue';

const components = {
    SkillInfoList,
};

document.querySelectorAll('.vue-app').forEach(element => {
    const app = createApp({ AppVue, components });
    const pinia = createPinia();

    app.use(pinia).mount(element);
});
