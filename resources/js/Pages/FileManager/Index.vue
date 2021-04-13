<template>
    <f-the-app-layout>
        <template v-slot:title>{{ items.meta.entry_type.plural_name }}</template>

        <f-file-manager
            :endpoint="items.links.fileManager"
            :csrf-token="$page.app.csrfToken"
            :selectable="false"
            :deletable="false"
            inline
        />
    </f-the-app-layout>
</template>

<script>
export default {
    name: 'FileManager.Index',
    props: {
        items: {
            required: true,
            type: Object,
        },
    },
}
</script>
