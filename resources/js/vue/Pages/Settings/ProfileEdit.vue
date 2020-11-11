<template>
    <AccountSettings :sidebar-menu="sidebarMenu">
        <template v-slot:title>{{ $l('core.account_settings.profile_title') }}</template>
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

        <f-form-root id="editForm" :form="form" :panels="item.meta.entry_type.panels" @submit.prevent="submitForm"/>
    </AccountSettings>
</template>

<script>
import AccountSettings from './Shared/AccountSettings'
import { FormSchemaFactory } from '../../Support/FormSchema'

export default {
    name: 'TokensCreate',
    components: { AccountSettings },

    props: {
        item: {
            required: true,
            type: Object,
        },
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
            form: new FormSchemaFactory(this.item.attributes, this.item.meta.entry_type.schema)
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
