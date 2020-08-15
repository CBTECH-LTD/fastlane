<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required">
        <template v-if="field.label" v-slot:label>{{ field.label }}</template>
        <div class="w-full flex items-center my-2">
            <input type="radio" class="form-radio" v-bind="$attrs" :checked="field.value" @input="ev => onInput(ev.target.checked)">
            <span class="ml-2">
                <slot/>
            </span>
        </div>
    </f-form-field>
</template>

<script>
    import FormInput from '../Mixins/FormInput'

    export default {
        name: 'RadioInput',
        mixins: [FormInput],
    }
</script>
