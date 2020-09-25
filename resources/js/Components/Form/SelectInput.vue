<template>
    <f-form-field :errors="$page.errors.get(field.attribute)" :required="field.config.required" :stacked="stacked">
        <template v-if="field.config.label" v-slot:label>{{ field.config.label }}</template>
        <div class="w-full">
            <!-- Render checkboxes or radios instead of select if type is set for checkbox -->
            <template v-if="field.config.type === 'checkbox'">
                <div v-for="opt in field.config.options" class="flex items-center mb-2">
                    <template v-if="field.config.multiple">
                        <input type="checkbox" class="form-checkbox" :value="opt" v-model="field.value">
                    </template>
                    <template v-else>
                        <input type="radio" class="form-radio" :value="opt" v-model="field.value">
                    </template>
                    <span>{{ opt.label }}</span>
                </div>
            </template>
            <!-- Otherwise just render a select -->
            <v-select
                v-else
                class="form-input"
                :clearable="false"
                :options="field.config.options"
                :multiple="field.config.multiple"
                :value="field.value"
                @input="onInput">
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
import find from 'lodash/find'
import map from 'lodash/map'
import isObject from 'lodash/isObject'

export default {
    name: 'SelectInput',
    mixins: [FormInput],
    components: { VSelect },

    methods: {
        commit (formObject) {
            formObject.put(this.field.attribute, this.prepareValueForCommit())
        },

        prepareValueForCommit () {
            if (!this.field.value) {
                return null
            }

            if (this.field.config.multiple) {
                return map(this.field.value, v => {
                    return v.value
                })
            }

            return this.field.value.value
        },

        isSelected (option) {
            if (this.field.config.multiple) {
                return !!find(this.field.value, v => {
                    return v.value === option.value
                })
            }

            return this.field.value && this.field.value.value === option.value
        },
    },

    buildForSchema ({ field, component, value }) {
        const selectedIds = (() => {
            if (!value) {
                return []
            }

            if (field.config.multiple === true) {
                return map(value, v => {
                    if (isObject(v)) {
                        return v.value
                    }

                    return v
                })
            }

            return value.length ? [value[0].value] : []
        })()

        const filteredValue = filter(field.config.options, o => {
            return selectedIds.includes(o.value)
        })

        return FormFieldFactory(
            field,
            component,
            filteredValue || field.config.default,
            {}
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
