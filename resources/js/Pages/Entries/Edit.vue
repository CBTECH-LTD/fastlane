<template>
    <f-the-app-layout>
        <template v-slot:title>{{ item.attributes.name }}</template>
        <template v-slot:subtitle>{{ entryType.singular_name }}</template>
        <template v-slot:actions>
            <f-button :href="item.links.parent" variant="outline" left-icon="arrow-left">
                Back to list
            </f-button>
        </template>

        <form @submit.prevent="submitForm">
            <f-boxed-card>
                <template v-for="field in form">
                    <f-form-field :errors="$page.errors.get(field.name)" :required="field.isRequired()">
                        <template v-if="field.label" v-slot:label>
                            {{ field.label }}
                        </template>
                        <component :is="field.component"
                                   :field="field"
                                   :required="field.isRequired()"
                                   :aria-required="field.isRequired()"
                                   :placeholder="field.placeholder"
                                   :aria-placeholder="field.placeholder"
                        ></component>

                    </f-form-field>
                </template>

                <template v-slot:footer>
                    <div class="text-right">
                        <f-button submit
                                  color="success"
                                  size="lg"
                                  left-icon="save"
                                  :disabled="isFormDisabled"
                                  :aria-disabled="isFormDisabled"
                                  :loading="isUpdating">
                            Save
                        </f-button>
                    </div>
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
    import FormSchema from '../../Support/FormSchema'
    import Dialogs from '../../Support/Dialogs'

    export default {
        name: 'Entries.Edit',

        props: {
            item: {
                required: true,
                type: Object,
            },
            entryType: {
                required: true,
                type: Object,
            },
        },

        data () {
            return {
                isUpdating: false,
                form: new FormSchema({
                    ...this.item.attributes,
                }, this.entryType.schema),
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
                        await this.$inertia.patch(this.item.links.self, formObject.all())
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
