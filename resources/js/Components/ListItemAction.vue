<template>
    <f-button v-bind="attributes" @click="ev => $emit('click', ev)">
        <f-icon class="text-2xl" :name="icon"/>
    </f-button>
</template>

<script>

    export default {
        name: 'ListItemAction',
        inheritAttrs: false,

        props: {
            icon: {
                type: String,
                required: true,
            },
            color: {
                type: String,
                default: 'black',
            }
        },

        computed: {
            attributes () {
                return {
                    ...this.$attrs,
                    variant: 'minimal',
                    color: this.color,
                }
            }
        }
    }
</script>

<style scoped>

</style>
