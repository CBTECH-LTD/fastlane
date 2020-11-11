export function StickyTitleBar () {
    return {
        init () {
            let observer = new IntersectionObserver(entries => {
                this.stickyBarClass = (!entries[0].isIntersecting)
                    ? 'is-floating'
                    : ''
            }, {
                rootMargin: '-20px 0px',
                threshold: 1
            })

            observer.observe(this.$el)
        }
    }
}
