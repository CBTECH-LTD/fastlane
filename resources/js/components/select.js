import SlimSelect from 'slim-select'
import 'slim-select/dist/slimselect.min.css'

export function Select (options) {
    return {
        instance: null,
        attribute: options.attribute,
        taggable: options.taggable,
        multiple: options.multiple,
        init () {
            this.instance = new SlimSelect({
                select: this.$el,
                closeOnSelect: !!options.taggable,
                addable: options.taggable
                    ? function (value) {
                        if (value.trim() === '') {
                            return false
                        }

                        return value
                    }
                    : false,
                onChange: (data) => {
                    const value = (this.multiple)
                        ? data.map(it => it.value)
                        : data.value

                    this.$wire.set(this.attribute, value)
                }
            })
        },
    }
}
