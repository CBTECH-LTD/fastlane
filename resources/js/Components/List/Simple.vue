<template>
    <span>
        {{ value }}
    </span>
</template>

<script>
    import ListRenderer from '../Mixins/ListRenderer'

    export default {
        name: 'ListSimple',
        mixins: [ListRenderer],
    }
</script>
