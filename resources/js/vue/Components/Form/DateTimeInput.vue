<template>
    <f-form-field :errors="$page.errors.get(field.attribute)" :required="field.config.required" :stacked="stacked">
        <template v-if="field.config.label" v-slot:label>{{ field.config.label }}</template>
        <div class="w-full">
            <input type="text"
                   ref="datePicker"
                   class="w-full form-input"
                   v-bind="$attrs"
                   :value="field.value"
            >
        </div>
    </f-form-field>
</template>

<script>
import flatpickr from 'flatpickr'
import 'flatpickr/dist/themes/airbnb.css'
import moment from 'moment'
import FormInput from '../Mixins/FormInput'

export default {
    name: 'DateTimeInput',
    mixins: [FormInput],

    data: () => ({
        flatpickr: null,
    }),

    methods: {
        onInput () {
            this.field.value = this.$refs.datePicker.value

            this.$emit('input', this.field.value)
        }
    },

    mounted () {
        this.$nextTick(() => {
            this.flatpickr = flatpickr(this.$refs.datePicker, {
                enableTime: this.field.config.enableTime,
                enableSeconds: this.field.config.enableSeconds,
                altInput: true,
                altFormat: this.field.config.displayFormat,
                dateFormat: this.field.config.saveFormat,
                onClose: this.onInput,
                onChange: this.onInput,
                allowInput: true,
                time_24hr: true,
                locale: {
                    firstDayOfWeek: 0,
                }
            })
        })
    },

    beforeDestroy () {
        this.flatpickr.destroy()
    }
}
</script>
