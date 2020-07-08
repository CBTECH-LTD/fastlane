<template>
    <div class="w-full">
        <input type="text" class="w-full form-input" v-bind="$attrs" :value="value" @input="onInput">
    </div>
</template>

<script>
    export default {
        name: 'FormStringInput',
        inheritAttrs: false,

        inject: ['errors', 'isRequired'],

        props: {
            value: {
                type: String | null,
                required: true,
            }
        },

        methods: {
            onInput (ev) {
                this.$emit('input', ev.target.value)
            }
        }
    }
</script>
