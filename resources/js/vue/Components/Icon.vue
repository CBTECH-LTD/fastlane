<template>
    <i v-bind="$attrs" :class="`la la-${name}`"></i>
</template>

<script>
    export default {
        name: 'Icon',

        props: {
            name: {
                type: String,
                required: true,
            },
        },
    }
</script>
