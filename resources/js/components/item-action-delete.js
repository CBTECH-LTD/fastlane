export function ItemActionDelete () {
    return {
        waitingConfirmation: false,
        attemptingDelete: false,
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
        async confirm () {
            if (this.attemptingDelete) {
                return
            }

            this.attemptingDelete = true

            await this.$wire.confirmAction()

            this.attemptingDelete = false
            this.waitingConfirmation = false
        },
    }
}
