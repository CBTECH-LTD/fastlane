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
    import FormInput from '../Mixins/FormInput'

    export default {
        name: 'StringInput',
        mixins: [FormInput],
    }
</script>
