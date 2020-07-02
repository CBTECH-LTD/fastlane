<template>
    <div class="w-full flex items-center my-2">
        <input type="radio" class="form-radio" v-bind="$attrs" :checked="value" @input="onInput">
        <span class="ml-2">
            <slot/>
        </span>
    </div>
</template>

<script>
    export default {
        name: 'FormRadioInput',
        inheritAttrs: false,

        inject: ['errors', 'isRequired'],

        props: {
            value: {
                type: Boolean,
                required: true,
            },
        },

        methods: {
            onInput (ev) {
                this.$emit('input', ev.target.checked)
            }
        }
    }
</script>
