<template>
    <div class="flex items-center justify-center font-bold">
        <f-button v-if="meta.currentPage > 1" v-bind="getButtonAttributes(meta.firstPageUrl)" @click="onClick(meta.firstPageUrl)" color="brand" variant="outline" class="mx-1">
            <f-icon name="step-backward"/>
        </f-button>
        <f-button v-if="meta.previousPageUrl" v-bind="getButtonAttributes(meta.previousPageUrl)" @click="onClick(meta.previousPageUrl)" color="brand" variant="outline" class="mx-1">
            <f-icon name="angle-left"/>
        </f-button>
        <span v-if="hasMoreBefore" class="mx-1 px-1 text-gray-700"><f-icon name="ellipsis-h"/></span>
        <f-button v-for="page in meta.pageUrls" :key="page.number" v-bind="getButtonAttributes(page.url)" @click="onClick(page.url)" color="brand" :variant="page.isCurrent ? 'solid' : 'outline'" class="mx-1">{{ page.number }}</f-button>
        <span v-if="hasMoreAfter" class="mx-1 px-1 text-gray-700"><f-icon name="ellipsis-h"/></span>
        <f-button v-if="meta.nextPageUrl" v-bind="getButtonAttributes(meta.nextPageUrl)" @click="onClick(meta.nextPageUrl)" color="brand" variant="outline" class="mx-1">
            <f-icon name="angle-right"/>
        </f-button>
        <f-button v-if="meta.currentPage < meta.lastPage" v-bind="getButtonAttributes(meta.lastPageUrl)" @click="onClick(meta.lastPageUrl)" color="brand" variant="outline" class="mx-1">
            <f-icon name="step-forward"/>
        </f-button>
    </div>
</template>

<script>
export default {
    name: 'Paginator',

    props: {
        meta: {
            type: Object,
            required: true,
        },
        asLinks: {
            type: Boolean,
            default: true,
        }
    },

    computed: {
        hasMoreBefore () {
            if (! this.meta.pageUrls || this.meta.pageUrls.length === 0) {
                return false
            }

            return this.meta.pageUrls[0].number > 1
        },

        hasMoreAfter () {
            if (!this.meta.pageUrls || this.meta.pageUrls.length === 0) {
                return false
            }

            return this.meta.pageUrls[this.meta.pageUrls.length - 1].number < this.meta.lastPage
        },
    },

     methods: {
        getButtonAttributes (link) {
            if (this.asLinks) {
                return {
                    href: link,
                }
            }

            return {}
        },

         onClick (link) {
            if (! this.asLinks) {
                this.$emit('changed', link)
            }
         }
     }
}
</script>
