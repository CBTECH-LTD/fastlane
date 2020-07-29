<template>
    <form @submit="e => $emit('submit', e)">
        <f-boxed-card v-if="panelSlots.general.length" data-panel="general">
            <template v-for="slotName in panelSlots.general">
                <slot :name="slotName" />
            </template>
        </f-boxed-card>

        <template v-if="panelSlots.panels.length">
            <div v-for="slotName in panelSlots.panels" :key="slotName" class="w-full my-6">
                <slot :name="slotName" />
            </div>
        </template>
    </form>
</template>

<script>
    import each from 'lodash/each'

    export default {
        name: 'FormRoot',

        data: () => ({
            panelSlots: {
                general: [],
                panels: [],
            }
        }),

        mounted () {
            each(this.$slots, (items, key) => {
                if (key.startsWith('generalPanel__')) {
                    this.panelSlots.general.push(key)
                    return
                }

                this.panelSlots.panels.push(key)
            })
        }
    }
</script>
