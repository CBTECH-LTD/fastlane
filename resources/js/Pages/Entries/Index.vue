<template>
    <f-the-app-layout>
        <template v-slot:title>{{ entryType.plural_name }}</template>
        <template v-slot:actions>
            <f-button v-if="links.create" :href="links.create" left-icon="plus" size="lg">Add {{ entryType.singular_name }}</f-button>
        </template>

        <f-table-card :items="items.data" auto>
            <template v-slot:columns>
                <th v-for="field in listSchema" :key="field.name" class="table__column" :width="field.listWidth || 'auto'">
                    {{ field.label }}
                </th>
                <th width="80"></th>
            </template>
            <template v-slot:item="{ item }">
                <td v-for="field in listSchema" :key="field.name" class="table__cell">
                    <slot :name="`item-content-${field.name}`" :field="field" :item="item" :value="item.attributes[field.name]">
                        <component :is="field.component"
                                   :type="field.type"
                                   :name="field.name"
                                   :label="field.label"
                                   :config="field.config"
                                   :value="item.attributes[field.name]"
                                   :loading="isPerformingActionFor[item.id]"
                                   @input="value => onInput(item, field, value)"
                        ></component>
                    </slot>
                </td>
                <td class="table__cell">
                    <div class="w-full h-full flex items-center justify-end">
                        <!-- Edit -->
                        <f-list-item-action v-if="item.links.self" :href="item.links.self" icon="pencil-alt" title="Edit"/>
                    </div>
                </td>
            </template>
        </f-table-card>
    </f-the-app-layout>
</template>

<script>

import ListSchema from '../../Support/ListSchema'

export default {
    name: 'Entries.Index',

    props: {
        links: {
            required: true,
            type: Object,
        },
        items: {
            required: true,
            type: Object,
        },
        entryType: {
            required: true,
            type: Object,
        }
    },

    data () {
        return {
            isPerformingActionFor: {},
            listSchema: new ListSchema(this.entryType.schema),
        }
    },

    methods: {
        async onInput (item, field, value) {
            if (!this.isPerformingActionFor[item.id]) {
                this.$set(this.isPerformingActionFor, item.id, true)

                try {
                    await this.$inertia.patch(item.links.self, {
                        [field.name]: value,
                    })
                } catch {}

                this.$set(this.isPerformingActionFor, item.id, false)
            }
        }
    },
}
</script>
