<template>
    <div class="w-full flex items-center my-2">
        <f-list-item-action v-if="value === true" @click="() => onInput(false)" icon="toggle-on" color="green" title="Click to turn off"/>
        <f-list-item-action v-else @click="() => onInput(true)" icon="toggle-off" color="black" title="Click to turn on"/>
        <span class="ml-2">
            <slot/>
        </span>
    </div>
</template>

<script>
    export default {
        name: 'FormSwitchInput',
        inheritAttrs: false,

        inject: ['errors', 'isRequired'],

        props: {
            value: {
                type: Boolean,
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
