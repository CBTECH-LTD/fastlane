<template>
    <f-boxed-card spaceless>
        <template v-if="$slots.hasOwnProperty('title')" v-slot:title>
            <slot name="title"/>
        </template>
        <div v-if="items.length === 0" class="p-8 flex text-center items-center justify-center text-base text-gray-700 font-semibold">
            <div class="w-full p-6">
                <div class="text-6xl text-orange-400 font-normal">
                    <f-icon name="exclamation-triangle"/>
                </div>
                <strong class="block text-gray-700 mb-2">
                    <slot name="empty-title">{{ $l('core.empty_lists') }}</slot>
                </strong>
                <span class="text-gray-600">
                    <slot name="empty-message"/>
                </span>
            </div>
        </div>
        <ul v-else class="list-none block w-full">
            <li v-for="item in items" :key="item.id" class="block w-full p-4 border-b border-gray-200 flex flex-wrap justify-between items-center">
                <div class="flex-grow">
                    <slot name="item-content" :item="item"/>
                </div>
                <div class="flex items-center justify-center w-full md:w-auto mt-6 md:mt-0">
                    <slot name="item-actions" :item="item"/>
                </div>
            </li>
        </ul>
        <template v-if="$slots.hasOwnProperty('footer')" v-slot:footer>
            <slot name="footer"/>
        </template>
    </f-boxed-card>
</template>

<script>

export default {
    name: 'List',

    props: {
        items: {
            type: Array,
            required: true,
        }
    }
}
</script>
