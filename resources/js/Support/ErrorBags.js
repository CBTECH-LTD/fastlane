import getData from 'lodash/get'

export default function ErrorBagsFactory (errors) {
    const def = {
        default: {}
    }

    const instance = {
        bags: errors === undefined
            ? def
            : { ...def, ...errors },

        get (field, bag = 'default') {
            return getData(this.bags, `${bag}.${field}`, [])
        },

        getBag (bag = 'default') {
            return getData(this.bags, bag, {})
        }
    }

    return instance
}

// const ErrorBagsPlugin = {
//     install: function (Vue, options) {
//         Vue.$errors = {
//             get () {
//
//             }
//         }
//     }
// }
