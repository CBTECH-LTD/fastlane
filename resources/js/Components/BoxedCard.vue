<template>
    <div class="rounded-lg bg-white border border-gray-300">
        <div v-if="$slots.hasOwnProperty('title')" class="p-4 border-b border-gray-300 text-gray-700 font-semibold text-lg tracking-wider">
            <slot name="title"/>
        </div>

        <div :class="{'p-6': !spaceless}">
            <slot/>
        </div>

        <div v-if="$slots.hasOwnProperty('footer')" class="p-4 border-t border-gray-300">
            <slot name="footer"/>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'BoxedCard',

        props: {
            spaceless: {
                type: Boolean,
                default: false,
            }
        }
    }
</script>
