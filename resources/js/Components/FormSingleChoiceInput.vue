<template>
    <div class="w-full">
        <v-select
            class="form-input"
            :clearable="false"
            :options="config.options"
            :value="value"
            @input="onInput">
        </v-select>
    </div>
</template>

<script>
    import VSelect from 'vue-select'
    import 'vue-select/dist/vue-select.css'

    export default {
        name: 'FormSingleChoiceInput',
        components: { VSelect },
        inheritAttrs: false,
        inject: ['errors', 'isRequired'],

        props: {
            value: {
                type: Object | null | undefined,
                required: true,

            },
            config: {
                type: Object,
                required: true,
            },
        },

        methods: {
            onInput (value) {
                this.$emit('input', value)
            }
        }
    }
</script>
