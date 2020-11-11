<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required">
        <template v-if="field.label" v-slot:label>{{ field.label }}</template>
        <div class="w-full">
            <input type="text"
                   class="w-full form-input"
                   v-bind="$attrs"
                   v-model="field.value"
            >
        </div>
    </f-form-field>
</template>

<script>
import moment from 'moment'
import FormInput from '../Mixins/FormInput'

export default {
    name: 'YearInput',
    mixins: [FormInput],

    data: () => ({
        //
    }),

    computed: {
        defaultPlaceholder () {
            return moment().format('Y')
        },
    },
}
</script>
