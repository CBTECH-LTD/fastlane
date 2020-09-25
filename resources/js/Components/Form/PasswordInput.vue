<template>
    <f-form-field :errors="$page.errors.get(field.attribute)" :required="field.required" :stacked="stacked">
        <template v-if="field.config.label" v-slot:label>{{ field.config.label }}</template>
        <div class="w-full">
            <input
                type="password"
                class="w-full form-input"
                v-bind="$attrs"
                :value="field.value"
                @input="onInput($event.target.value)"
            />
        </div>
    </f-form-field>
</template>

<script>
    import FormInput from '../Mixins/FormInput'

    export default {
        name: 'PasswordInput',
        mixins: [FormInput],
    }
</script>
