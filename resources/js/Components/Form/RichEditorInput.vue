<template>
    <f-form-field :errors="$page.errors.get(field.name)" :required="field.required">
        <template v-if="field.label" v-slot:label>{{ field.label }}</template>
        <div class="w-full">
            <!-- MENU BAR -->
            <editor-menu-bar :editor="editor" v-slot="{ commands, isActive }">
                <div class="rich-editor__menubar">
                    <button type="button" class="rich-editor__menubar__button" :class="{ 'is-active': isActive.paragraph() }" @click="commands.paragraph">
                        <f-icon class="text-lg" name="paragraph"/>
                    </button>

                    <button type="button" class="rich-editor__menubar__button" :class="{ 'is-active': isActive.heading({ level: 1 }) }" @click="commands.heading({ level: 1 })">
                        H1
                    </button>

                    <button type="button" class="rich-editor__menubar__button" :class="{ 'is-active': isActive.heading({ level: 2 }) }" @click="commands.heading({ level: 2 })">
                        H2
                    </button>

                    <button type="button" class="rich-editor__menubar__button" :class="{ 'is-active': isActive.heading({ level: 3 }) }" @click="commands.heading({ level: 3 })">
                        H3
                    </button>

                    <button type="button" class="rich-editor__menubar__button" :class="{ 'is-active': isActive.bullet_list() }" @click="commands.bullet_list">
                        <f-icon class="text-lg" name="list-ul"/>
                    </button>

                    <button type="button" class="rich-editor__menubar__button" :class="{ 'is-active': isActive.ordered_list() }" @click="commands.ordered_list">
                        <f-icon class="text-lg" name="list-ol"/>
                    </button>

                    <button type="button" class="rich-editor__menubar__button" :class="{ 'is-active': isActive.blockquote() }" @click="commands.blockquote">
                        <f-icon class="text-lg" name="quote-left"/>
                    </button>

                    <button type="button" class="rich-editor__menubar__button" @click="openFileManager">
                        <f-icon class="text-lg" name="image"/>
                    </button>
                    <portal to="modals">
                        <f-file-manager  v-if="showFileManager" :endpoint="field.config.links.fileManager" :file-types="['image/*']" :csrf-token="$page.app.csrfToken" @files-selected="files => onFilesSelected(files, commands.image)" @close="closeFileManager" />
                    </portal>

                    <button type="button" class="rich-editor__menubar__button" @click="commands.iframe">
                        <f-icon class="text-lg" name="film"/>
                    </button>

                    <!-- TABLES -->
                    <span class="inline-block flex items-center rounded" :class="{'border border-gray-300': isActive.table()}">
                        <button type="button" class="rich-editor__menubar__button" @click="commands.createTable({rowsCount: 3, colsCount: 3, withHeaderRow: false })">
                            <f-icon class="text-lg" name="table"/>
                        </button>
                        <span v-if="isActive.table()" class="inline-block flex items-center">
                            <button type="button" class="rich-editor__menubar__button" @click="commands.deleteTable">
                                <f-icon class="text-lg" name="trash"/>
                            </button>
                            <span class="divider"></span>
                            <button class="rich-editor__menubar__button" @click="commands.addColumnBefore">
                                <f-icon class="text-lg" name="caret-square-left"/>
                            </button>
                            <button type="button" class="rich-editor__menubar__button" @click="commands.addColumnAfter">
                                <f-icon class="text-lg" name="caret-square-right"/>
                            </button>
                            <button type="button" class="rich-editor__menubar__button" @click="commands.deleteColumn">
                                <f-icon class="text-lg" name="window-close"/>
                            </button>
                            <span class="rich-editor__menubar__divider"></span>
                            <button type="button" class="rich-editor__menubar__button" @click="commands.addRowBefore">
                                <f-icon class="text-lg" name="caret-square-up"/>
                            </button>
                            <button type="button" class="rich-editor__menubar__button" @click="commands.addRowAfter">
                                <f-icon class="text-lg" name="caret-square-down"/>
                            </button>
                            <button type="button" class="rich-editor__menubar__button" @click="commands.deleteRow">
                                <f-icon class="text-lg" name="window-close"/>
                            </button>
                            <span class="rich-editor__menubar__divider"></span>
                            <button class="rich-editor__menubar__button" @click="commands.toggleCellMerge">
                                <f-icon class="text-lg" name="expand"/>
                            </button>
                        </span>
					</span>

                    <span class="rich-editor__menubar__divider"></span>

                    <button type="button" class="rich-editor__menubar__button" :class="{ 'is-active': isActive.code_block() }" @click="commands.code_block">
                        <f-icon class="text-lg" name="terminal"/>
                    </button>

                    <button type="button" class="rich-editor__menubar__button" @click="commands.horizontal_rule">
                        <f-icon class="text-lg" name="ruler-horizontal"/>
                    </button>

                    <button type="button" class="rich-editor__menubar__button" @click="commands.undo">
                        <f-icon class="text-lg" name="undo"/>
                    </button>

                    <button type="button" class="rich-editor__menubar__button" @click="commands.redo">
                        <f-icon class="text-lg" name="redo"/>
                    </button>
                </div>
            </editor-menu-bar>

            <!-- MENU BUBBLE -->
            <editor-menu-bubble class="rich-editor__menububble" :editor="editor" @hide="hideLinkMenu" v-slot="{ commands, isActive, getMarkAttrs, menu }">
                <div class="rich-editor__menububble" :class="{ 'is-active': menu.isActive }" :style="`left: ${menu.left}px; bottom: ${menu.bottom}px;`">
                    <!-- GENERAL -->
                    <button type="button" class="rich-editor__menububble__button" :class="{ 'is-active': isActive.bold() }" @click="commands.bold">
                        <f-icon class="text-lg" name="bold"/>
                    </button>

                    <button type="button" class="rich-editor__menububble__button" :class="{ 'is-active': isActive.italic() }" @click="commands.italic">
                        <f-icon class="text-lg" name="italic"/>
                    </button>

                    <button type="button" class="rich-editor__menububble__button" :class="{ 'is-active': isActive.strike() }" @click="commands.strike">
                        <f-icon class="text-lg" name="strikethrough"/>
                    </button>

                    <button type="button" class="rich-editor__menububble__button" :class="{ 'is-active': isActive.underline() }" @click="commands.underline">
                        <f-icon class="text-lg" name="underline"/>
                    </button>

                    <button type="button" class="rich-editor__menububble__button" :class="{ 'is-active': isActive.code() }" @click="commands.code">
                        <f-icon class="text-lg" name="code"/>
                    </button>

                    <!-- LINKS -->
                    <form class="rich-editor__menububble__form" v-if="linkMenuIsActive" @submit.prevent="setLinkUrl(commands.link, linkUrl)">
                        <input class="rich-editor__menububble__input" type="text" v-model="linkUrl" placeholder="https://" ref="linkInput" @keydown.esc="hideLinkMenu"/>
                        <button class="rich-editor__menububble__button" @click="setLinkUrl(commands.link, null)" type="button">
                            <f-icon class="text-lg" name="trash"/>
                        </button>
                    </form>
                    <template v-else>
                        <button class="rich-editor__menububble__button" @click="showLinkMenu(getMarkAttrs('link'))" :class="{ 'is-active': isActive.link() }">
                            <f-icon class="text-lg" name="link"/>
                        </button>
                    </template>
                </div>
            </editor-menu-bubble>

            <editor-content class="rich-editor__editor__content form-input" :editor="editor"/>
        </div>
    </f-form-field>
</template>

<script>
import axios from 'axios'
import { Editor, EditorContent, EditorMenuBar, EditorMenuBubble } from 'tiptap'
import {
    Blockquote,
    CodeBlock,
    HardBreak,
    Heading,
    HorizontalRule,
    OrderedList,
    BulletList,
    ListItem,
    TodoItem,
    TodoList,
    Image,
    Bold,
    Code,
    Italic,
    Link,
    Strike,
    Underline,
    History,
    Table,
    TableHeader,
    TableCell,
    TableRow,
} from 'tiptap-extensions'
import Iframe from './tiptap/iframe'
import { v4 as uuidv4 } from 'uuid'
import FormInput from '../Mixins/FormInput'

export default {
    name: 'RichEditorInput',
    mixins: [FormInput],
    inheritAttrs: false,
    components: {
        EditorMenuBar,
        EditorMenuBubble,
        EditorContent,
    },

    props: {
        field: {
            type: Object,
            required: true,
        },
    },

    data: () => ({
        editor: null,
        linkUrl: null,
        linkMenuIsActive: false,
        showFileManager: false,
    }),

    methods: {
        /**
         * @param {FormObject} formObject
         */
        commit (formObject) {
            formObject.put(this.field.name, this.field.value)
        },

        async onFileAdded ({ attachment }) {
            if (attachment.file) {
                const data = new FormData()
                data.append('Content-Type', attachment.file.type)
                data.append('files[]', attachment.file)

                const { data: { urls } } = await axios.post(this.field.config.links.self, data, {
                    onUploadProgress: function (progressEvent) {
                        attachment.setUploadProgress(
                            Math.round((progressEvent.loaded * 100) / progressEvent.total)
                        )
                    },
                })

                return attachment.setAttributes({
                    url: urls[0],
                    href: urls[0],
                })
            }
        },

        onFileRemoved ({ attachment: { attachment } }) {
            // TODO: Delete file on API...
            console.log(attachment)
        },

        showLinkMenu (attrs) {
            this.linkUrl = attrs.href
            this.linkMenuIsActive = true
            this.$nextTick(() => {
                this.$refs.linkInput.focus()
            })
        },

        hideLinkMenu () {
            this.linkUrl = null
            this.linkMenuIsActive = false
        },

        setLinkUrl (command, url) {
            command({ href: url })
            this.hideLinkMenu()
        },

        openFileManager () {
            this.showFileManager = true
        },

        closeFileManager () {
            this.showFileManager = false
        },

        onFilesSelected (files, command) {
            this.closeFileManager()

            files.forEach(f => {
                command({
                    src: f.url,
                })
            })
        },

        cleanUp () {
            this.editor.destroy()

            if (this.field.config.acceptFiles) {
                // TODO: Delete draft attachments
            }
        },
    },

    mounted () {
        this.editor = new Editor({
            content: this.field.value,
            extensions: [
                new Blockquote(),
                new BulletList(),
                new CodeBlock(),
                new HardBreak(),
                new Heading({ levels: [1, 2, 3] }),
                new ListItem(),
                new OrderedList(),
                new TodoItem(),
                new TodoList(),
                new Image(),
                new Link(),
                new Bold(),
                new Code(),
                new Italic(),
                new Strike(),
                new Underline(),
                new History(),
                new HorizontalRule(),
                new Table({
                    resizable: true,
                }),
                new TableHeader(),
                new TableCell(),
                new TableRow(),
                new Iframe(),
            ],
            onUpdate: ({ getHTML }) => {
                this.onInput(getHTML())
            }
        })
    },

    beforeDestroy () {
        this.cleanUp()
    }
}
</script>

<style scoped>
.menubar {
    @apply flex items-center py-2;
}

.menububble {
    @apply absolute flex items-center bg-gray-900 rounded z-10 p-1 mb-1;
    transform: translateX(-50%);
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.2s, visibility 0.2s;
}

.menububble.is-active {
    opacity: 1;
    visibility: visible;
}

.menububble__form {
    @apply flex items-center;
}

.menububble__button {
    @apply p-1 flex items-center justify-center rounded bg-transparent font-normal text-sm text-gray-300 mx-1;
}

.menububble__button:hover {
    @apply bg-gray-800 text-gray-200;
}

.menububble__button.is-active {
    @apply text-gray-100 bg-gray-800;
}

.menububble__input {
    @apply text-white bg-transparent border-0;
}

.menubar__button {
    @apply w-8 h-6 flex items-center justify-center rounded bg-transparent font-bold text-sm text-gray-700 mx-1;
}

.menubar__button:hover {
    @apply bg-gray-200 text-gray-800;
}

.menubar__button.is-active {
    @apply bg-gray-300 text-gray-900;
}

.divider {
    @apply mx-1 h-4 border-l border-gray-500;
}

.editor__content {
    min-height: 200px;
}
</style>

<style>
.editor__content {
    @apply relative;
    max-height: 600px;
    overflow-y: auto;
}

.editor__content .ProseMirror {
    outline: none;
    min-height: 200px;
}

.editor__content h3 {
    @apply text-lg text-gray-700 font-semibold;
}

.editor__content h2 {
    @apply text-xl text-gray-800 font-bold;
}

.editor__content h1 {
    @apply text-2xl text-gray-900 font-bold;
}

.editor__content ul {
    @apply p-8;
    list-style: disc;
}

.editor__content ol {
    @apply p-8;
    list-style: decimal;
}

.editor__content blockquote {
    @apply p-4 bg-gray-200 italic text-base text-gray-900 rounded-lg;
}

.editor__content code {
    @apply p-1 bg-gray-200 text-gray-800 text-base font-mono rounded;
}

.editor__content pre {
    @apply p-4 bg-gray-900 text-gray-100 text-base font-mono rounded-lg;
}

.editor__content pre code {
    @apply bg-transparent text-gray-100 text-base font-mono;
}

.editor__content a {
    @apply underline text-blue-500;
}

.editor__content table {
    @apply table-auto border border-gray-500 w-full;
}

.editor__content td {
    @apply p-2 border border-gray-500 text-base text-gray-700;
}

.editor__content tr {
    @apply border border-gray-500 text-base text-gray-700;
}

.editor__content .iframe__embed {
    @apply w-full bg-gray-200 rounded;
    height: 15rem;
    border: 0;
}

.editor__content .iframe__input {
    @apply bg-white border-2 border-gray-400 rounded;
    display: block;
    width: 100%;
    font: inherit;
    padding: 0.3rem 0.5rem;
}
</style>
