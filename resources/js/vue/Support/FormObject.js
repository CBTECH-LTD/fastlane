export default class FormObject {
    constructor (data = {}) {
        this.data = data
    }

    put (key, value) {
        this.data[key] = value
        return this
    }

    remove (key) {
        delete this.data[key]
    }

    all () {
        return this.data
    }
}
