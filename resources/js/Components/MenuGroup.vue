<template>
    <div class="pb-4">
        <div class="relative w-full py-3 flex items-center text-gray-600 opacity-100 menu-group__label">
            <span class="flex-shrink bg-gray-300 text-sm font-semibold uppercase tracking-wider z-10 ml-12 px-4">{{ item.label }}</span>
        </div>
        <f-navigation-list class="transition-all duration-300" :items="item.children" />
    </div>
</template>

<script>

export default {
    name: 'MenuGroup',
    props: {
        item: {
            type: Object,
            required: true,
        }
    },
}
</script>

<style scoped>
.menu-group__label::before {
    @apply border-t border-dashed border-gray-400;
    content: ' ';
    display: block;
    position: absolute;
    width: 100%;
}
</style>
