<template>
    <f-form-field :errors="$page.errors.get(field.attribute)" :required="field.config.required" :stacked="field.config.stacked">
        <template v-if="field.config.label" v-slot:label>
            {{ field.config.label }}
        </template>

        <div class="w-full">
            <f-button v-for="block in availableBlocks" :key="block.key" @click="() => addBlock(block)">
                {{ block.name }}
            </f-button>
        </div>
        <div v-for="block in blocks" :key="block.uuid" class="w-full my-2">
            <f-form-field-panel :name="block.uuid" :label="block.name">
                <template v-slot:actions>
                    <f-button @click="removeBlock(block)" variant="minimal" color="black">
                        <f-icon class="text-lg" name="trash"/>
                    </f-button>
                </template>
                <template v-for="field in block.schema.getAll()">
                    <component :is="field.component"
                               :field="field"
                               :required="field.config.required"
                               :aria-required="field.config.required"
                               :placeholder="field.config.placeholder"
                               :aria-placeholder="field.config.placeholder"
                               :form="block.schema"
                               @input="updateContent"/>
                </template>
            </f-form-field-panel>
        </div>
    </f-form-field>
</template>

<script>
import { v4 as uuidv4 } from 'uuid'
import { FormSchemaFactory } from '../../Support/FormSchema'
import each from 'lodash/each'
import map from 'lodash/map'
import FormInput from '../Mixins/FormInput'

export default {
    name: 'BlockEditorInput',
    mixins: [FormInput],

    data () {
        return {
            blocks: {},
        }
    },

    computed: {
        /**
         * Just map all available blocks to an object
         * so we can access it easily.
         */
        availableBlocks () {
            return this.field.config.blocks
        }
    },

    methods: {
        commit (formObject) {
            formObject.put(this.field.attribute, this.field.value)
        },

        addBlock (block) {
            const uuid = uuidv4()

            this.$set(this.blocks, uuid, {
                uuid,
                key: block.key,
                name: block.name,
                schema: new FormSchemaFactory(block.fields, {
                    id: uuidv4()
                }),
            })

            this.$nextTick(() => {
                this.updateContent()
            })

            return uuid
        },

        removeBlock (block) {
            this.$delete(this.blocks, block.uuid)

            this.$nextTick(() => {
                this.updateContent()
            })
        },

        updateContent () {
            const content = map(this.blocks, b => {
                return {
                    block: b.key,
                    data: b.schema.toFormObject(false).all()
                }
            })

            this.onInput(content)
        }
    },

    mounted () {
        if (this.field.value) {
            this.field.value.forEach(block => {
                const uuid = this.addBlock(this.availableBlocks[block.key])

                each(block.fields, (field) => {
                    this.blocks[uuid].schema.getField(field.attribute).value = field.value
                })
            })
        }
    }
}
</script>
