import axios from 'axios'
import ajax from '@codexteam/ajax'
import selectFiles from '@/Support/utils/selectFiles'

export default class Uploader {
    /**
     * @param {Object} config
     * @param {function} onUpload - callback for successful file upload
     * @param {function} onError - callback for uploading errors
     */
    constructor ({ config, onUpload, onError }) {
        this.config = config
        this.onUpload = onUpload
        this.onError = onError
    }

    /**
     * Handle clicks on the upload file button
     * @fires ajax.transport()
     * @param {function} onPreview - callback fired when preview is ready
     */
    async uploadSelectedFile ({ onPreview }) {
        const params = {
            url: this.config.endpoint || '',
            accept: this.config.types || '*',
            data: this.config.additionalRequestData || {},
            headers: this.config.additionalRequestHeaders || {},
            beforeSend: function () {
                console.log(this)

                return onPreview()
            },
            fieldName: this.config.field || 'file'
        }

        const files = await selectFiles(params)

        if (files && files.length) {
            const formData = new FormData()
            formData.append('Content-Type', files[0].type)
            formData.append('name', files[0].name)

            for (const key of Object.keys(params.data)) {
                formData.append(key, params.data[key])
            }

            formData.append(params.fieldName, files[0], files[0].name)

            try {
                const { data: { id, url, name } } = await axios.post(params.url, formData, {
                    headers: params.headers,
                })

                this.onUpload({
                    body: {
                        success: 1,
                        file: { id, url, name },
                    },
                })
            } catch (error) {
                const message = (error && error.message)
                    ? error.message
                    : this.config.errorMessage || 'File upload failed'

                this.onError(message)
            }
        }
    }
}
