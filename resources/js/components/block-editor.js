export function BlockEditor (options) {
    return {
        newBlockPosition: -1,
        isProcessing: false,

        get shouldShowAvailableBlocks () {
            return this.newBlockPosition > -1
        },

        showAvailableBlocks (position) {
            this.newBlockPosition = position
            document.body.style.overflow = 'hidden'
        },

        hideAvailableBlocks () {
            this.newBlockPosition = -1
            document.body.style.overflow = 'auto'
        },

        async selectNewBlock (key) {
            if (! this.isProcessing) {
                this.isProcessing = true

                await this.$wire.addBlock(key, this.newBlockPosition)

                this.hideAvailableBlocks()
                this.isProcessing = false
            }
        },

        async removeBlock (position) {
            if (! this.isProcessing) {
                this.isProcessing = true

                await this.$wire.removeBlock(position)

                this.isProcessing = false
            }
        }
    }
}
