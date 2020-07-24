<template>
    <div class="w-full">
        <f-trix
            name="richEditor"
            :value="field.value"
            v-bind="$attrs"
            :files-enabled="field.config.acceptFiles"
            @change="onInput"
            @file-add="onFileAdded"
            @file-remove="onFileRemoved"
        />
    </div>
</template>

<script>
    import axios from 'axios'
    import { v4 as uuidv4 } from 'uuid'
    import FormInput from '../Mixins/FormInput'

    export default {
        name: 'RichEditorInput',
        mixins: [FormInput],
        inheritAttrs: false,

        props: {
            field: {
                type: Object,
                required: true,
            },
        },

        data: () => ({
            draftId: uuidv4(),
        }),

        methods: {
            /**
             * @param {FormObject} formObject
             */
            commit (formObject) {
                formObject.put(this.field.name, this.field.value)
                formObject.put(`${this.field.name}__draft_id`, this.draftId)
            },

            onInput (value) {
                this.field.value = value
            },

            async onFileAdded ({ attachment }) {
                if (attachment.file) {
                    const data = new FormData()
                    data.append('Content-Type', attachment.file.type)
                    data.append('file', attachment.file)
                    data.append('draft_id', this.draftId)

                    const { data: { url } } = await axios.post(this.field.config.links.self, data, {
                        onUploadProgress: function (progressEvent) {
                            attachment.setUploadProgress(
                                Math.round((progressEvent.loaded * 100) / progressEvent.total)
                            )
                        },
                    })

                    return attachment.setAttributes({
                        url: url,
                        href: url,
                    })
                }
            },

            onFileRemoved ({ attachment: { attachment } }) {
                // TODO: Delete file on API...
                console.log(attachment)
            },

            cleanUp () {
                if (this.field.config.acceptFiles) {
                    // TODO: Delete draft attachments
                }
            }
        },

        mounted () {
            //
        },

        beforeDestroy () {
            this.cleanUp()
        }
    }
</script>
