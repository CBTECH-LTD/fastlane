import { InertiaApp } from '@inertiajs/inertia-vue'
import ErrorBagsFactory from './Support/ErrorBags'
import Vue from 'vue'
import PortalVue from 'portal-vue'
import VueI18n from 'vue-i18n'
import VueMeta from 'vue-meta'
import components from './components.js'
import Asset from './Plugins/Asset'
import Lang from './Plugins/Lang'

/**
 * Register all plugins.
 */
Vue.use(Asset)
Vue.use(Lang)
Vue.use(VueI18n)
Vue.use(PortalVue)
Vue.use(VueMeta)

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

const initialPage = JSON.parse(app.dataset.page)

new Vue({
    metaInfo: {
        // if no subcomponents specify a metaInfo.title, this title will be used
        title: 'Control Panel',
        // all titles will be injected into this template
        titleTemplate: '%s | ' + initialPage.props.app.name,
    },
    render: h => {
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
