<template>
    <input
        type="hidden"
        v-bind="$attrs"
        v-model="field.value"
    />
</template>

<script>
import FormInput from '../Mixins/FormInput'

export default {
    name: 'HiddenInput',
    mixins: [FormInput],
}
</script>
