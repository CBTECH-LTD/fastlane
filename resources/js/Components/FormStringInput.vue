<template>
    <div class="w-full">
        <input
            type="text"
            class="w-full form-input"
            v-bind="$attrs"
            v-model="field.value"
        />
    </div>
</template>

<script>
    import FormInput from './Mixins/FormInput'

    export default {
        name: 'FormStringInput',
        mixins: [FormInput],
    }
</script>
