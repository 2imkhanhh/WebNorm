import { createApp } from 'vue';
import Dashboard from './Dashboard.vue';

const appElement = document.getElementById('studio-app');
if (appElement) {
    const app = createApp(Dashboard);
    app.mount('#studio-app');
}

