<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required">
        <template v-if="field.label" v-slot:label>{{ field.label }}</template>
        <div class="w-full">
            <textarea rows="4" class="w-full form-input" v-bind="$attrs" v-model="field.value"></textarea>
        </div>
    </f-form-field>
</template>

<script>
    import FormInput from '../Mixins/FormInput'

    export default {
        name: 'TextInput',
        mixins: [FormInput],
    }
</script>
