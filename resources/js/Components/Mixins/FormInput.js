export default {
    inheritAttrs: false,
    inject: ['errors', 'isRequired'],

    props: {
        field: {
            type: Object,
            required: true,
        },
    },

    data: () => ({
        value: null,
    }),

    methods: {
        /**
         * @param {FormObject} formObject
         */
        commit (formObject) {
            formObject.put(this.field.name, this.field.value)
        },

        onInput (value) {
            this.field.value = value
        },
    },

    mounted () {
        // Make a copy of the `commit` method from this component
        // to the form field instance, because we need to be able
        // to run it from FormSchema instances.
        this.field.commit = this.commit
    }
}
