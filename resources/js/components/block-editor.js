export function BlockEditor (options) {
    return {
        newBlockPosition: -1,
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
            await this.$wire.addBlock(key, this.newBlockPosition)
        }
    }
}
