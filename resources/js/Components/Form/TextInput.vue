<template>
    <div class="w-full">
        <textarea rows="4" class="w-full form-input" v-bind="$attrs" v-model="field.value"></textarea>
    </div>
</template>

<script>
    import FormInput from '../Mixins/FormInput'

    export default {
        name: 'TextInput',
        mixins: [FormInput],
    }
</script>
