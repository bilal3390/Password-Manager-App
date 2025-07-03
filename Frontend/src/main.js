import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import './assets/main.css'

import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

const app = createApp(App);

app.use(router);
app.use(Toast, {
  position: 'top-right',
  timeout: 1000,
  closeOnClick: true,
  pauseOnHover: true,
  draggable: true,
  showCloseButtonOnHover: true,
});

app.mount('#app');