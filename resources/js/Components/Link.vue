<template>
    <component :is="as" v-bind="$attrs" :class="classes" class="font-medium focus:outline-none focus:underline transition ease-in-out duration-500">
        <slot/>
    </component>
</template>

<script>
    export default {
        name: 'Link',
        inheritAttrs: false,
        props: {
            as: {
                default: 'inertia-link',
            },
            color: {
                type: String,
                default: 'brand',
            }
        },
        computed: {
            classes () {
                return `${this.colorClasses} hover:underline`
            },

            colorClasses () {
                if (this.color === 'white') {
                    return 'text-white hover:text-brand-300'
                }

                return `text-${this.color}-600 hover:text-${this.color}-700`
            }
        }
    }
</script>
