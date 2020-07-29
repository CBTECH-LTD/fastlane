'use strict'

import each from 'lodash/each'
import camelCase from 'lodash/camelCase'
import components from './utils/schemaComponents'

export default function ListSchema (schema) {
    each(schema, field => {
        this[field.name] = {
            ...field,
            component: components[camelCase(field.type)].list,
        }
    })
}
