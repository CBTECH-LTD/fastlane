<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required">
        <template v-if="field.label" v-slot:label>{{ field.label }}</template>
        <div class="w-full flex items-center my-2">
            <f-list-item-action v-if="field.value === true" @click="() => onInput(false)" icon="toggle-on" color="green" :title="$l('core.fields.toggle.turn_off')"/>
            <f-list-item-action v-else @click="() => onInput(true)" icon="toggle-off" color="black" :title="$l('core.fields.toggle.turn_on')"/>
            <span class="ml-2">
                <slot/>
            </span>
        </div>
    </f-form-field>
</template>

<script>
  import FormInput from '../Mixins/FormInput'

  export default {
    name: 'ToggleInput',
    mixins: [FormInput],
  }
</script>
