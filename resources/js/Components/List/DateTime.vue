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
                console.log(this.value, this.config.displayFormat)
                return moment(this.value).format(this.config.displayFormat)
            }
        },
    }
</script>
