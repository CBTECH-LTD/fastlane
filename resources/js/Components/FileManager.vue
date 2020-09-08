<template>
    <div tabindex="0" @keydown.esc="close">
        <div class="fixed top-0 left-0 w-full h-screen bg-black bg-opacity-50 flex items-center justify-center p-8 z-50">
            <div class="relative w-full h-full bg-white shadow-lg rounded-lg">
                <div class="absolute top-0 left-0 w-full h-16 flex items-center justify-between border-b border-gray-200 px-4">
                    <div>
                        <f-button @click="openUploadModal" size="lg" left-icon="cloud-upload-alt" :disabled="!uploadForm || isUploading">Upload</f-button>
                    </div>
                    <div>
                        <f-button @click="selectFiles" color="green" size="lg" left-icon="cloud-upload-alt" :disabled="!selectedFiles.length">Select</f-button>
                        <f-button @click="close" variant="minimal" size="lg" left-icon="close">Cancel</f-button>
                    </div>
                </div>
                <div class="h-full py-16">
                    <template v-if="files.length">
                        <transition-group name="files-list" tag="div" class="w-full h-full p-4 flex flex-wrap overflow-x-hidden overflow-y-auto custom-scroll">
                            <div v-for="file in files" :key="file.file" class="relative w-1/2 p-2">
                                <div class="absolute top-0 left-0 z-10">
                                    <input v-if="canSelectFile(file)" type="checkbox" class="form-checkbox p-3" :checked="file.selected" @input="toggleFile(file)">
                                </div>
                                <div class="relative w-full h-16 flex items-center bg-gray-100 border border-gray-300 rounded-lg shadow-md overflow-hidden">
                                    <div class="w-16 h-full flex items-center justify-center bg-purple-300 p-2 border-r border-purple-500 text-purple-600 text-xs font-semibold uppercase"
                                         :style="isImage(file) ? `background-image: url('${file.url}'); background-size: cover; background-repeat: no-repeat; background-position: center;` : ''">
                                        <span v-if="!isImage(file)">{{ file.extension }}</span>
                                    </div>
                                    <div class="flex-grow flex flex-col justify-center h-full px-2">
                                    <span class="truncate w-full pr-2">
                                        {{ file.name }}
                                    </span>
                                        <a :href="file.url" target="_blank" class="block text-sm text-brand-700 underline mt-1">Download</a>
                                    </div>
                                </div>
                            </div>
                        </transition-group>
                    </template>
                </div>
                <div class="absolute bottom-0 left-0 px-4 w-full h-16 border-t border-gray-200 flex items-center justify-center">
                    <f-paginator v-if="meta" :meta="meta" :as-links="false" @changed="(url) => loadFiles(url)"/>
                </div>
            </div>
        </div>
        <div ref="container"></div>
    </div>
</template>

<script>
import axios from 'axios'
import map from 'lodash/map'
import filter from 'lodash/filter'
import { v4 as uuidv4 } from 'uuid'
import { FormSchemaFactory } from '../Support/FormSchema'
import { Uppy } from '@uppy/core'
import Dashboard from '@uppy/dashboard'
import ImageEditor from '@uppy/image-editor'
import XHRUpload from '@uppy/xhr-upload'

export default {
    name: 'FileManager',

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
    },

    data () {
        return {
            id: uuidv4(),
            isUploading: false,
            files: [],
            meta: {},
            uploadForm: null,
            uppy: null,
        }
    },

    computed: {
        selectedFiles () {
            return filter(this.files, f => f.selected)
        },
        isMultiple () {
            return this.maxNumberOfFiles === undefined || this.maxNumberOfFiles === null || parseInt(this.maxNumberOfFiles) > 1
        }
    },

    methods: {
        close () {
            this.$emit('close')
            this.$destroy()
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
            const { data } = await axios.get(url, {
                params: {
                    'filter[types]': this.fileTypes,
                }
            })

            const selected = map(this.selected, f => parseInt(f.id))

            this.files = map(data.data, f => ({
                ...f.attributes,
                selected: selected.indexOf(parseInt(f.id)) > -1,
                id: f.id,
            }))

            this.meta = data.meta

            const schema = filter(data.meta.entry_type.schema, f => f.name === 'file')
            this.uploadForm = new FormSchemaFactory({}, schema)
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
