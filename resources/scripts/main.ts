import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { resolvePageComponent } from 'vite-plugin-laravel/inertia'

import { Quasar, Notify, Dialog } from 'quasar'
import quasarLang from 'quasar/lang/ru'
import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/dist/quasar.css'

import axios from 'axios'
import VueAxios from 'vue-axios'

createInertiaApp({
	resolve: (name) => resolvePageComponent(name, import.meta.glob('../views/pages/**/*.vue')),
	setup({ el, app, props, plugin }) {
		createApp({ render: () => h(app, props) })
			.use(plugin)
            .use(Quasar, {
                plugins: {
                    Notify, Dialog
                },
                lang: quasarLang,
                config: {
                    framework: {
                        cssAddon: true
                    }
                }
            })
            .use(VueAxios, axios)
			.mount(el)
	},
})
