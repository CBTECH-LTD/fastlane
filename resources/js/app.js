import { InertiaApp } from '@inertiajs/inertia-vue'
import ErrorBagsFactory from './Support/ErrorBags'
import Vue from 'vue'
import VueI18n from 'vue-i18n'
import components from './components.js'
import Asset from './Plugins/Asset'
import Lang from './Plugins/Lang'

/**
 * Register all plugins.
 */
Vue.use(Asset)
Vue.use(Lang)
Vue.use(VueI18n)

/*
 * Register all components and pages globally.
 */
Vue.use(components)

/*
 * Create the Inertia-ready Vue app.
 * @type {HTMLElement}
 */
Vue.use(InertiaApp)

const app = document.getElementById('app')

new Vue({
    render: h => {
        const initialPage = JSON.parse(app.dataset.page)

        return h(InertiaApp, {
            props: {
                initialPage,
                resolveComponent: name => {
                    return require(`./Pages/${name}`).default
                },
                transformProps: props => {
                    return {
                        ...props,
                        errors: ErrorBagsFactory(props.errors),
                    }
                }
            },
        })
    },
}).$mount(app)
