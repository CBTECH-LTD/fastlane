<template>
    <div class="mt-2 mb-4">
        <div class="relative block flex flex-wrap" :class="containerClasses">
            <span v-if="required" class="absolute left-0 top-0 -ml-4 text-red-600">*</span>
            <label v-if="$slots.hasOwnProperty('label') || $slots.hasOwnProperty('description')" class="block relative font-medium text-base text-gray-600 mb-4" :class="labelClasses">
                <slot name="label"/>
                <span class="block relative text-sm text-gray-500 font-normal mt-2 pr-4">
                    <slot name="description"/>
                </span>
            </label>
            <div :class="fieldContainerClasses">
                <div :class="fieldClasses">
                    <slot/>
                </div>
                <div class="w-full mt-2">
                    <span v-if="errors && errors.length" class="block my-1 text-sm text-red-600">
                        <span class="block" v-for="e in errors">{{ e }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'FormField',

    props: {
        stacked: {
            type: Boolean,
            default: false,
        },
        errors: {
            type: Array | undefined,
            default: () => [],
        },
        required: {
            type: Boolean,
            default: false,
        }
    },

    computed: {
        containerClasses () {
            if (this.stacked) {
                return 'flex-col'
            }

            return 'flex-row'
        },
        labelClasses () {
            let classes = ['w-full']

            if (!this.stacked) {
                classes.push('md:w-2/5', 'lg:w-2/6')
            }

            if (this.required) {
                classes.push('label--required')
            }

            return classes
        },
        fieldContainerClasses () {
            let base = 'w-full rounded'

            if (!this.stacked) {
                base += ' md:w-3/5 lg:w-4/6'
            }

            return base
        },
        fieldClasses () {
            let base = 'w-full'

            if (this.errors.length) {
                base += ' border border-red-400'
            }

            return base
        },
    }
}
</script>
