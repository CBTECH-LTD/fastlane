<template>
    <span class="block text-sm">
        <span v-if="extension.length" class="uppercase p-1 bg-indigo-300 text-indigo-700 rounded mr-2">
            {{ extension }}
        </span>
        <f-link as="a" :href="value ? value.url : null" target="_blank" class="text-sm">
            {{ value ? value.file : '' }}
        </f-link>
    </span>
</template>

<script>
import ListRenderer from '../Mixins/ListRenderer'

export default {
    name: 'ListFile',
    mixins: [ListRenderer],

    computed: {
        extension () {
            if (!this.value) {
                return ''
            }

            const splitted = this.value.file.split('.')

            if (splitted.length > 1) {
                return splitted[splitted.length - 1]
            }

            return ''
        }
    }
}
</script>
