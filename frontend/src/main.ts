import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';
import './style.css';
import { useAuthStore } from './stores/auth';

const app = createApp(App);

app.use(createPinia());

// Inicializa a autenticação antes de montar o app
const authStore = useAuthStore();
authStore.initializeAuth();

app.use(router);

app.mount('#app');