<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required">
        <template v-if="field.label" v-slot:label>{{ field.label }}</template>
        <div class="w-full">
            <!--            <f-button @click="openModal" left-icon="upload">Select files</f-button>-->
            <div ref="container"></div>
        </div>
    </f-form-field>
</template>

<script>
import { v4 as uuidv4 } from 'uuid'
import FormInput from '../Mixins/FormInput'
import { Uppy } from '@uppy/core'
import Dashboard from '@uppy/dashboard'
import XHRUpload from '@uppy/xhr-upload'
import '@uppy/core/dist/style.min.css'
import ImageEditor from '@uppy/image-editor'

export default {
    name: 'FileInput',
    mixins: [FormInput],
    inheritAttrs: false,

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
            autoProceed: true,
            restrictions: {
                // maxFileSize: 10000000,
            },
        }).use(Dashboard, {
            target: this.$refs.container,
            inline: true,
            width: '100%',
            height: 300,
            showProgressDetails: true,
            showLinkToFileUploadResult: false,
            browserBackButtonClose: false,
            proudlyDisplayPoweredByUppy: false,
            onRequestCloseModal: () => this.closeModal(),
            // note: 'Images up to 10 MB',
        }).use(ImageEditor, {
            target: Dashboard,
            quality: 0.8,
        }).use(XHRUpload, {
            endpoint: this.field.config.links.self,
            withCredentials: true,
            headers: {
                'X-CSRF-TOKEN': this.field.config.csrfToken
            }
        })

        this.uppy.on('file-added', () => {
            this.uppy.setMeta({
                draft_id: this.draftId,
            })
        })

        this.uppy.on('upload-success', (file, response) => {
            this.field.value = this.field.value || []
            this.field.value.push(response.body.url)
        })
    },

    beforeDestroy () {
        delete this.uppy
    }
}
</script>

<style module src="../../../css/components/uppy.dashboard.css"></style>
