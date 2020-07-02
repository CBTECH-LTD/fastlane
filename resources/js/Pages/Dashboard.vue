<template>
    <f-the-app-layout>
        <template v-slot:sidebar>
            <f-sidebar>
                <template v-slot:title>Pages</template>
                <template v-slot:description>
                    Pages show interesting content to the visitor. Here you can add, edit, remove and publish pages.
                </template>
            </f-sidebar>
        </template>
    </f-the-app-layout>
</template>

<script>
    export default {
        name: 'Dashboard',
    }
</script>
