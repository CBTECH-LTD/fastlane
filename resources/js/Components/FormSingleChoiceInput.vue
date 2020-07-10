<template>
    <div class="w-full">
        <v-select
            class="form-input"
            :clearable="false"
            :options="field.config.options"
            v-model="field.value">
        </v-select>
    </div>
</template>

<script>
    import VSelect from 'vue-select'
    import 'vue-select/dist/vue-select.css'
    import FormInput from './Mixins/FormInput'
    import find from 'lodash/find'
    import { buildForSchema } from '../Support/FormSchema'

    export default {
        name: 'FormSingleChoiceInput',
        mixins: [FormInput],
        components: { VSelect },

        buildForSchema (obj, { key, field, type, value }) {
            const filteredValue = find(field.config.options, o => o.value === value)

            buildForSchema(obj, {
                key,
                field,
                type,
                value: filteredValue || type.default,
            })
        }
    }
</script>
