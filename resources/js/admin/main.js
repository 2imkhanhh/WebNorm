import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

createInertiaApp({
  title: title => title ? `${title} - Norm Admin` : 'Norm Admin',
  resolve: name => {
    const pages = import.meta.glob('./*.vue', { eager: true });
    return pages[`./${name}.vue`];
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el);
  },
});
