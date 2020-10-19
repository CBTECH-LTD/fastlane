<template>
    <EntriesIndex :items="items">
        <template v-slot:actions>
            <f-button v-if="items.links.create" @click="openUploadModal" left-icon="file-upload" size="lg">Upload {{ items.meta.entry_type.plural_name }}</f-button>
        </template>
        <template v-slot:before-table>
            <div class="w-full my-8">
                <f-form-file-input :show-upload-button="false" ref="fileInput" :field="uploadForm.getField('file')" @input="uploadFiles"/>
            </div>
        </template>
    </EntriesIndex>
</template>

<script>
import filter from 'lodash/filter'
import EntriesIndex from '../Entries/Index'
import { FormSchemaFactory } from '../../Support/FormSchema'

export default {
    name: 'FileManager.Index',
    components: { EntriesIndex },
    props: {
        items: {
            required: true,
            type: Object,
        },
    },

    data () {
        const schema = filter(this.items.meta.entry_type.schema, f => f.attribute === 'file')

        return {
            uploadForm: new FormSchemaFactory(schema, {}),
            isUploading: false,
        }
    },

    methods: {
        openUploadModal () {
            this.$refs.fileInput.openFileManager()
        },

        async uploadFiles () {
            if (!this.isUploading) {
                this.isUploading = true

                try {
                    await this.$inertia.post(
                        this.items.links.upload,
                        this.uploadForm.toFormObject(false).all(),
                        { preserveState: false }
                    )
                } catch {}

                this.isUploading = false
            }
        },
    }
}
</script>

<style scoped>

</style>
