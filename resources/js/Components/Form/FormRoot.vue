<template>
    <form @submit.prevent="e => $emit('submit', e)">
        <div v-for="panel in panelSlots" :key="panel.name" class="w-full my-6">
            <f-form-field-panel :name="panel.name" :label="panel.label" :icon="panel.icon">
                <template v-for="field in panel.fields">
                    <component :is="field.component"
                               :field="field"
                               :required="field.required"
                               :aria-required="field.required"
                               :placeholder="field.placeholder"
                               :aria-placeholder="field.placeholder"
                               :form="form"
                    ></component>
                </template>
            </f-form-field-panel>
        </div>
    </form>
</template>

<script>
    import each from 'lodash/each'
    import find from 'lodash/find'

    export default {
        name: 'FormRoot',

        props: {
            panels: {
                type: Object | Array,
                required: true,
            },
            form: {
                type: Object,
                required: true,
            }
        },

        data: () => ({
            panelSlots: {},
        }),

        mounted () {
            each(this.form.getAll(), (field) => {
                const panelKey = field.panel || 'default_panel'

                if (!this.panelSlots[panelKey]) {
                    const panelConfig = panelKey !== 'default_panel'
                        ? find(this.panels, p => p.name === panelKey)
                        : { name: 'default_panel', label: '', icon: '', }

                    this.$set(this.panelSlots, panelKey, {
                        ...panelConfig,
                        fields: [],
                    })
                }

                this.panelSlots[panelKey].fields.push(field)
            })
        }
    }
</script>
