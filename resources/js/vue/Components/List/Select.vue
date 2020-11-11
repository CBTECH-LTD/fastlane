<template>
    <span class="block flex flex-wrap items-center justify-start">
        <span v-for="item in items" :key="item.value" class="p-1 text-xs text-info-800 font-semibold border border-info-800 rounded mr-1">
            {{ item.label }}
        </span>
    </span>
</template>

<script>
    import ListRenderer from '../Mixins/ListRenderer'
    import isString from 'lodash/isString'

    export default {
        name: 'ListSelect',
        mixins: [ListRenderer],

        computed: {
            items () {
                return isString(this.value)
                    ? [{ label: this.value, value: this.value }]
                    : this.value
            }
        }
    }
</script>
