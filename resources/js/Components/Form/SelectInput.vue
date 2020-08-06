<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required">
        <template v-if="field.label" v-slot:label>{{ field.label }}</template>
        <div class="w-full">
            <v-select
                class="form-input"
                :clearable="false"
                :options="field.config.options"
                :multiple="field.config.multiple"
                v-model="field.value">
            </v-select>
        </div>
    </f-form-field>
</template>

<script>
import VSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import FormInput from '../Mixins/FormInput'
import { FormField, FormFieldFactory } from '../../Support/FormField'
import filter from 'lodash/filter'
import map from 'lodash/map'

export default {
    name: 'SelectInput',
    mixins: [FormInput],
    components: { VSelect },

    methods: {
        commit (formObject) {
            formObject.put(this.field.name, this.prepareValueForCommit())
        },

        prepareValueForCommit () {
            if (!this.field.value) {
                return null
            }

            if (this.field.config.multiple === true) {
                return map(this.field.value, v => {
                    return v.value
                })
            }

            return this.field.value.value
        }
    },

    buildForSchema ({ field, component, value }) {
        const getValue = () => {
            if (!value) {
                return []
            }

            if (field.config.multiple === true) {
                return map(value, v => v.value)
            }

            return [value[0].value] || []
        }

        const filteredValue = filter(field.config.options, o => {
            return getValue().includes(o.value)
        })

        return FormFieldFactory(
            field,
            component,
            filteredValue || field.default
        )
    }
}
</script>

<style>
.form-input.v-select .vs__selected {
    padding: 2px 4px !important;
] margin-right: 4 px !important;
}

.vs__selected {
    font-size: theme('fontSize.xs') !important;
    font-weight: theme('fontWeight.semibold') !important;
    color: theme('colors.gray.800') !important;
    background-color: theme('colors.gray.200') !important;
    border-color: theme('colors.gray-400') !important;
}
</style>
