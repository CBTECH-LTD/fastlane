export default {
    install (Vue) {
        Vue.prototype.$asset = function (path) {
            return `${this.$page.app.baseUrl}/${path}`
        }
    }
}
