<template>
    <f-the-app-layout>
        <template v-slot:title>New {{ entryType.singular_name }}</template>
        <template v-slot:actions>
            <f-button :href="links.parent" variant="outline" left-icon="arrow-left">
                Back to list
            </f-button>
        </template>

        <form @submit.prevent="submitForm">
            <f-boxed-card>
                <template v-for="field in form">
                    <f-form-field :errors="$page.errors.get(field.name)">
                        <template v-if="field.label" v-slot:label>
                            {{ field.label }}
                        </template>
                        <component :is="field.component"
                                   :field="field"
                                   v-model="field.value"
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
                                  :loading="isCreating">
                            Save
                        </f-button>
                    </div>
                </template>
            </f-boxed-card>
        </form>
    </f-the-app-layout>
</template>

<script>
    import FormSchema from '../../Support/FormSchema'

    export default {
        name: 'Entries.Create',

        props: {
            links: {
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
                isCreating: false,
                form: new FormSchema({}, this.entryType.schema),
            }
        },

        computed: {
            isFormDisabled () {
                return this.isCreating || !this.form.isDirty()
            }
        },

        methods: {
            async submitForm () {
                if (this.form.isDirty() && !this.isCreating) {
                    this.isCreating = true

                    try {
                        await this.$inertia.post(this.links.form, this.form.toFormObject(false).all())
                    } catch {}

                    this.isCreating = false
                }
            }
        }
    }
</script>
