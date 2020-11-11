<template>
    <span>
        {{ formattedValue }}
    </span>
</template>

<script>
import ListRenderer from '../Mixins/ListRenderer'
import moment from 'moment'

export default {
    name: 'ListDateTime',
    mixins: [ListRenderer],

    computed: {
        formattedValue () {
            return moment(this.value).format(this.field.config.momentDisplayFormat)
        }
    },
}
</script>
