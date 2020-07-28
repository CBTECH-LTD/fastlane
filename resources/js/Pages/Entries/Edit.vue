<template>
    <f-the-app-layout>
        <template v-slot:title>{{ item.meta.item_label }}</template>
        <template v-slot:subtitle>{{ item.meta.entry_type.singular_name }}</template>
        <template v-slot:actions>
            <f-button :href="item.links.parent" variant="outline" left-icon="arrow-left">
                Back to list
            </f-button>
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

        <form id="editForm" @submit.prevent="submitForm">
            <f-boxed-card>
                <template v-for="field in form.getAll()">
                    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required">
                        <template v-if="field.label" v-slot:label>
                            {{ field.label }}
                        </template>
                        <component :is="field.component"
                                   :field="field"
                                   :required="field.required"
                                   :aria-required="field.required"
                                   :placeholder="field.placeholder"
                                   :aria-placeholder="field.placeholder"
                        ></component>
                    </f-form-field>
                </template>
            </f-boxed-card>
        </form>

        <f-boxed-card class="mt-8">
            <template v-slot:title>
                <span class="flex items-center text-danger-600">
                    <f-icon name="exclamation-triangle" class="text-2xl mr-2"/>
                    Danger Zone
                </span>
            </template>

            <div class="flex justify-between">
                <div class="flex flex-col text-sm">
                    <strong class="font-bold text-gray-900">Delete this entry?</strong>
                    <span class="font-normal text-gray-800">Once you delete an entry, there is no going back. Please be certain</span>
                </div>
                <div>
                    <f-button @click="deleteItem" color="danger" variant="outline" left-icon="trash">Delete this entry</f-button>
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
            }
        },

        methods: {
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
