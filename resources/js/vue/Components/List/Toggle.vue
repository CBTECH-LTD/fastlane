<template>
    <span>
        <div class="w-full flex items-center my-2">
            <f-list-item-action v-if="value === true" @click="() => onToggle(false)" icon="toggle-on" color="green" :title="$l('core.fields.toggle.turn_on')" :loading="loading"/>
            <f-list-item-action v-else @click="() => onToggle(true)" icon="toggle-off" color="black" :title="$l('core.fields.toggle.turn_off')" :loading="loading"/>
        </div>
    </span>
</template>

<script>
import ListRenderer from '../Mixins/ListRenderer'

export default {
    name: 'ListSimple',
    mixins: [ListRenderer],

    methods: {
        onToggle (value) {
            this.$emit('input', value)
        }
    }
}
</script>
