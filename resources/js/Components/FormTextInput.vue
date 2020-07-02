<template>
    <div class="w-full">
        <textarea rows="4" class="w-full form-input" :class="{'border-purple-500': dirty}" v-bind="$attrs" :value="value" @input="onInput"></textarea>
    </div>
</template>

<script>
    export default {
        name: 'FormTextInput',
        inheritAttrs: false,

        inject: ['errors', 'isRequired'],

        props: {
            value: {
                type: String | null,
                required: true,
            },
            dirty: {
                type: Boolean,
                default: false,
            }
        },

        methods: {
            onInput (ev) {
                this.$emit('input', ev.target.value)
            }
        }
    }
</script>
