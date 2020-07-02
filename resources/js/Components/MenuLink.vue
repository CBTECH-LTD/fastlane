<template>
    <inertia-link
        :href="item.href"
        @click="() => $emit('click', item)"
        class="relative w-full py-3 flex items-center text-gray-600 hover:text-gray-700 transition-all ease-in-out duration-300 opacity-75 hover:opacity-100"
        :class="classes">
        <span class="flex text-2xl font-semibold justify-center w-16">
            <f-icon :name="item.icon"/>
        </span>
        <span class="text-sm font-medium opacity-100">{{ item.label }}</span>
    </inertia-link>
</template>

<script>

    export default {
        name: 'MenuLink',
        props: {
            item: {
                type: Object,
                required: true,
            }
        },
        computed: {
            classes () {
                if (`${this.$page.app.baseUrl}/${this.$page.app.requestUrl}` === this.item.href) {
                    return 'text-gray-800'
                }

                return ''
            }
        }
    }
</script>

<style scoped>

</style>
