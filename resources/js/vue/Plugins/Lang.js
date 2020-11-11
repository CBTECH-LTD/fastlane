import get from 'lodash/get'
import each from 'lodash/each'

export default {
    install (Vue) {
        function replaceProps (translation, replace) {
            each(replace || {}, (value, k) => {
                const regExp = new RegExp(`:${k}`, 'g')
                translation = translation.replace(regExp, value)
            })

            return translation
        }

        Vue.prototype.$l = function (key, replace) {
            return replaceProps(get(window.fastlane.translations, key, key), replace)
        }

        Vue.prototype.$lc = function (key, count, replace) {
            const translations = get(window.fastlane.translations, key, key).split('|')

            if (count > 1) {
                return replaceProps(
                    translations[translations.length - 1],
                    { replace, ...count }
                )
            }

            if (count === 1) {
                return replaceProps(
                    translations[1] || translations[0],
                    { replace, ...count }
                )
            }

            return replaceProps(translations[0], { replace, ...count })
        }
    }
}
