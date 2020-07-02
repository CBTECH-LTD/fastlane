<template>
    <div
        :href="item.href"
        @click="() => $emit('click', item)"
        @mouseenter="ev => $emit('mouseenter', ev)"
        @mouseleave="ev => $emit('mouseleave', ev)"
        class="relative w-full py-3 flex items-center font-semibold text-gray-500 hover:text-gray-700 transition-all ease-in-out duration-300 cursor-pointer opacity-75 hover:opacity-100">
        <span class="flex text-2xl justify-center w-16 opacity-75">
            <f-icon :name="item.icon"/>
        </span>
        <span class="text-sm">{{ item.label }}</span>
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
        }
    }
</script>

<style scoped>

</style>
