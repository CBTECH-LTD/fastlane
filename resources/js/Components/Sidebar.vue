<template>
    <div :style="containerStyle" class="min-h-screen bg-gray-200 border-r border-gray-300 py-12">
        <div class="h-screen overflow-y-auto">
            <div class="font-semibold text-gray-900 text-2xl tracking-tight px-8">
                <slot name="title"/>
            </div>
            <div v-if="shouldRenderDescription" class="font-normal text-gray-600 text-xs pt-4  px-8">
                <slot name="description"/>
            </div>
            <div class="my-4">
                <slot/>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Sidebar',
        props: {
            width: {
                type: String,
                default: '360px',
            }
        },

        computed: {
            containerStyle () {
                return {
                    width: this.width,
                }
            },

            shouldRenderDescription () {
                return !!this.$slots['description'] || !!this.$scopedSlots['description']
            }
        }
    }
</script>
