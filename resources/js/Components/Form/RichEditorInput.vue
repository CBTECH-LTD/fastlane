<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required">
        <template v-if="field.label" v-slot:label>{{ field.label }}</template>
        <div class="w-full">
            <Editor
                :initialValue="field.value"
                :init="editorConfig"
                @onChange="(ev, editor) => onInput(editor.getContent())"
            ></Editor>
        </div>

        <portal to="modals">
            <f-file-manager v-if="fileManagerSettings !== null" max-number-of-files="1" :endpoint="field.config.links.fileManager" :file-types="['image/*']" :csrf-token="$page.app.csrfToken" @files-selected="files => onFilesSelected(files)" @close="closeFileManager" />
        </portal>
    </f-form-field>
</template>

<script>
import axios from 'axios'
import Editor from '@tinymce/tinymce-vue'
import FormInput from '../Mixins/FormInput'

// Tiny MCE
import 'tinymce/tinymce'
import 'tinymce/icons/default'
import 'tinymce/themes/silver/theme'

// Tiny Plugins
import 'tinymce/plugins/advlist'
import 'tinymce/plugins/anchor'
import 'tinymce/plugins/autolink'
import 'tinymce/plugins/autoresize'
import 'tinymce/plugins/charmap'
import 'tinymce/plugins/code'
import 'tinymce/plugins/fullscreen'
import 'tinymce/plugins/help'
import 'tinymce/plugins/hr'
import 'tinymce/plugins/image'
import 'tinymce/plugins/imagetools'
import 'tinymce/plugins/link'
import 'tinymce/plugins/lists'
import 'tinymce/plugins/media'
import 'tinymce/plugins/paste'
import 'tinymce/plugins/searchreplace'
import 'tinymce/plugins/table'
import 'tinymce/plugins/visualblocks'
import 'tinymce/plugins/wordcount'

export default {
    name: 'RichEditorInput',
    mixins: [FormInput],
    inheritAttrs: false,
    components: {
        Editor,
    },

    props: {
        field: {
            type: Object,
            required: true,
        },
    },

    data () {
        return {
            editor: null,
            editorConfig: {
                height: 500,
                menubar: false,
                document_base_url: this.$page.app.baseUrl + '/',
                relative_urls: false,
                remove_script_host: true,
                image_title: true,
                image_advtab: true,
                file_picker_callback: (cb, value, meta) => this.openFileManager(cb, value, meta),
                plugins: [
                    'advlist autolink lists charmap anchor link image table',
                    'searchreplace visualblocks code fullscreen',
                    'media hr paste help wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic link | hr | image imagetools media | \
                            table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | \
                            alignleft aligncenter alignright | \
                            bullist numlist charmap anchor outdent indent | fullscreen help'
            },
            linkUrl: null,
            linkMenuIsActive: false,
            fileManagerSettings: null,
        }
    },

    methods: {
        /**
         * @param {FormObject} formObject
         */
        commit (formObject) {
            formObject.put(this.field.name, this.field.value)
        },

        async onFileAdded ({ attachment }) {
            if (attachment.file) {
                const data = new FormData()
                data.append('Content-Type', attachment.file.type)
                data.append('files[]', attachment.file)

                const { data: { urls } } = await axios.post(this.field.config.links.self, data, {
                    onUploadProgress: function (progressEvent) {
                        attachment.setUploadProgress(
                            Math.round((progressEvent.loaded * 100) / progressEvent.total)
                        )
                    },
                })

                return attachment.setAttributes({
                    url: urls[0],
                    href: urls[0],
                })
            }
        },

        onFileRemoved ({ attachment: { attachment } }) {
            // TODO: Delete file on API...
            console.log(attachment)
        },

        openFileManager (callback, value, meta) {
            this.fileManagerSettings = { callback, value, meta }
        },

        closeFileManager () {
            this.fileManagerSettings = null
        },

        onFilesSelected (files) {
            console.log('onFilesSelected', files)

            this.fileManagerSettings.callback(files[0].url, { title: files[0].name })

            this.closeFileManager()
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

<style scoped>
.menubar {
    @apply flex items-center py-2;
}

.menububble {
    @apply absolute flex items-center bg-gray-900 rounded z-10 p-1 mb-1;
    transform: translateX(-50%);
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.2s, visibility 0.2s;
}

.menububble.is-active {
    opacity: 1;
    visibility: visible;
}

.menububble__form {
    @apply flex items-center;
}

.menububble__button {
    @apply p-1 flex items-center justify-center rounded bg-transparent font-normal text-sm text-gray-300 mx-1;
}

.menububble__button:hover {
    @apply bg-gray-800 text-gray-200;
}

.menububble__button.is-active {
    @apply text-gray-100 bg-gray-800;
}

.menububble__input {
    @apply text-white bg-transparent border-0;
}

.menubar__button {
    @apply w-8 h-6 flex items-center justify-center rounded bg-transparent font-bold text-sm text-gray-700 mx-1;
}

.menubar__button:hover {
    @apply bg-gray-200 text-gray-800;
}

.menubar__button.is-active {
    @apply bg-gray-300 text-gray-900;
}

.divider {
    @apply mx-1 h-4 border-l border-gray-500;
}

.editor__content {
    min-height: 200px;
}
</style>

<style>
.editor__content {
    @apply relative;
    max-height: 600px;
    overflow-y: auto;
}

.editor__content .ProseMirror {
    outline: none;
    min-height: 200px;
}

.editor__content h3 {
    @apply text-lg text-gray-700 font-semibold;
}

.editor__content h2 {
    @apply text-xl text-gray-800 font-bold;
}

.editor__content h1 {
    @apply text-2xl text-gray-900 font-bold;
}

.editor__content ul {
    @apply p-8;
    list-style: disc;
}

.editor__content ol {
    @apply p-8;
    list-style: decimal;
}

.editor__content blockquote {
    @apply p-4 bg-gray-200 italic text-base text-gray-900 rounded-lg;
}

.editor__content code {
    @apply p-1 bg-gray-200 text-gray-800 text-base font-mono rounded;
}

.editor__content pre {
    @apply p-4 bg-gray-900 text-gray-100 text-base font-mono rounded-lg;
}

.editor__content pre code {
    @apply bg-transparent text-gray-100 text-base font-mono;
}

.editor__content a {
    @apply underline text-blue-500;
}

.editor__content table {
    @apply table-auto border border-gray-500 w-full;
}

.editor__content td {
    @apply p-2 border border-gray-500 text-base text-gray-700;
}

.editor__content tr {
    @apply border border-gray-500 text-base text-gray-700;
}

.editor__content .iframe__embed {
    @apply w-full bg-gray-200 rounded;
    height: 15rem;
    border: 0;
}

.editor__content .iframe__input {
    @apply bg-white border-2 border-gray-400 rounded;
    display: block;
    width: 100%;
    font: inherit;
    padding: 0.3rem 0.5rem;
}
</style>
