import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { TheMask } from 'vue-the-mask';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)

        app.directive('mask-date', {
            beforeMount(el) {
                const handler = (e) => {
                    let v = e.target.value.replace(/\D/g, '').slice(0, 8)
                    v = v.replace(/^(\d{2})(\d)/, '$1/$2')
                    v = v.replace(/\/(\d{2})(\d)/, '/$1/$2')
                    e.target.value = v
                    e.target.dispatchEvent(new Event('input'))
                }
                el.__maskDateHandler__ = handler
                el.addEventListener('input', handler)
            },
            unmounted(el) {
                el.removeEventListener('input', el.__maskDateHandler__)
                delete el.__maskDateHandler__
            }
        })

        app.directive('mask-decimal', {
            beforeMount(el) {
              const handler = e => {
                // 1) só dígitos
                const digits = e.target.value.replace(/\D/g, '')  
                // 2) converte em centavos → unidades
                const num = parseFloat(digits || '0') / 100  
                // 3) formata com ponto e 2 casas, sem separador de milhar
                e.target.value = num.toFixed(2)  
                // 4) dispara input para atualizar o v-model
                el.dispatchEvent(new Event('input'))
              }
              // guarda o handler para poder remover depois
              el.__maskDecimalHandler__ = handler  
              el.addEventListener('input', handler)
            },
            unmounted(el) {
              el.removeEventListener('input', el.__maskDecimalHandler__)
              delete el.__maskDecimalHandler__
            }
          })
          

        return app.mount(el)
    },
    progress: {
        color: '#4B5563',
    },
});
