<template>
    <trix-editor
        ref="editorInstance"
        class="trix-content"
        v-bind="$attrs"
        @keydown.stop
        @trix-change="onChange"
        @trix-initialize="initialize"
        @trix-attachment-add="onFileAdded"
        @trix-attachment-remove="onFileRemoved"
        @trix-file-accept="acceptFiles"
        :value="value"
    />
</template>

<script>
    import 'trix'
    import 'trix/dist/trix.css'

    export default {
        name: 'Trix',
        props: {
            name: { type: String },
            value: { type: String },
            placeholder: { type: String },
            filesEnabled: { type: Boolean, default: true },
            disabled: { type: Boolean, default: false },
        },

        methods: {
            initialize () {
                this.$refs.editorInstance.editor.insertHTML(this.value)

                if (this.disabled) {
                    this.$refs.editorInstance.setAttribute('contenteditable', false)
                }
            },

            onChange () {
                this.$emit('change', this.$refs.editorInstance.value)
            },

            onFileAdded (e) {
                this.$emit('file-add', e)
            },

            onFileRemoved (e) {
                this.$emit('file-remove', e)
            },

            acceptFiles (e) {
                if (!this.filesEnabled) {
                    e.preventDefault()
                }
            },
        }
    }
</script>

<style>
    .trix-content ul, .trix-content ol {
        list-style: disc !important;
    }
</style>
