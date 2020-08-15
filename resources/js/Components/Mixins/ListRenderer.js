export default {
    props: {
        value: {
            required: true,
        },
        type: {
            type: String,
            required: true,
        },
        name: {
            type: String,
            required: true,
        },
        label: {
            type: String,
            required: true,
        },
        config: {
            type: Object | Array,
            required: true,
        },
        loading: {
            type: Boolean,
            default: false,
        }
    },
}
