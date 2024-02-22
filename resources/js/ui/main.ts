import { createApp } from 'vue';
import AppVue from './components/App.vue';

document.querySelectorAll('.vue-app').forEach(element => {
    const app = createApp({ AppVue, components: {} });
    app.mount(element);
});
