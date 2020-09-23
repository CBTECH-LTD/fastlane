<template>
    <f-the-app-layout>
        <template v-slot:title>{{ item.meta.item_label }}</template>
        <template v-slot:subtitle>{{ item.meta.entry_type.singular_name }}</template>
        <template v-slot:actions>
            <f-button :href="item.links.parent" variant="outline" left-icon="arrow-left">
                {{ $l('core.back_to_list') }}
            </f-button>
            <f-button :href="item.links.create" variant="outline" color="success" left-icon="plus">
                {{ $l('core.add_more') }}
            </f-button>
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

        <f-form-root id="editForm"
                     @submit.prevent="submitForm"
                     :form="form"
                     :panels="item.meta.entry_type.panels">
        </f-form-root>

        <f-boxed-card class="mt-8 border-red-500">
            <template v-slot:title>
                <span class="flex items-center text-danger-600">
                    <f-icon name="exclamation-triangle" class="text-2xl mr-2"/>
                    {{ $l('core.danger_zone') }}
                </span>
            </template>

            <div class="flex justify-between">
                <div class="flex flex-col text-sm">
                    <strong class="font-bold text-gray-900">{{ $l('core.delete_entry_title') }}</strong>
                    <span class="font-normal text-gray-800">{{ $l('core.delete_entry_description') }}</span>
                </div>
                <div>
                    <f-button @click="deleteItem" color="danger" variant="outline" left-icon="trash">{{ $l('core.delete_entry_button') }}</f-button>
                </div>
            </div>
        </f-boxed-card>
    </f-the-app-layout>
</template>

<script>
import Dialogs from '../../Support/Dialogs'
import { FormSchemaFactory } from '../../Support/FormSchema'

export default {
    name: 'Entries.Edit',

    props: {
        item: {
            required: true,
            type: Object,
        },
    },

    // remember: ['form'],

    data () {
        return {
            isUpdating: false,
            form: new FormSchemaFactory(
                this.item.attributes, this.item.meta.entry_type.schema
            )
        }
    },

    computed: {
        isFormDisabled () {
            return this.isUpdating || !this.form.isDirty()
        },
    },

    methods: {
        getFieldSlot (field) {
            return field.panel
                ? `${field.panel}____${field.name}`
                : `default_panel____${field.name}`
        },

        async submitForm () {
            const formObject = this.form.toFormObject()

            if (formObject && this.form.isDirty() && !this.isUpdating) {
                this.isUpdating = true

                try {
                    await this.$inertia.patch(this.item.links.self, formObject.all(), {
                        preserveState: false,
                    })
                } catch {}

                this.isUpdating = false
            }
        },

        async deleteItem () {
            if (!this.isUpdating && await Dialogs.confirm('Are you absolutely sure?')) {
                this.isUpdating = true

                try {
                    await this.$inertia.delete(this.item.links.self)
                } catch {}

                this.isUpdating = true
            }
        }
    }
}
</script>
