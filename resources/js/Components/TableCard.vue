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
                    <slot name="empty-title">Nothing to show here.</slot>
                </strong>
                <span class="text-gray-600 mb-8">
                    <slot name="empty-message"/>
                </span>
            </div>
        </div>
        <table v-else class="w-full table-auto" :class="auto ? 'table-auto' : 'table-fixed'">
            <thead>
            <tr class="table__column-group">
                <slot name="columns"/>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in items" :key="item.id" class="table__row">
                <slot name="item" :item="item"/>
            </tr>
            </tbody>
            <tfoot>
            <tr class="table__column-group">
                <slot name="columns"/>
            </tr>
            </tfoot>
        </table>
        <template v-if="$slots.hasOwnProperty('footer')" v-slot:footer>
            <slot name="footer"/>
        </template>
    </f-boxed-card>
</template>

<script>

export default {
    name: 'TableCard',

    props: {
        items: {
            type: Array,
            required: true,
        },
        auto: {
            type: Boolean,
            default: false,
        }
    },
}
</script>
