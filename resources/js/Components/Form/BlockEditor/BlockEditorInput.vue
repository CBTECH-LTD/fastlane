<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required" :stacked="field.label_stacked">
        <template v-if="field.label && field.label_visible" v-slot:label>
            {{ field.label }}
        </template>

        <div class="w-full form-input p-6">
            <div :id="editorId" ref="editor"></div>
        </div>
    </f-form-field>
</template>

<script>
import { v4 as uuidv4 } from 'uuid'
import axios from 'axios'
import FormInput from '../../Mixins/FormInput'

// EditorJS
import EditorJS from '@editorjs/editorjs'
import Attaches from './tools/attaches'
import Checklist from '@editorjs/checklist'
import Code from '@editorjs/code'
import Delimiter from '@editorjs/delimiter'
import Embed from '@editorjs/embed'
import Header from '@editorjs/header'
import Image from '@editorjs/image'
import InlineCode from '@editorjs/inline-code'
import List from '@editorjs/list'
import Marker from '@editorjs/marker'
import Quote from '@editorjs/quote'
import Raw from '@editorjs/raw'
import Table from '@editorjs/table'
import Underline from '@editorjs/underline'

export default {
    name: 'BlockEditorInput',
    mixins: [FormInput],

    data () {
        const uuid = uuidv4()

        return {
            editor: null,
            editorId: `editor_${uuid}`,
            draftId: uuid,
        }
    },

    methods: {
        commit (formObject) {
            formObject.put(this.field.name, this.field.value)
            formObject.put(`${this.field.name}__draft_id`, this.draftId)
        },
    },

    created () {
        this.editor = new EditorJS({
            holder: this.editorId,
            placeholder: this.field.placeholder,
            data: this.field.value || {},
            tools: {
                attaches: {
                    class: Attaches,
                    config: {
                        field: 'files[]',
                        endpoint: this.field.config.links.fileManager,
                        additionalRequestHeaders: {
                            'X-CSRF-TOKEN': this.field.config.csrfToken,
                        },
                        additionalRequestData: {
                            draft_id: this.draftId,
                        },
                    },
                },
                checklist: {
                    class: Checklist,
                    inlineToolbar: true,
                },
                code: Code,
                delimiter: Delimiter,
                embed: {
                    class: Embed,
                    inlineToolbar: true,
                },
                header: {
                    class: Header,
                    inlineToolbar: true,
                    defaultLevel: 2,
                },
                image: {
                    class: Image,
                    config: {
                        uploader: {
                            uploadByFile: async (file) => {
                                const formData = new FormData()
                                formData.append('Content-Type', file.type)
                                formData.append('name', file.name)
                                formData.append('draft_id', this.draftId)
                                formData.append('files[]', file)

                                const { data: { id, url, name } } = await axios.post(
                                    this.field.config.links.fileManager,
                                    formData,
                                    { headers: { 'X-CSRF-TOKEN': this.field.config.csrfToken } },
                                )

                                return {
                                    success: 1,
                                    file: { id, url, name },
                                }
                            },
                            async uploadByUrl () {
                                return null
                            }
                        },
                    }
                },
                inlineCode: InlineCode,
                list: {
                    class: List,
                    inlineToolbar: true,
                },
                marker: Marker,
                quote: {
                    class: Quote,
                    inlineToolbar: true,
                },
                raw: Raw,
                table: {
                    class: Table,
                    inlineToolbar: true,
                },
                underline: Underline,
            },
            onChange: async () => {
                this.field.value = await this.editor.save()
            }
        })
    }
}
</script>
