<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required">
        <template v-if="field.label" v-slot:label>{{ field.label }}</template>
        <div class="w-full relative">
            <div class="absolute flex items-center justify-center top-0 left-0 h-full bg-gray-200 text-gray-600 text-sm font-semibold border border-gray-400 p-2 rounded-l">
                {{ field.config.currency }}
            </div>
            <input ref="input"
                   type="text"
                   class="w-full form-input pl-12"
                   v-bind="$attrs"
                   v-model="field.value"
            />
        </div>
    </f-form-field>
</template>

<script>
import Inputmask from 'inputmask'
import FormInput from '../Mixins/FormInput'

export default {
    name: 'CurrencyInput',
    mixins: [FormInput],

    data () {
        return {
            maskInstance: null,
        }
    },

    methods: {
        /**
         * @param {FormObject} formObject
         */
        commit (formObject) {
            const val = this.maskInstance.unmaskedvalue() * 100

            formObject.put(this.field.name, val)
        },

        buildOptions () {
            const opts = {}

            if (this.field.config.min) {
                opts.min = this.field.config.min
            }

            if (this.field.config.max) {
                opts.max = this.field.config.max
            }
        }
    },

    mounted () {
        this.maskInstance = new Inputmask(this.field.config.mask, {
            rightAlign: false,
            numericInput: true,
            radixPoint: '.',
            min: this.field.config.min,
            max: this.field.config.max,
            allowMinus: this.field.config.min < 0,
            onBeforeMask (value, options) {
                return (value / 100).toFixed(2)
            }
        }).mask(this.$refs.input)
    }
}
</script>
