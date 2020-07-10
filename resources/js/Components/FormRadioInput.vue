<template>
    <div class="w-full flex items-center my-2">
        <input type="radio" class="form-radio" v-bind="$attrs" :checked="field.value" @input="ev => onInput(ev.target.checked)">
        <span class="ml-2">
            <slot/>
        </span>
    </div>
</template>

<script>
    import FormInput from './Mixins/FormInput'

    export default {
        name: 'FormRadioInput',
        mixins: [FormInput],
    }
</script>
