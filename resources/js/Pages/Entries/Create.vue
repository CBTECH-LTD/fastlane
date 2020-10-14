<template>
    <f-the-app-layout>
        <template v-slot:title>{{ $l('core.new') }} {{ item.meta.entry_type.singular_name }}</template>
        <template v-slot:actions>
            <f-button :href="item.links.top" variant="outline" left-icon="arrow-left">
                {{ $l('core.back_to_list') }}
            </f-button>
            <f-button submit form="createForm"
                      class="ml-4"
                      color="success"
                      size="lg"
                      left-icon="save"
                      :disabled="isFormDisabled"
                      :aria-disabled="isFormDisabled"
                      :loading="isCreating">
                {{ $l('core.save') }}
            </f-button>
        </template>

        <f-form-root id="createForm"
                     @submit.prevent="submitForm"
                     :form="form"
                     :panels="item.meta.entry_type.panels">
        </f-form-root>
    </f-the-app-layout>
</template>

<script>
import { FormSchemaFactory } from '../../Support/FormSchema'

export default {
    name: 'Entries.Create',

    props: {
        item: {
            required: true,
            type: Object,
        },
    },

    data () {
        return {
            isCreating: false,
            form: new FormSchemaFactory(this.item.meta.entry_type.fields, this.item.attributes),
        }
    },

    computed: {
        isFormDisabled () {
            return this.isCreating || !this.form.isDirty()
        }
    },

    methods: {
        getFieldSlot (field) {
            return field.panel
                ? `${field.panel}____${field.name}`
                : `default_panel____${field.name}`
        },

        async submitForm () {
            if (this.form.isDirty() && !this.isCreating) {
                this.isCreating = true

                try {
                    await this.$inertia.post(this.item.links.self, this.form.toFormObject(false).all())
                } catch {}

                this.isCreating = false
            }
        }
    }
}
</script>
