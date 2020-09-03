<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required && (inline || showUploadButton)" :stacked="stacked">
        <template v-if="field.label && (inline || showUploadButton)" v-slot:label>{{ field.label }}</template>
        <template v-if="field.description && (inline || showUploadButton)" v-slot:description>{{ field.description }}</template>

        <div class="w-full">
            <template v-if="field.value.length">
                <transition-group name="files-list" tag="div" class="w-full flex flex-wrap">
                    <div v-for="file in persistentFiles" :key="file.file" class="relative w-1/2 p-2">
                        <div class="absolute top-0 right-0 -mr-2 -mt-2 z-10">
                            <f-button @click="removeFile(file)" color="danger" size="sm" class="btn-oval p-0 flex items-center justify-center">
                                <div class="flex items-center justify-center w-full h-full">
                                    <f-icon class="text-xl" name="times-circle"/>
                                </div>
                            </f-button>
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

            <div ref="container"></div>
            <div v-if="!inline && showUploadButton">
                <f-button @click="openModal" left-icon="file-upload" size="lg">Upload {{ field.label }}</f-button>
            </div>
        </div>
    </f-form-field>
</template>

<script>
import map from 'lodash/map'
import findIndex from 'lodash/findIndex'
import cloneDeep from 'lodash/cloneDeep'
import { v4 as uuidv4 } from 'uuid'
import FormInput from '../Mixins/FormInput'
import { Uppy } from '@uppy/core'
import Dashboard from '@uppy/dashboard'
import XHRUpload from '@uppy/xhr-upload'
import ImageEditor from '@uppy/image-editor'
import '@uppy/core/dist/style.min.css'
import '@uppy/image-editor/dist/style.min.css'

export default {
    name: 'FileInput',
    mixins: [FormInput],
    inheritAttrs: false,

    props: {
        autoUpload: {
            type: Boolean,
            default: false,
        },
        inline: {
            type: Boolean,
            default: false,
        },
        showUploadButton: {
            type: Boolean,
            default: true,
        },
        height: {
            type: Number,
            default: 300,
        }
    },

    data () {
        return {
            uppy: null,
            draftId: uuidv4(),
        }
    },

    computed: {
        persistentFiles () {
            return this.field.value.filter(
                file => !file.excluded
            )
        },
        excludedFiles () {
            return this.field.value.filter(
                file => file.excluded === true
            )
        }
    },

    methods: {
        commit (formObject) {
            formObject.put(this.field.name, this.field.value.map(f => ({
                file: f.file,
                is_draft: !!f.is_draft,
                excluded: !!f.excluded,
            })))

            formObject.put(`${this.field.name}__draft_id`, this.draftId)
        },

        removeFile (file) {
            const index = findIndex(this.field.value, f => {
                return f.file === file.file
            })

            if (index > -1) {
                const newVal = cloneDeep(this.field.value)
                this.$set(newVal[index], 'excluded', true)
                this.onInput(newVal)
            }
        },

        openModal () {
            const dash = this.uppy.getPlugin('Dashboard')

            if (!dash.isModalOpen()) {
                dash.openModal()
            }
        },

        closeModal () {
            this.uppy.getPlugin('Dashboard').closeModal()
        },

        isImage (file) {
            return file.mimetype && file.mimetype.startsWith('image/')
        }
    },

    mounted () {
        this.uppy = new Uppy({
            autoProceed: this.autoUpload,
            restrictions: {
                maxFileSize: this.field.config.maxFileSize,
                minNumberOfFiles: this.field.config.minNumberOfFiles,
                maxNumberOfFiles: this.field.config.maxNumberOfFiles,
                allowedFileTypes: this.field.config.fileTypes.length ? this.field.config.fileTypes : null,
            },
        }).use(Dashboard, {
            target: this.$refs.container,
            inline: this.inline,
            width: '100%',
            height: this.height,
            showProgressDetails: true,
            showLinkToFileUploadResult: false,
            browserBackButtonClose: false,
            proudlyDisplayPoweredByUppy: false,
            onRequestCloseModal: () => this.closeModal(),
            metaFields: [
                { id: 'name', name: this.$l('core.fields.name'), placeholder: this.$l('core.fields.name') }
            ]
            // note: 'Images up to 10 MB',
        }).use(ImageEditor, {
            id: `ImageEditor_${this.field.name}`,
            target: Dashboard,
            quality: 0.8,
            cropperOptions: {
                viewMode: 1,
                background: false,
                autoCropArea: 1,
                responsive: true,
            }
        }).use(XHRUpload, {
            endpoint: this.field.config.links.self,
            withCredentials: true,
            bundle: false,
            headers: {
                'X-CSRF-TOKEN': this.field.config.csrfToken
            }
        })

        this.uppy.on('file-added', () => {
            this.uppy.setMeta({
                draft_id: this.draftId,
            })
        })

        this.uppy.on('complete', ({ successful }) => {
            const value = (this.field.multiple)
                ? this.field.value || []
                : []

            const files = map(successful, f => {
                return {
                    ...f.response.body,
                    is_draft: true,
                }
            })

            this.onInput(value.concat(files))
            this.closeModal()

            if (!this.multiple) {
                this.uppy.reset()
            }
        })
    },

    beforeDestroy () {
        delete this.uppy
    }
}
</script>

<style module src="../../../css/components/uppy.dashboard.css"></style>

<style scoped>
.files-list-enter-active,
.files-list-leave-active {
    transition: all;
    transition-duration: 500ms;
}

.files-list-enter,
.files-list-leave-to {
    opacity: 0;
    transform: scale(0.70);
}

.files-list-enter-to,
.files-list-leave {
    opacity: 1;
    transform: scale(1);
}

.files-list-leave-active {
    position: absolute;
}

.files-list-move {
    transition: transform 500ms;
}
</style>
