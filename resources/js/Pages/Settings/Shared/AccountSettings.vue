<template>
    <f-the-app-layout>
        <template v-slot:page-sidebar>
            <f-sidebar>
                <template v-slot:title>Account Settings</template>
                <template v-slot:description>
                    Here you can setup your profile, change your password and manage API tokens.
                </template>

                <div class="flex-grow overflow-y-auto overflow-x-hidden custom-scroll">
                    <f-navigation-list class="my-6" :items="sidebarMenu"/>
                </div>
            </f-sidebar>
        </template>

        <div class="px-12 my-12">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl text-gray-700 font-extrabold">
                    <slot name="title"/>
                </h1>
                <div class="flex items-center">
                    <slot name="actions"/>
                </div>
            </div>

            <p class="text-base text-gray-600 w-full my-8">
                <slot name="description" />
            </p>

            <slot />
        </div>
    </f-the-app-layout>
</template>

<script>
export default {
    name: 'UserSettings',

    props: {
        sidebarMenu: {
            type: Array,
            required: true,
        },
    },
}
</script>
