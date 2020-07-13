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
    import FormInput from '../Mixins/FormInput'
    import find from 'lodash/find'

    export default {
        name: 'SelectInput',
        mixins: [FormInput],
        components: { VSelect },

        methods: {
            commit (formObject) {
                formObject.put(this.field.name, this.field.value ? this.field.value.value : null)
            },
        },

        buildForSchema (obj, { field, type, value }) {
            const filteredValue = find(field.config.options, o => o.value === value)

            obj.value = filteredValue || field.default
        }
    }
</script>
