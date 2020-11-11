export function Modal (show) {
    return {
        show,
        classes: 'opacity-0',
        init () {
            this.$watch('show', isOpen => {
                if (isOpen) {
                    this.classes = 'opacity-25'
                    return
                }

                this.classes = 'opacity-0'
            })
        },
        close () {
            this.show = false
        }
    }
}
