<template>
    <f-the-app-layout>
        <template v-slot:title>{{ items.meta.entry_type.plural_name }}</template>
        <template v-slot:actions>
            <slot name="actions">
                <f-button v-if="items.links.create" :href="items.links.create" left-icon="plus" size="lg">{{ $l('core.add') }} {{ items.meta.entry_type.singular_name }}</f-button>
            </slot>
        </template>

        <slot name="before-table"/>

        <f-table-card :items="items.data" auto>
            <template v-slot:columns>
                <th v-for="field in listSchema" :key="field.name" class="table__column" :width="field.listWidth || 'auto'">
                    <span class="flex items-center">
                        {{ field.label }}
                        <f-button v-if="field.sortable" @click="orderBy(field)" variant="minimal" :color="orderField === field.name ? 'black' : 'gray'" class="ml-2">
                            <f-icon v-if="orderDirection === 'asc'" name="sort-alpha-down"/>
                            <f-icon v-if="orderDirection === 'desc'" name="sort-alpha-down-alt"/>
                        </f-button>
                    </span>
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

            <template v-slot:footer>
                <div class="flex items-center justify-center font-bold">
                    <f-button v-if="items.meta.currentPage > 1" :href="items.meta.firstPageUrl" color="brand" variant="outline" class="mx-1">
                        <f-icon name="step-backward"/>
                    </f-button>
                    <f-button v-if="items.meta.previousPageUrl" :href="items.meta.previousPageUrl" color="brand" variant="outline" class="mx-1">
                        <f-icon name="angle-left"/>
                    </f-button>
                    <span v-if="hasMoreBefore" class="mx-1 px-1 text-gray-700"><f-icon name="ellipsis-h"/></span>
                    <f-button v-for="page in items.meta.pageUrls" :key="page.number" :href="page.url" color="brand" :variant="page.isCurrent ? 'solid' : 'outline'" class="mx-1">{{ page.number }}</f-button>
                    <span v-if="hasMoreAfter" class="mx-1 px-1 text-gray-700"><f-icon name="ellipsis-h"/></span>
                    <f-button v-if="items.meta.nextPageUrl" :href="items.meta.nextPageUrl" color="brand" variant="outline" class="mx-1">
                        <f-icon name="angle-right"/>
                    </f-button>
                    <f-button v-if="items.meta.currentPage < items.meta.lastPage" :href="items.meta.lastPageUrl" color="brand" variant="outline" class="mx-1">
                        <f-icon name="step-forward"/>
                    </f-button>
                </div>
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
        hasMoreBefore () {
            if (this.items.meta.pageUrls.length === 0) {
                return false
            }

            return this.items.meta.pageUrls[0].number > 1
        },

        hasMoreAfter () {
            if (this.items.meta.pageUrls.length === 0) {
                return false
            }

            return this.items.meta.pageUrls[this.items.meta.pageUrls.length - 1].number < this.items.meta.lastPage
        },

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
                        [field.name]: value,
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
                    order: `${dir}${field.name}`,
                }
            })
        }
    },
}
</script>
