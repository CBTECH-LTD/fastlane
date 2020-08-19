<template>
    <div :class="containerClasses">
        <inertia-link
            :href="item.href"
            @click="() => $emit('click', item)"
            class="relative w-full py-3 flex items-center text-gray-600 hover:text-gray-700 transition-all ease-in-out duration-300 opacity-75 hover:opacity-100"
            :class="classes">
            <span class="flex text-2xl font-semibold justify-center w-16">
                <f-icon v-if="item.icon" :name="item.icon"/>
            </span>
            <span class="flex-1 text-sm font-medium opacity-100">{{ item.label }}</span>
        </inertia-link>
    </div>
</template>

<script>
import trimStart from 'lodash/trimStart'

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
            if (this.$page.app.requestUrl.startsWith(trimStart(this.item.href, this.$page.app.baseUrl))) {
                return 'text-gray-800  border-r-8 border-brand-500'
            }

            return 'bg-transparent'
        },

        containerClasses () {
            if (this.$page.app.requestUrl.startsWith(trimStart(this.item.href, this.$page.app.baseUrl))) {
                return 'bg-gray-100 border-t border-b border-gray-400'
            }

            return ''
        }
    }
}
</script>

<style scoped>

</style>
