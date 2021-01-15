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

        selectNewBlock (key) {
            alert(`${key}: ${this.newBlockPosition}`)
        }
    }
}
