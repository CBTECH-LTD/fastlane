<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required && (inline || showUploadButton)" :stacked="stacked">
        <template v-if="field.label && (inline || showUploadButton)" v-slot:label>{{ field.label }}</template>
        <template v-if="field.description && (inline || showUploadButton)" v-slot:description>{{ field.description }}</template>

        <div class="w-full">
            <template v-if="listFiles && field.value && field.value.length">
                <transition-group name="files-list" tag="div" class="w-full flex flex-wrap">
                    <div v-for="file in field.value" :key="file.file" class="relative w-1/2 p-2">
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

            <f-button @click="openFileManager" left-icon="file-upload" size="lg">Select {{ field.label }}</f-button>

            <portal to="modals">
                <f-file-manager v-if="showFileManager"
                    :endpoint="$page.app.cpUrls.fileManager"
                    :selected="this.field.value"
                    :max-file-size="field.config.maxFileSize"
                    :min-number-of-files="field.config.minNumberOfFiles"
                    :max-number-of-files="field.config.maxNumberOfFiles"
                    :file-types="field.config.fileTypes"
                    :csrf-token="$page.app.csrfToken"
                    @files-selected="onFilesSelected"
                    @close="closeFileManager"
                />
            </portal>
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
        listFiles: {
            type: Boolean,
            default: true,
        },
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

    data: () => ({
        showFileManager: false,
    }),

    methods: {
        commit (formObject) {
            formObject.put(this.field.name, this.field.value.map(f => f.id))
        },

        openFileManager () {
            this.showFileManager = true
        },

        closeFileManager () {
            this.showFileManager = false
        },

        onFilesSelected (files) {
            this.onInput(files)
            this.closeFileManager()
        },

        removeFile (file) {
            const index = findIndex(this.field.value, f => {
                return f.file === file.file
            })

            if (index > -1) {
                const newVal = cloneDeep(this.field.value)
                this.$delete(newVal, index)
                this.onInput(newVal)
            }
        },

        isImage (file) {
            return file.mimetype && file.mimetype.startsWith('image/')
        }
    },
}
</script>

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
