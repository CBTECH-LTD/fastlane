<template>
    <div class="block mb-2">
        <inertia-link
            :href="item.href"
            @click="() => $emit('click', item)"
            class="relative py-2 px-4 flex items-center transition-all ease-in-out duration-300 rounded-lg"
            :class="classes">
            <span class="flex text-2xl font-semibold justify-start w-8 opacity-75">
                <f-icon v-if="item.icon" :name="item.icon"/>
            </span>
            <span class="flex-1 text-sm font-medium">{{ item.label }}</span>
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
                return 'text-brand-200 bg-brand-700'
            }

            return 'bg-transparent text-gray-600 hover:text-brand-600 hover:bg-brand-300'
        },
    }
}
</script>

<style scoped>

</style>
