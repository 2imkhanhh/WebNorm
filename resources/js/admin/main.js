import { createApp } from 'vue';
import StudioApp from './StudioApp.vue';

const appElement = document.getElementById('studio-app');
if (appElement) {
    const app = createApp(StudioApp);
    app.mount('#studio-app');
}

