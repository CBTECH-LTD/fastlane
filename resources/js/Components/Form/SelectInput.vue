<template>
    <div class="w-full">
        <v-select
            class="form-input"
            :clearable="false"
            :options="field.config.options"
            :multiple="field.config.multiple"
            v-model="field.value">
        </v-select>
    </div>
</template>

<script>
    import VSelect from 'vue-select'
    import 'vue-select/dist/vue-select.css'
    import FormInput from '../Mixins/FormInput'
    import { FormField } from '../../Support/FormField'
    import find from 'lodash/find'
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
                if (! this.field.value) {
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
            const filteredValue = find(field.config.options, o => o.value === value)

            return new FormField(
                field,
                component,
                filteredValue || field.default
            )
        }
    }
</script>
