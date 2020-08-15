<template>
    <f-boxed-card :data-panel="name" :icon="icon">
        <template v-if="label.length" v-slot:title>{{ label }}</template>

        <slot/>
    </f-boxed-card>
</template>

<script>
    export default {
        name: 'FieldPanelInput',

        props: {
            name: {
                type: String,
                required: true,
            },
            label: {
                type: String,
                default: '',
            },
            icon: {
                type: String,
                default: '',
            },
        },
    }
</script>
