<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required">
        <template v-if="field.label" v-slot:label>{{ field.label }}</template>
        <div class="w-full relative">
            <input ref="input"
                   type="text"
                   class="w-full form-input"
                   v-bind="$attrs"
                   :value="field.value"
                   @input="onInput"
            />
        </div>
    </f-form-field>
</template>

<script>
import Inputmask from 'inputmask'
import FormInput from '../Mixins/FormInput'

export default {
    name: 'NumberInput',
    mixins: [FormInput],

    data () {
        return {
            maskInstance: null,
        }
    },

    methods: {
        /**
         * @param value
         */
        onInput () {
            this.field.value = this.maskInstance.unmaskedvalue()
            this.field.emitValueChanged(this.field.value)
        },

        buildDecimalsMask () {
            if (this.field.config.decimals === 0) {
                return ''
            }

            const decimals = []
            for (let i = 0; i < this.field.config.decimals; i++) {
                decimals.push('0')
            }

            return ',' + decimals.join('')
        },
        buildIntegerMask () {
            if (this.field.config.max) {
                return `(9){${this.field.config.max.length}}`
            }

            return '(999){+|1}'
        }
    },

    mounted () {
        this.maskInstance = new Inputmask('numeric', {
            rightAlign: false,
            digits: this.field.config.decimals,
            allowMinus: this.field.config.min < 0,
            min: this.field.config.min,
            max: this.field.config.max,
            positionCaretOnClick: 'radixFocus',
            radixPoint: ',',
            _radixDance: true,
            numericInput: true,
            placeholder: '0',
            definitions: {
                '0': {
                    validator: '[0-9\uFF11-\uFF19]'
                }
            }
        }).mask(this.$refs.input)
    }
}
</script>
