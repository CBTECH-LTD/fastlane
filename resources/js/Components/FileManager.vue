<template>
    <div v-bind="outerContainerAttrs" @keydown.esc="close">
        <div class="overlay">
            <div class="mainContainer">
                <!-- TOOLBAR -->
                <div class="toolbar">
                    <div>
                        <f-button @click="openUploadModal" size="lg" left-icon="cloud-upload-alt" :disabled="!uploadForm || isUploading">Upload</f-button>
                    </div>
                    <div class="flex items-center">
                        <input type="text" class="w-full form-input" placeholder="Search..." :value="searchTerm" @input="onSearch($event.target.value)">
                        <!--                        <v-select class="form-input w-64 ml-4" clearable :options="fileTypesOptions" :value="searchType" @input="onSearchType"></v-select>-->
                    </div>
                    <div v-if="selectable || deletable">
                        <f-button v-if="selectable" @click="selectFiles" color="green" size="lg" left-icon="cloud-upload-alt" :disabled="!selectedFiles.length">Select</f-button>
                        <f-button v-if="deletable && selectedFiles.length" @click="deleteFiles" color="red" size="lg" left-icon="cloud-upload-alt">Delete Files</f-button>
                        <f-button v-if="selectable" @click="close" variant="minimal" size="lg" left-icon="close">Cancel</f-button>
                    </div>
                </div>

                <!-- FILES LIST -->
                <div class="files">
                    <template v-if="isLoading">
                        <div class="flex justify-center items-center w-full h-full">
                            <f-spinner></f-spinner>
                        </div>
                    </template>
                    <template v-else-if="files.length">
                        <transition-group name="files-list" tag="div" class="files__list">
                            <template v-for="file in files">
                                <div v-if="file.visible" :key="file.id" class="relative w-32 p-2">
                                    <div class="absolute top-0 left-0 z-10">
                                        <input v-if="canSelectFile(file)" type="checkbox" class="form-checkbox p-3" :checked="file.selected" @input="toggleFile(file)">
                                    </div>
                                    <div class="relative w-full h-32 flex flex-col bg-gray-100 border-2 rounded-lg shadow-md overflow-hidden text-xs" :class="file.selected ? 'border-purple-300' : 'border-transparent'">
                                        <div class="w-full h-20 flex items-center justify-center bg-purple-200 text-purple-600 text-xs font-semibold uppercase rounded overflow-hidden"
                                             :style="isImage(file) ? `background-image: url('${file.url}'); background-size: cover; background-repeat: no-repeat; background-position: center;` : ''">
                                            <span v-if="!isImage(file)">{{ file.extension }}</span>
                                        </div>
                                        <div class="flex flex-col flex-grow justify-center px-1 overflow-hidden">
                                            <span class="truncate w-full">
                                                {{ file.name }}
                                            </span>
                                            <a :href="file.url" target="_blank" class="block text-brand-700 underline">Download</a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </transition-group>
                    </template>
                </div>
                <div v-if="meta" class="pagination">
                    <f-paginator :meta="meta" :as-links="false" @changed="(url) => loadFiles(url)"/>
                </div>
            </div>
        </div>
        <div ref="container"></div>
    </div>
</template>

<script>
import axios from 'axios'
import Fuse from 'fuse.js'
import map from 'lodash/map'
import filter from 'lodash/filter'
import { v4 as uuidv4 } from 'uuid'
import { FormSchemaFactory } from '../Support/FormSchema'
import { Uppy } from '@uppy/core'
import Dashboard from '@uppy/dashboard'
import ImageEditor from '@uppy/image-editor'
import XHRUpload from '@uppy/xhr-upload'
import VSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'

export default {
    name: 'FileManager',
    components: { VSelect },

    props: {
        endpoint: {
            type: String,
            required: true,
        },
        selected: {
            type: Array | undefined,
            default: undefined,
        },
        csrfToken: {
            type: String,
            required: true,
        },
        maxFileSize: {
            type: Number | undefined,
            default: undefined,
        },
        minNumberOfFiles: {
            type: Number | undefined,
            default: undefined,
        },
        maxNumberOfFiles: {
            type: Number | undefined,
            default: undefined,
        },
        fileTypes: {
            type: Array,
            default: () => [],
        },
        selectable: {
            type: Boolean,
            default: true,
        },
        deletable: {
            type: Boolean,
            default: false,
        },
        inline: {
            type: Boolean,
            default: false,
        },
    },

    data () {
        return {
            id: uuidv4(),
            isLoading: false,
            isUploading: false,
            files: [],
            meta: {},
            uploadForm: null,
            uppy: null,
            fuse: null,
            searchTerm: '',
            searchType: null,
        }
    },

    computed: {
        selectedFiles () {
            return filter(this.files, f => f.selected)
        },
        fileTypesOptions () {
            const types = []

            this.files.forEach(file => {
                if (!types.includes(file.extension)) {
                    types.push(file.extension)
                }
            })

            return types
        },
        isMultiple () {
            return this.maxNumberOfFiles === undefined || this.maxNumberOfFiles === null || parseInt(this.maxNumberOfFiles) > 1
        },
        outerContainerAttrs () {
            if (this.inline) {
                return {
                    class: 'inline',
                }
            }

            return {
                tabindex: '0',
                class: 'modal',
            }
        },
        filesListAttrs () {
            return {
                class: this.inline ? 'filesList filesList--inline' : 'filesList filesList--overlay',
            }
        }
    },

    methods: {
        close () {
            if (! this.inline) {
                this.$emit('close')
                this.$destroy()
            }
        },

        openUploadModal () {
            const dash = this.uppy.getPlugin('Dashboard')

            if (!dash.isModalOpen()) {
                dash.openModal()
            }
        },

        closeUploadModal () {
            this.uppy.getPlugin('Dashboard').closeModal()
        },

        canSelectFile (file) {
            return file.selected ||
                !this.isMultiple ||
                this.maxNumberOfFiles === undefined ||
                this.maxNumberOfFiles === null ||
                this.selectedFiles.length < parseInt(this.maxNumberOfFiles)
        },

        selectFiles () {
            this.$emit('files-selected', this.selectedFiles)
        },

        deleteFiles () {
            //
        },

        toggleFile (file) {
            if (!this.isMultiple) {
                this.files.forEach(f => {
                    if (f.id !== file.id) {
                        f.selected = false
                    }
                })
            }

            file.selected = !file.selected
        },

        async loadFiles (url) {
            if (this.isLoading) {
                return
            }

            this.isLoading = true

            const { data } = await axios.get(url, {
                params: {
                    'filter[types]': this.fileTypes,
                }
            })

            const selected = map(this.selected, f => parseInt(f.id))

            this.files = map(data.data, f => ({
                ...f.attributes,
                selected: selected.indexOf(parseInt(f.id)) > -1,
                visible: true,
                id: f.id,
            }))

            this.meta = data.meta

            // Generate the Form Schema
            const schema = filter(data.meta.entry_type.schema, f => f.name === 'file')
            this.uploadForm = new FormSchemaFactory({}, schema)

            // Initialize Fuse for fuzy search
            this.fuse = new Fuse(this.files, {
                keys: ['name', 'mimetype'],
                threshold: 0.2
            })

            this.isLoading = false
        },

        async uploadFiles () {
            if (!this.isUploading) {
                this.isUploading = true

                try {
                    await axios.post(this.endpoint, this.uploadForm.toFormObject(false).all(), {
                        headers: { 'X-CSRF-TOKEN': this.csrfToken }
                    })

                    await this.loadFiles(this.endpoint)
                } catch {}

                this.isUploading = false
            }
        },

        isImage (file) {
            return file.mimetype && file.mimetype.startsWith('image/')
        },

        onSearch (value) {
            this.searchTerm = value

            // If an empty string is provided, we just show all files.
            if (value.trim() === '') {
                this.files.forEach(file => {
                    file.visible = true
                })

                return
            }

            // Search for files matching the typed term
            const ids = this.fuse.search(value).map(item => {
                return item.item.id
            })

            this.files.forEach(file => {
                file.visible = ids.includes(file.id)
            })
        },

        onSearchType (type) {
            this.searchType = type
        }
    },

    mounted () {
        document.body.style.overflow = 'hidden'

        this.uppy = new Uppy({
            autoProceed: false,
            restrictions: {
                maxFileSize: this.maxFileSize,
                minNumberOfFiles: this.minNumberOfFiles,
                maxNumberOfFiles: this.maxNumberOfFiles,
                allowedFileTypes: this.fileTypes.length ? this.fileTypes : null,
            },
        }).use(Dashboard, {
            target: this.$refs.container,
            inline: false,
            width: '100%',
            height: 300,
            showProgressDetails: true,
            showLinkToFileUploadResult: false,
            browserBackButtonClose: false,
            proudlyDisplayPoweredByUppy: false,
            onRequestCloseModal: () => this.closeUploadModal(),
            metaFields: [
                { id: 'name', name: this.$l('core.fields.name'), placeholder: this.$l('core.fields.name') }
            ]
            // note: 'Images up to 10 MB',
        }).use(ImageEditor, {
            id: `ImageEditor_${this.id}`,
            target: Dashboard,
            quality: 0.8,
            cropperOptions: {
                viewMode: 1,
                background: false,
                autoCropArea: 1,
                responsive: true,
            }
        }).use(XHRUpload, {
            endpoint: this.endpoint,
            withCredentials: true,
            bundle: false,
            headers: {
                'X-CSRF-TOKEN': this.csrfToken
            }
        })

        this.uppy.on('complete', async ({ successful }) => {
            await this.loadFiles(this.endpoint)

            this.closeUploadModal()
            this.uppy.reset()
        })

        this.loadFiles(this.endpoint)
    },

    beforeDestroy () {
        document.body.style.overflow = ''
        delete this.uppy
    }
}
</script>

<style module src="../../css/components/uppy.dashboard.css"></style>

<style scoped>
    .files {
        @apply w-full;
    }

    .files__list {
        @apply w-full p-4 flex justify-center flex-wrap;
    }

    .overlay {
        @apply w-full;
    }

    .toolbar {
        @apply w-full h-16 flex items-center justify-between border-b border-gray-200 px-4;
    }

    .pagination {
        @apply px-4 w-full h-16 border-t border-gray-200 flex items-center justify-center;
    }

    .mainContainer {
        @apply relative w-full;
    }

    /**
     * MODAL
     */

    .modal .overlay {
        @apply fixed flex items-center justify-center top-0 left-0 h-screen bg-black bg-opacity-50 p-8;
        z-index: 2000;
    }

    .modal .files {
        @apply py-16;
    }

    .modal .files__list {
        @apply max-h-full overflow-x-hidden overflow-y-auto;
    }

    .modal .toolbar {
        @apply absolute top-0 left-0;
    }

    .modal .pagination {
        @apply absolute bottom-0 left-0;
    }

    .modal .mainContainer {
        @apply h-full bg-white shadow-lg rounded-lg;
    }

    /**
     * INLINE
     */

    .inline .filesList {

    }

    .inline .filesList {
        @apply max-h-full overflow-x-hidden overflow-y-auto;
    }
</style>
