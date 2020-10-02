<template>
    <span class="block text-sm">
        <template v-if="files.length === 1">
            <span v-if="files[0].extension.length" class="uppercase p-1 bg-indigo-300 text-indigo-700 rounded mr-2">
                {{ files[0].extension }}
            </span>
            <f-link as="a" :href="files[0].url" target="_blank" class="text-sm">
                {{ files[0].file }}
            </f-link>
        </template>
        <template v-else>
            <span>{{ $lc('core.fields.file.selected', files.length) }}</span>
        </template>
    </span>
</template>

<script>
import map from 'lodash/map'
import ListRenderer from '../Mixins/ListRenderer'

export default {
    name: 'ListFile',
    mixins: [ListRenderer],

    computed: {
        files () {
            return map(this.value || [], item => ({
                ...item,
                extension: this.getExtension(item.file)
            }))
        },
    },

    methods: {
        getExtension (file) {
            if (!file) {
                return ''
            }

            const split = file.split('.')

            if (split.length > 1) {
                return split[split.length - 1]
            }

            return ''
        }
    }
}
</script>
