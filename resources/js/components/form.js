export function Form (options) {
    return {
        submit () {
            this.$el.focus()

            setTimeout(() => {
                this.$wire.submit()
            }, 200)
        }
    }
}
