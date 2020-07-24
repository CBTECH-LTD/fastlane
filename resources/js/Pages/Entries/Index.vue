<template>
    <f-the-app-layout>
        <template v-slot:title>{{ entryType.plural_name }}</template>
        <template v-slot:actions>
            <f-button v-if="links.create" :href="links.create" left-icon="plus" size="lg">Add {{ entryType.singular_name }}</f-button>
        </template>

        <f-table-card :items="items.data">
            <template v-slot:columns>
                <th v-for="field in listSchema" :key="field.name" class="table__column" :width="field.config.listWidth > 0 ? field.config.listWidth : 'auto'">
                    {{ field.label }}
                </th>
                <th width="160"></th>
            </template>
            <template v-slot:item="{ item }">
                <td v-for="field in listSchema" :key="field.name" class="table__cell">
                    <slot :name="`item-content-${field.name}`" :field="field" :item="item" :value="item.attributes[field.name]">
                        <component :is="field.component"
                                   :type="field.type"
                                   :name="field.name"
                                   :label="field.label"
                                   :config="field.config"
                                   :value="item.attributes[field.name]">
                        </component>
                    </slot>
                </td>
                <td class="table__cell">
                    <div class="w-full h-full flex items-center justify-end">
                        <!-- Activate / Deactivate -->
                        <template v-if="item.attributes.hasOwnProperty('is_active')">
                            <f-list-item-action v-if="item.attributes.is_active" @click="toggleItemActivation(item, false)" icon="toggle-on" color="green" title="Click to deactivate" :loading="isUpdatingActivationStateFor[item.id]"/>
                            <f-list-item-action v-else @click="toggleItemActivation(item, true)" icon="toggle-off" color="black" title="Click to activate" :loading="isUpdatingActivationStateFor[item.id]"/>
                        </template>
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
                isUpdatingActivationStateFor: {},
                listSchema: new ListSchema(this.entryType.schema),
            }
        },

        methods: {
            async toggleItemActivation (item, state) {
                if (!this.isUpdatingActivationStateFor[item.id]) {
                    this.$set(this.isUpdatingActivationStateFor, item.id, true)

                    try {
                        await this.$inertia.patch(item.links.self, {
                            is_active: state,
                        })
                    } catch {}

                    this.$set(this.isUpdatingActivationStateFor, item.id, false)
                }
            }
        },
    }
</script>
