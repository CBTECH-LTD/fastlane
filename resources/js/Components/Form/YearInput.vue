<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required">
        <template v-if="field.label" v-slot:label>{{ field.label }}</template>
        <div class="w-full">
            <input type="text"
                   ref="datePicker"
                   class="w-full form-input"
                   :placeholder="defaultPlaceholder"
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
    name: 'YearInput',
    mixins: [FormInput],

    data: () => ({
        flatpickr: null,
    }),

    computed: {
        defaultPlaceholder () {
            return moment().format('YYYY')
        },
    },

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
                altFormat: 'YYYY',
                dateFormat: 'Y',
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
