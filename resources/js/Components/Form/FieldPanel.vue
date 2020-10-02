<template>
    <f-boxed-card :data-panel="name" :icon="icon">
        <template v-if="$slots.label" v-slot:title><slot name="label" /></template>
        <template v-else-if="label.length" v-slot:title>{{ label }}</template>
        <template v-slot:actions>
            <slot name="actions" />
        </template>
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
