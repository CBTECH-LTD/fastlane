<template>
    <div class="relative my-6">
        <template v-for="(item, index) in items">
            <div ref="items" :key="`link_${index}`">
                <component
                    :is="getItemComponent(item)"
                    :item="item"
                    @click="onItemClicked(item)"></component>
            </div>
        </template>
    </div>
</template>

<script>
    import CBIcon from './Icon'
    import CBMenuLink from './MenuLink'
    import CBMenuGroup from './MenuGroup'

    export default {
        name: 'NavigationList',
        components: { CBIcon, CBMenuLink, CBMenuGroup },

        data () {
            return {
                items: this.$page.menu,
                label: {
                    state: false,
                    content: '',
                    position: {
                        top: '100px',
                        left: 400,
                    }
                }
            }
        },

        methods: {
            getItemComponent (item) {
                if (item.type === 'link') {
                    return CBMenuLink
                }

                if (item.type === 'group') {
                    return CBMenuGroup
                }

                return null
            },
            onItemClicked (item) {
                this.$emit('click', { item })
            }
        }
    }
</script>
