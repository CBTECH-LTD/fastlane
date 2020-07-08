<template>
    <div class="w-full">
        <f-trix
            name="richEditor"
            :value="value"
            v-bind="$attrs"
            :files-enabled="config.acceptFiles"
            @change="onInput"
            @file-add="onFileAdded"
            @file-remove="onFileRemoved"
        />
    </div>
</template>

<script>
    export default {
        name: 'FormRichEditorInput',
        inheritAttrs: false,

        inject: ['errors', 'isRequired'],

        props: {
            value: {
                type: String | null,
                required: true,
            },
            config: {
                type: Object,
                required: true,
            }
        },

        methods: {
            onInput (value) {
                this.$emit('input', value)
            },

            onFileAdded ({ attachment }) {
                if (attachment.file) {
                    // TODO: Upload file to API...
                    console.log(attachment)
                }
            },

            onFileRemoved ({ attachment: { attachment } }) {
                // TODO: Delete file on API...
                console.log(attachment)
            },
        },

        mounted () {
            //
        },

        beforeDestroy () {
            //
        }
    }
</script>
