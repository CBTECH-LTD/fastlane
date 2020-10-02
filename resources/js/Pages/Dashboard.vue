<template>
    <f-the-app-layout>
        <template v-slot:title>Dashboard</template>

        <div v-if="widgets.length > 0" class="my-8">
            <div v-for="(widget, index) in widgets" :key="`${widget.id}-${index}`">
                <component :is="widget.component" v-bind="widget.data"></component>
            </div>
        </div>
    </f-the-app-layout>
</template>

<script>
export default {
    name: 'Dashboard',
    props: {
        widgets: {
            type: Array,
            default: () => [],
        }
    }
}
</script>
