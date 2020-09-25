<template>
    <form @submit.prevent="e => $emit('submit', e)">
        <div v-for="panel in panelSlots" :key="panel.attribute" class="w-full my-6">
            <f-form-field-panel :name="panel.attribute" :label="panel.config.label" :icon="panel.config.icon">
                <template v-for="field in panel.fields">
                    <component :is="field.component"
                               :field="field"
                               :required="field.config.required"
                               :aria-required="field.config.required"
                               :placeholder="field.config.placeholder"
                               :aria-placeholder="field.config.placeholder"
                               :form="form"
                               stacked
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
            const panelKey = field.config.panel || 'default_panel'

            if (!this.panelSlots[panelKey]) {
                const panelConfig = panelKey !== 'default_panel'
                    ? find(this.panels, p => p.attribute === panelKey)
                    : { attribute: 'default_panel', config: { label: '', icon: '' }}

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
