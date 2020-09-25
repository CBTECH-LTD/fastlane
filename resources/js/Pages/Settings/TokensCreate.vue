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
            form: new FormSchemaFactory({
                name: {
                    value: '',
                    field: {
                        attribute: 'name',
                        component: 'string',
                        config: {
                            label: 'Token name',
                            placeholder: 'Name',
                            required: true,
                            default: null,
                            panel: null,
                            unique: false,
                            sortable: false,
                            listing: {
                                listWidth: 0,
                            }
                        }
                    },
                },
                abilities: {
                    value: '',
                    field: {
                        attribute: 'abilities',
                        component: 'select',
                        config: {
                            label: 'Token Abilities',
                            default: null,
                            placeholder: 'Abilities',
                            required: true,
                            unique: false,
                            sortable: false,
                            multiple: true,
                            type: 'checkbox',
                            panel: null,
                            options: map(this.abilities, a => ({ label: a, value: a })),
                            listing: {
                                colWidth: 0,
                            },
                        }
                    }
                },
            })
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
