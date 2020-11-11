<template>
    <f-form-field :errors="$page.errors.get(field.attribute)" :required="field.config.required" :stacked="stacked">
        <template v-if="field.config.label" v-slot:label>{{ field.config.label }}</template>
        <div class="w-full">
            <input ref="input"
                   type="text"
                   class="w-full form-input"
                   v-bind="$attrs"
                   :value="field.value"
                   @input="ev => onInput(ev.target.value)"
            />
        </div>
    </f-form-field>
</template>

<script>
import FormInput from '../Mixins/FormInput'

export default {
    name: 'SlugInput',
    mixins: [FormInput],

    data: () => ({
        maskInstance: null,
    }),

    mounted () {
        this.maskInstance = new Inputmask({
            regex: '[a-z0-9]+(?:-[a-z0-9]+)*',
            onBeforeMask: (value) => {
                return value.replace(/\s/g, '-').toLowerCase()
            }
        }).mask(this.$refs.input)

        if (this.field.config.baseField && this.form) {
            this.form.$on(`${this.field.config.baseField}:value-changed`, (value) => {
                this.maskInstance.setValue(value)
                this.onInput(this.maskInstance.el.value)
            })
        }
    }
}
</script>
