<template>
    <f-boxed-card :data-panel="field.name" :icon="field.config.icon">
        <template v-slot:title>{{ field.label }}</template>

        <template v-for="child in field.childrenSchema.getAll()">
            <component :is="child.component"
                       :field="child"
                       :required="child.required"
                       :aria-required="child.required"
                       :placeholder="child.placeholder"
                       :aria-placeholder="child.placeholder"
                       @input="onInput"
            ></component>
        </template>
    </f-boxed-card>
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
                each(this.field.childrenSchema.getAll(), child => {
                    formObject.put(child.name, child.value)
                })
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
                    childrenSchema: FormSchemaFactory(childrenData, field.config.children),
                    isDirty () {
                        return this.childrenSchema.isDirty()
                    }
                }
            )
        }
    }
</script>
