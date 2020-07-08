<template>
    <div class="w-full">
        <input type="text"
               ref="datePicker"
               class="w-full form-input"
               :placeholder="defaultPlaceholder"
               v-bind="$attrs"
               :value="value"
        >
    </div>
</template>

<script>
    import flatpickr from 'flatpickr'
    import 'flatpickr/dist/themes/airbnb.css'
    import moment from 'moment'

    export default {
        name: 'FormDateTimeInput',
        inheritAttrs: false,
        inject: ['errors', 'isRequired'],

        props: {
            value: {
                type: String | null,
                required: true,
            },
            config: {
                type: Object,
                required: true,
            },
        },

        data: () => ({
            flatpickr: null,
        }),

        computed: {
            defaultPlaceholder () {
                return moment().format('YYYY-MM-DD HH:mm:ss')
            },

            saveFormat () {
                return this.config.enableTime
                    ? 'Y-m-d H:i:S'
                    : 'Y-m-d'
            }
        },

        methods: {
            onInput () {
                this.$emit('input', this.$refs.datePicker.value)
            }
        },

        mounted () {
            this.$nextTick(() => {
                this.flatpickr = flatpickr(this.$refs.datePicker, {
                    enableTime: this.config.enableTime,
                    enableSeconds: this.config.enableSeconds,
                    altInput: true,
                    altFormat: this.config.displayFormat,
                    dateFormat: this.saveFormat,
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
