
<template>
    <div class="files__list">
        <div v-for="(file, index) in files" :key="file.id" class="relative w-32 p-2" :draggable="file.draggable" @dragstart="e => onDragStart(e, file, index)" @dragend="onDragEnd" @dragenter="e => onDragEnter(e, file)" @dragleave="onDragLeave" @dragover="onDragOver" @drop="e => onDrop(e, file)">
            <div class="absolute top-0 left-0 z-10">
                <input v-if="canSelectFile(file)" type="checkbox" class="form-checkbox p-3" :checked="file.selected" @input="toggleFile(file)">
            </div>
            <div @dblclick="() => onDoubleClick(file)" class="files__list__box relative w-full h-32 flex flex-col bg-gray-100 border-2 rounded-lg shadow-md overflow-hidden text-xs" :class="file.selected ? 'border-purple-300' : 'border-transparent'">
                <div class="w-full h-20 flex items-center justify-center text-purple-600 text-xs font-semibold uppercase rounded overflow-hidden"
                     :class="isDirectory(file) ? 'bg-orange-200' : 'bg-purple-200'"
                     :style="isImage(file) ? `background-image: url('${file.url}'); background-size: cover; background-repeat: no-repeat; background-position: center;` : ''">
                                    <span v-if="!isImage(file)">
                                        <f-icon v-if="file.icon" :name="file.icon" class="text-5xl" />
                                        <span v-else>{{ file.extension }}</span>
                                    </span>
                </div>
                <div class="flex flex-col flex-grow justify-center px-1 overflow-hidden text-center">
                                    <span class="truncate w-full">
                                        {{ file.name }}
                                    </span>
                    <a v-if="file.url" :href="file.url" target="_blank" class="block text-brand-700 underline">Download</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable'
import VSelect from 'vue-select'
import filter from 'lodash/filter'
import map from 'lodash/map'

export default {
    name: "DraggableFileList",
    components: { draggable, VSelect },

    props: {
        list: {
            type: Array,
            required: true,
        },
        canSelectFile: {
            type: Function,
            required: true,
        },
        onDoubleClick: {
            type: Function,
            required: true,
        },
        ghostClass: {
            type: String,
            default: 'files__list--ghost',
        },
        targetClass: {
            type: String,
            default: 'files__list--target',
        },
        dragClass: {
            type: String,
            default: 'files__list--drag',
        },
    },

    computed: {
        files () {
            return filter(this.list, i => i.visible)
        },

        selectedFiles () {
            return filter(this.list, f => f.selected)
        },
    },

    methods: {
        isImage (file) {
            return file.mimetype && file.mimetype.startsWith('image/')
        },

        isDirectory (file) {
            return file.mimetype === 'fastlane/directory'
        },

        toggleFile (file) {
            this.$emit('toggle-select', file)
        },

        onDragStart (ev, file, fileIndex) {
            ev.target.classList.add(this.ghostClass)

            ev.dataTransfer.effectAllowed = 'move'
            ev.dataTransfer.setData('file/data', JSON.stringify(file))
            ev.dataTransfer.setData('file/index', fileIndex)
        },

        onDragEnd (ev) {
            ev.target.classList.remove(this.ghostClass)
        },

        onDragEnter (ev, file) {
            if (this.isDirectory(file)) {
                ev.currentTarget.classList.add(this.targetClass)
            }
        },

        onDragLeave (ev) {
            ev.currentTarget.classList.remove(this.targetClass)
        },

        onDragOver (ev) {
            if (ev.preventDefault) {
                ev.preventDefault()
            }

            return false
        },

        async onDrop (ev, targetItem) {
            ev.stopPropagation()

            ev.currentTarget.classList.remove(this.targetClass)

            if (! this.isDirectory(targetItem)) {
                return
            }

            const draggedItem = JSON.parse(ev.dataTransfer.getData('file/data'))
            const draggedItemIndex = JSON.parse(ev.dataTransfer.getData('file/index'))

            this.$emit('move',{
                target: targetItem,
                files: [draggedItem].concat(this.selectedFiles),
                filesIndexes: [draggedItemIndex],
            })
        },
    }
}
</script>

<style scoped>
.files__list {
    @apply w-full p-4 flex justify-center flex-wrap;
}

.files__list--ghost {
    @apply opacity-50;
}

.files__list--ghost .files__list__box {
    @apply border-gray-300 shadow-lg;
}

.files__list--target {
    @apply opacity-75;
}

.files__list--target * {
    pointer-events: none;
}

.files__list--target .files__list__box {
    @apply border-dashed border-purple-400 shadow-lg;
}

.files__list--drag {
    @apply opacity-50;
}

.files__list--drag .files__list__box {
    @apply border-green-400 shadow-lg;
}
</style>
