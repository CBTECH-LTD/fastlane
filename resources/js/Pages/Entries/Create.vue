<template>
    <f-the-app-layout>
        <template v-slot:title>New {{ entryType.singular_name }}</template>
        <template v-slot:actions>
            <f-button :href="links.parent" variant="outline" left-icon="arrow-left">
                Back to list
            </f-button>
        </template>

        <f-boxed-card>
            <template v-for="field in form">
                <f-form-field :errors="$page.errors.get(field.name)">
                    <template v-if="field.label" v-slot:label>
                        {{ field.label }}
                    </template>
                    <component :is="field.component"
                               :field="field"
                               v-model="field.value"
                               :required="field.isRequired"
                    ></component>
                </f-form-field>
            </template>

            <template v-slot:footer>
                <div class="text-right">
                    <f-button @click="submitForm" color="success" size="lg" left-icon="save">
                        Save
                    </f-button>
                </div>
            </template>
        </f-boxed-card>
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

        methods: {
            async submitForm () {
                if (this.form.isDirty() && !this.isCreating) {
                    this.isCreating = true

                    try {
                        await this.$inertia.post(this.links.form, this.form.toFormObject(true).all())
                    } catch {}

                    this.isCreating = false
                }
            }
        }
    }
</script>
