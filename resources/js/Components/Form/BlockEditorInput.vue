<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required" :stacked="field.label_stacked">
        <template v-if="field.label && field.label_visible" v-slot:label>
            {{ field.label }}
        </template>

        <div class="w-full">
            <f-button v-for="block in availableBlocks" :key="block.key" @click="() => addBlock(block)">
                {{ block.name }}
            </f-button>
        </div>
        <div v-for="(block, index) in blocks" :key="block.uuid" class="w-full my-2">
            <f-form-field-panel :name="block.uuid" :label="block.name" :ref="`block_${block.uuid}`">
                <template v-slot:actions>
                    <f-button @click="moveUp(index)" variant="minimal" color="black" :disabled="index === 0">
                        <f-icon class="text-lg" name="arrow-up" />
                    </f-button>
                    <f-button @click="moveDown(index)" variant="minimal" color="black" :disabled="index === blocks.length">
                        <f-icon class="text-lg" name="arrow-down" />
                    </f-button>
                    <f-button @click="removeBlock(index)" variant="minimal" color="black">
                        <f-icon class="text-lg" name="trash"/>
                    </f-button>
                </template>
                <template v-for="field in block.schema.getAll()">
                    <component :is="field.component"
                               :field="field"
                               :required="field.required"
                               :aria-required="field.required"
                               :placeholder="field.placeholder"
                               :aria-placeholder="field.placeholder"
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
            blocks: [],
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
            formObject.put(this.field.name, this.field.value)
        },

        addBlock (block) {
            const uuid = uuidv4()

            const blocksLength = this.blocks.push({
                uuid,
                key: block.key,
                name: block.name,
                schema: new FormSchemaFactory({}, block.fields),
            })

            this.$nextTick(() => {
                this.updateContent()
            })

            return { uuid, index: blocksLength - 1 }
        },

        removeBlock (blockIndex) {
            this.blocks.splice(blockIndex)

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
        },

        moveUp (index) {
            const moveUp = this.blocks[index]
            const moveDown = this.blocks[index - 1]

            this.$set(this.blocks, index - 1, moveUp)
            this.$set(this.blocks, index, moveDown)

            this.$nextTick(() => this.scrollToBlock(moveUp))
            this.updateContent()
        },

        moveDown (index) {
            const moveDown = this.blocks[index]
            const moveUp = this.blocks[index + 1]

            this.$set(this.blocks, index + 1, moveDown)
            this.$set(this.blocks, index, moveUp)

            this.$nextTick(() => this.scrollToBlock(moveDown))
            this.updateContent()
        },

        scrollToBlock (block ) {
            this.$refs[`block_${block.uuid}`][0].$el.scrollIntoView({ behavior: 'smooth' })
        }
    },

    mounted () {
        if (this.field.value) {
            this.field.value.forEach(block => {
                const { index } = this.addBlock(this.availableBlocks[block.key])

                each(block.fields, (field) => {
                    this.blocks[index].schema.getField(field.name).value = field.value
                })
            })
        }
    }
}
</script>
