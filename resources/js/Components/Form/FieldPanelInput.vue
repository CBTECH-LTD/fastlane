<template>
    <div class="w-full">
        <f-boxed-card>
            <template v-for="field in field.childrenSchema.getAll()">
                <f-form-field :errors="$page.errors.get(field.name)" :required="field.requuired">
                    <template v-if="field.label" v-slot:label>
                        {{ field.label }}
                    </template>
                    <component :is="field.component"
                               :field="field"
                               :required="field.required"
                               :aria-required="field.required"
                               :placeholder="field.placeholder"
                               :aria-placeholder="field.placeholder"
                               @input="onInput"
                    ></component>

                </f-form-field>
            </template>
        </f-boxed-card>
    </div>
</template>

<script>
    import FormInput from '../Mixins/FormInput'
    import { FormFieldFactory } from '../../Support/FormField'
    import { FormSchemaFactory } from '../../Support/FormSchema'
    import each from 'lodash/each'

    export default {
        name: 'FieldPanelInput',
        mixins: [FormInput],

        methods: {
            /**
             * @param {FormObject} formObject
             */
            commit (formObject) {
                formObject.put(this.field.name, this.field.value)
            },

            onInput () {
                const data = {}

                each(this.field.childrenSchema.getAll(), child => {
                    data[child.name] = child.value
                })

                this.field.value = data
                this.$emit('input', this.field.value)
            }
        },

        buildForSchema ({ field, component, data }) {
            let childrenData = {}

            field.config.children.forEach(c => {
                childrenData[c.name] = data[c.name]
            })

            return FormFieldFactory(
                field,
                component,
                childrenData,
                {
                    childrenSchema: FormSchemaFactory(childrenData, field.config.children)
                }
            )
        }
    }
</script>
