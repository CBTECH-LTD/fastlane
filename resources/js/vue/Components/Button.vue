<template>
    <component :is="renderAs" :class="classes" v-bind="attributes" @click="onClick">
        <span v-if="leftIcon.length" class="btn-icon btn-icon-l" :class="{'opacity-0': loading}">
            <f-icon :name="leftIcon"></f-icon>
        </span>
        <span :class="{'opacity-0': loading}">
            <slot/>
        </span>
        <span v-if="rightIcon.length" class="btn-icon btn-icon-r" :class="{'opacity-0': loading}">
            <f-icon :name="rightIcon"></f-icon>
        </span>
        <span v-if="loading" class="absolute top-0 left-0 w-full h-full flex items-center justify-center">
            <f-spinner color="info"></f-spinner>
        </span>
    </component>
</template>

<script>

    export default {
        name: 'Button',
        inheritAttrs: false,

        props: {
            external: {
                type: Boolean,
                default: false,
            },
            loading: {
                type: Boolean,
                default: false,
            },
            disabled: {
                type: Boolean,
                default: false,
            },
            submit: {
                type: Boolean,
                default: false,
            },
            color: {
                type: String,
                default: 'brand'
            },
            variant: {
                type: String,
                default: 'solid',
            },
            size: {
                type: String,
                default: 'base'
            },
            full: {
                type: Boolean,
                default: false
            },
            leftIcon: {
                type: String,
                default: ''
            },
            rightIcon: {
                type: String,
                default: ''
            }
        },

        computed: {
            renderAs () {
                if (this.$attrs.href) {
                    if (this.external) {
                        return 'a'
                    }

                    return 'inertia-link'
                }

                return 'button'
            },

            attributes () {
                if (this.renderAs === 'inertia-link' || this.renderAs === 'a') {
                    return {
                        ...this.$attrs,
                        disabled: this.isDisabled
                    }
                }

                return {
                    ...this.$attrs,
                    type: this.type,
                    disabled: this.isDisabled
                }
            },

            type () {
                return this.submit ? 'submit' : 'button'
            },

            classes () {
                let classes = [
                    'relative',
                    'btn',
                    `btn-${this.color}-${this.variant}`,
                    `btn-${this.size}`
                ]

                if (this.full) {
                    classes.push('w-full')
                }

                if (this.loading) {
                    classes.push('btn-loading')
                }

                if (this.isDisabled) {
                    classes.push('btn-disabled')
                }

                return classes
            },

            isDisabled () {
                return this.disabled || this.loading
            },
        },

        methods: {
            onClick (event) {
                if (this.$attrs.href && this.isDisabled) {
                    event.preventDefault()
                }

                this.$emit('click', event)
            },
        }
    }
</script>
