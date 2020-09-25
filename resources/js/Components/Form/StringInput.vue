<template>
    <f-form-field :errors="$page.errors.get(field.attribute)" :required="field.config.required" :stacked="stacked">
        <template v-if="field.config.label" v-slot:label>{{ field.config.label }}</template>
        <div class="w-full">
            <input
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
    name: 'StringInput',
    mixins: [FormInput],
}
</script>
