<template>
    <AccountSettings :sidebar-menu="sidebarMenu">
        <template v-slot:title>Edit your password</template>
        <template v-slot:actions>
            <f-button submit form="editForm"
                      class="ml-4"
                      color="success"
                      size="lg"
                      left-icon="save"
                      :disabled="isFormDisabled"
                      :aria-disabled="isFormDisabled"
                      :loading="isUpdating">
                Save
            </f-button>
        </template>

        <f-form-root id="editForm" :form="form" :panels="[]" @submit.prevent="submitForm"/>
    </AccountSettings>
</template>

<script>
import map from 'lodash/map'
import AccountSettings from './Shared/AccountSettings'
import { FormSchemaFactory } from '../../Support/FormSchema'

function makeField (label, name, type) {
    return {
        label,
        name,
        type,
        config: {},
        default: null,
        listWidth: 0,
        panel: null,
        placeholder: label,
        required: true,
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
            form: new FormSchemaFactory({}, [
                makeField('New password', 'password', 'password'),
                makeField('Confirm new password', 'password_confirmation', 'password'),
            ])
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
