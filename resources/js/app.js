import '../css/app.css';
import './bootstrap';

import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {createApp, h} from 'vue';
import {ZiggyVue} from '../../vendor/tightenco/ziggy';
import 'primeicons/primeicons.css'
import i18n from "@/i18n.js";
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue'),
    ),
  setup({el, App, props, plugin}) {
    return createApp({render: () => h(App, props)})
      .use(plugin)
      .use(ZiggyVue)
      .use(i18n)
      .use(Toast, {
        timeout: 3000,
        position: 'top-right',
      })
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});
