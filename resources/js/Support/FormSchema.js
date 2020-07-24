'use strict'

import each from 'lodash/each'
import some from 'lodash/some'
import filter from 'lodash/filter'
import map from 'lodash/map'
import tap from 'lodash/tap'
import camelCase from 'lodash/camelCase'
import FormObject from './FormObject'
import { FormField } from './FormField'
import components from './utils/schemaComponents'

export default function FormSchema (data, schema) {
    each(schema, field => {
        const component = components[camelCase(field.type)].form

        const value = data.hasOwnProperty(field.name)
            ? data[field.name]
            : field.default

        this[field.name] = !!component.buildForSchema
            ? component.buildForSchema({ field, component, value })
            : new FormField(field, component, value)
    })

    // Now we define some useful methods...
    Object.defineProperties(this, {
        getSchema: {
            value: () => {
                return schema
            },
        },
        isDirty: {
            value: () => {
                return some(this, v => v.isDirty())
            }
        },
        getDirty: {
            value: () => {
                return filter(this, f => f.isDirty())
            },
        },
        getAll: {
            value: () => {
                return map(this, f => f)
            }
        },

        restart: {
            value: (attributes) => {
                return new FormSchema(attributes, this.getSchema())
            }
        },

        toFormObject: {
            value: (onlyDirty = true) => {
                const items = onlyDirty ? this.getDirty() : this.getAll()

                if (items.length === 0) {
                    return null
                }

                return tap(new FormObject(), formObject => {
                    each(items, (field) => {
                        field.commit(formObject)
                    })
                })
            }
        },
    })
}
