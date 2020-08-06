<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required">
        <template v-if="field.label" v-slot:label>{{ field.label }}</template>
        <div class="w-full relative">
            <input ref="fileInput" type="file" @change="uploadFile" class="hidden w-0 h-0">
            <div @click="openFileDialog" class="w-32 h-32 form-input cursor-pointer overflow-hidden flex items-center justify-center" v-bind="$attrs">
                <img v-if="field.value" :src="field.value" alt="" class="w-full">
                <span v-else class="flex flex-col items-center justify-center">
                    <f-icon class="text-2xl text-gray-600" name="upload"></f-icon>
                    <span class="mt-2 text-xs text-gray-600">Upload image</span>
                </span>
            </div>
        </div>
    </f-form-field>
</template>

<script>
import axios from 'axios'
import FormInput from '../Mixins/FormInput'

export default {
    name: 'ImageInput',
    mixins: [FormInput],

    data: () => ({
        isUploading: false,
    }),

    methods: {
        async uploadFile (event) {
            if (this.isUploading) {
                return
            }

            const file = event.target.files[0]

            if (!file) {
                return
            }

            const data = new FormData()
            data.append(this.field.name, file)
            data.append('type', file.type)
            data.append('filename', file.name)

            this.isUploading = true
            const { data: { url } } = await axios.post(this.field.config.uploadUrl, data)
            this.isUploading = false

            // this.onInput(`${this.field.config.baseViewUrl}${url}`)
            this.onInput(url)
        },

        openFileDialog () {
            this.$refs.fileInput.click()
        }
    }
}
</script>
