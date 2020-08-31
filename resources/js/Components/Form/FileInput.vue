<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required && (inline || showUploadButton)" :stacked="stacked">
        <template v-if="field.label && (inline || showUploadButton)" v-slot:label>{{ field.label }}</template>
        <template v-if="field.description && (inline || showUploadButton)" v-slot:description>{{ field.description }}</template>
        <div class="w-full">
            <div ref="container"></div>
            <div v-if="!inline && showUploadButton">
                <f-button @click="openModal" left-icon="file-upload" size="lg">Upload {{ field.label }}</f-button>
            </div>
        </div>
    </f-form-field>
</template>

<script>
import map from 'lodash/map'
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
            default: true,
        },
        inline: {
            type: Boolean,
            default: true,
        },
        showUploadButton: {
            type: Boolean,
            default: false,
        },
    },

    data () {
        return {
            uppy: null,
            files: {},
            draftId: uuidv4(),
        }
    },

    methods: {
        commit (formObject) {
            formObject.put(this.field.name, this.field.value)
            formObject.put(`${this.field.name}__draft_id`, this.draftId)
        },

        deleteFiles () {
            console.log('delete files')
        },

        openModal () {
            const dash = this.uppy.getPlugin('Dashboard')

            if (!dash.isModalOpen()) {
                dash.openModal()
            }
        },

        closeModal () {
            this.uppy.getPlugin('Dashboard').closeModal()
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
            height: 300,
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
            const value = this.field.value || []

            const files = map(successful, f => {
                return f.response.body.file
            })

            this.onInput(value.concat(files))
        })
    },

    beforeDestroy () {
        delete this.uppy
    }
}
</script>

<style module src="../../../css/components/uppy.dashboard.css"></style>
