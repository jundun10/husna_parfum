import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import LoadingSpinner from './Components/LoadingSpinner.vue';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';


createInertiaApp({
    title: (title) => `${title} - Lamore Perfumes`,

    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),

    progress: false,

    setup({ el, App, props, plugin }) {
        createApp({
            render: () =>
                h('div', null, [
                    h(App, props),
                    h(LoadingSpinner),
                ]),
        })
            .use(plugin)
            .mount(el);
    },
});