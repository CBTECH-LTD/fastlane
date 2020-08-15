<template>
    <AccountSettings :sidebar-menu="sidebarMenu">
        <template v-slot:title>New personal access token</template>
        <template v-slot:description>
            Personal access tokens can be used to communicate with the Fastlane Content API.
        </template>

        <f-form-root id="createForm" :form="form" :panels="[]" @submit.prevent="submitForm"/>

        <div class="mt-8 flex items-center">
            <f-button submit form="createForm" color="green" size="lg">Generate token</f-button>
            <f-button :href="links.top" color="black" variant="minimal" size="lg" class="ml-4">Cancel</f-button>
        </div>
    </AccountSettings>
</template>

<script>
import map from 'lodash/map'
import AccountSettings from './Shared/AccountSettings'
import { FormSchemaFactory } from '../../Support/FormSchema'

export default {
    name: 'TokensCreate',
    components: { AccountSettings },

    props: {
        links: {
            type: Object,
            required: true,
        },
        abilities: {
            type: Array,
            required: true,
        },
        sidebarMenu: {
            type: Array,
            required: true,
        },
    },

    data () {
        return {
            isCreating: false,
            form: new FormSchemaFactory({}, [
                {
                    config: {},
                    default: null,
                    label: 'Token name',
                    listWidth: 0,
                    name: 'name',
                    panel: null,
                    placeholder: 'Name',
                    required: true,
                    type: 'string'
                },
                {
                    config: {
                        options: map(this.abilities, a => ({ label: a, value: a })),
                        multiple: true,
                        type: 'checkbox'
                    },
                    default: null,
                    label: 'Token Abilities',
                    listWidth: 0,
                    name: 'abilities',
                    panel: null,
                    placeholder: 'Abilities',
                    required: true,
                    type: 'select'
                },
            ])
        }
    },

    methods: {
        async submitForm () {
            if (this.form.isDirty() && !this.isCreating) {
                this.isCreating = true

                try {
                    await this.$inertia.post(this.links.form, this.form.toFormObject().all())
                } catch {}

                this.isCreating = false
            }
        }
    }
}
</script>
