<template>
    <f-the-app-layout>
        <template v-slot:title>{{ items.meta.entry_type.plural_name }}</template>
        <template v-slot:actions>
            <slot name="actions">
                <f-button v-if="items.links.create" :href="items.links.create" left-icon="plus" size="lg">{{ $l('core.add') }}</f-button>
            </slot>
        </template>

        <slot name="before-table"/>

        <f-table-card :items="items.data" auto>
            <template v-slot:columns>
                <th v-for="field in listSchema" :key="field.attribute" class="table__column" :width="field.config.listing.colWidth || 'auto'">
                    <span class="flex items-center">
                        {{ field.config.label }}
                        <f-button v-if="field.config.sortable" @click="orderBy(field)" variant="minimal" :color="orderField === field.attribute ? 'black' : 'gray'" class="ml-2">
                            <f-icon v-if="orderDirection === 'asc'" name="sort-alpha-down"/>
                            <f-icon v-if="orderDirection === 'desc'" name="sort-alpha-down-alt"/>
                        </f-button>
                    </span>
                </th>
                <th width="80"></th>
            </template>
            <template v-slot:item="{ item }">
                <td v-for="field in listSchema" :key="field.attribute" class="table__cell">
                    <slot :name="`item-content-${field.attribute}`" :field="field" :item="item" :value="item.attributes[field.attribute]">
                        <component :is="field.component"
                                   :field="field"
                                   :value="item.attributes[field.attribute].value"
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

            <template v-slot:footer>
                <f-paginator :meta="items.meta"/>
            </template>
        </f-table-card>

        <slot name="after-table"/>
    </f-the-app-layout>
</template>

<script>
import ListSchema from '../../Support/ListSchema'

export default {
    name: 'Entries.Index',

    props: {
        items: {
            type: Object,
            required: true,
        },
    },

    data () {
        return {
            isPerformingActionFor: {},
            listSchema: new ListSchema(this.items.meta.entry_type.schema),
        }
    },

    computed: {
        orderField () {
            return this.items.meta.order
                ? this.items.meta.order.replace(/^-/, '')
                : ''
        },

        orderDirection () {
            return this.items.meta.order && this.items.meta.order.startsWith('-')
                ? 'desc'
                : 'asc'
        }
    },

    methods: {
        async onInput (item, field, value) {
            if (!this.isPerformingActionFor[item.id]) {
                this.$set(this.isPerformingActionFor, item.id, true)

                try {
                    await this.$inertia.patch(item.links.self, {
                        [field.attribute]: value,
                    })
                } catch {}

                this.$set(this.isPerformingActionFor, item.id, false)
            }
        },

        orderBy (field) {
            const dir = field.name === this.orderField && this.orderDirection === 'asc'
                ? '-'
                : ''

            this.$inertia.visit(this.items.meta.firstPageUrl, {
                data: {
                    order: `${dir}${field.attribute}`,
                }
            })
        }
    },
}
</script>
