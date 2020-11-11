export function ItemActionDelete () {
    return {
        waitingConfirmation: false,
        classes: 'bg-transparent',
        init () {
            this.$watch('waitingConfirmation', (val) => {
                this.classes = !!val ? 'bg-yellow-300' : 'bg-transparent'
            })
        },
        showConfirmation () {
            this.waitingConfirmation = true
        },
        cancel () {
            this.waitingConfirmation = false
        },
        confirm () {
            this.waitingConfirmation = false

            this.$wire.confirmAction()
        },
    }
}
