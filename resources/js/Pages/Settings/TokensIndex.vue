<template>
    <AccountSettings :sidebar-menu="sidebarMenu">
        <template v-slot:title>{{ $l('core.account_settings.personal_access_tokens_title') }}</template>
        <template v-slot:description>
            {{ $l('core.account_settings.personal_access_tokens_description') }}
        </template>
        <template v-slot:actions>
            <f-button :href="items.links.create" color="black">
                {{ $l('core.account_settings.personal_access_tokens_new_button') }}
            </f-button>
        </template>

        <div v-if="newAccessToken" class="text-base text-gray-800 bg-green-200 border border-green-300 rounded mb-4 p-4">
            {{ $l('core.account_settings.personal_access_tokens_copy_message') }}
        </div>

        <f-boxed-card spaceless>
            <div v-for="token in items.data" :key="token.id" class="p-4 border-b border-gray-200 flex flex-wrap items-center">
                <div class="flex-grow">
                    <div class="text-base text-gray-700 font-semibold">{{ token.attributes.name }}</div>
                    <div class="text-sm text-gray-500 font-normal italic">{{ token.attributes.abilities.join(', ') }}</div>
                </div>
                <div v-if="token.attributes.last_used_at" class="flex-shrink text-sm text-gray-500 font-normal">
                    {{ $l('core.account_settings.personal_access_tokens_last_used_at') }} {{ token.attributes.last_used_at }}
                </div>
                <div class="flex-shrink flex items-center">
                    <f-button :href="items.links.create" color="danger" size="sm" variant="outline">
                        {{ $l('core.account_settings.personal_access_tokens_revoke') }}
                    </f-button>
                </div>
                <div v-if="newAccessToken && parseInt(newAccessToken.accessToken.id) === parseInt(token.id)"
                     class="flex items-center w-full mt-4 p-1 bg-indigo-100 border border-indigo-300 text-indigo-700 text-left text-xs font-semibold rounded">
                    <f-icon class="text-green-500" name="check"/>
                    <span class="mx-2">{{ newAccessToken.plainTextToken }}</span>
                </div>
            </div>

            <!-- Show a message if user has no token -->
            <div v-if="items.data.length === 0" class="text-gray-600 text-base text-center italic font-normal py-8">
                {{ $l('core.account_settings.personal_access_tokens_empty') }}
            </div>
        </f-boxed-card>
    </AccountSettings>
</template>

<script>
import AccountSettings from './Shared/AccountSettings'

export default {
    name: 'TokensIndex',
    components: { AccountSettings },

    props: {
        items: {
            type: Object,
            required: true,
        },
        sidebarMenu: {
            type: Array,
            required: true,
        },
        newAccessToken: {
            type: Object | null,
            required: false,
            default: null,
        }
    },
}
</script>
