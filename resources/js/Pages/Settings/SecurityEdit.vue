<template>
    <AccountSettings :sidebar-menu="sidebarMenu">
        <template v-slot:title>{{ $l('core.account_settings.security_title') }}</template>
        <template v-slot:actions>
            <f-button submit form="editForm"
                      class="ml-4"
                      color="success"
                      size="lg"
                      left-icon="save"
                      :disabled="isFormDisabled"
                      :aria-disabled="isFormDisabled"
                      :loading="isUpdating">
                {{ $l('core.save') }}
            </f-button>
        </template>

        <f-form-root id="editForm" :form="form" :panels="[]" @submit.prevent="submitForm"/>
    </AccountSettings>
</template>

<script>
import AccountSettings from './Shared/AccountSettings'
import { FormSchemaFactory } from '../../Support/FormSchema'

function makeField (label, attribute, component) {
    return {
        value: '',
        field: {
            attribute,
            component,
            config: {
                label,
                default: null,
                panel: null,
                placeholder: label,
                required: true,
                unique: false,
                sortable: false,
                listing: {
                    colWidth: 0,
                }
            },
        },
    }
}

export default {
    name: 'TokensCreate',
    components: { AccountSettings },

    props: {
        links: {
            required: true,
            type: Object,
        },
        sidebarMenu: {
            type: Array,
            required: true,
        },
    },

    data () {
        return {
            isUpdating: false,
            form: new FormSchemaFactory({
                password: makeField(this.$l('core.account_settings.security_new_password'), 'password', 'password'),
                password_confirmation: makeField(this.$l('core.account_settings.security_confirm_new_password'), 'password_confirmation', 'password'),
            })
        }
    },

    computed: {
        isFormDisabled () {
            return this.isUpdating || !this.form.isDirty()
        },
    },

    methods: {
        async submitForm () {
            if (this.form.isDirty() && !this.isUpdating) {
                this.isUpdating = true

                try {
                    await this.$inertia.patch(this.links.form, this.form.toFormObject().all())
                } catch {}

                this.isUpdating = false
            }
        }
    }
}
</script>
