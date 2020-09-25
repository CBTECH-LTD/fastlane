export default {
    props: {
        field: {
            type: Object,
            required: true,
        },
        value: {
            required: true,
        },
        loading: {
            type: Boolean,
            default: false,
        }
    },
}
