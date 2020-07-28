import Vue from 'vue'
import each from 'lodash/each'
import some from 'lodash/some'
import filter from 'lodash/filter'
import tap from 'lodash/tap'
import camelCase from 'lodash/camelCase'
import components from './utils/schemaComponents'
import FormObject from './FormObject'
import { FormFieldFactory } from './FormField'

/**
 * Define methods to be accessible on the instance of FormSchema.
 */
function generateMethods (form) {
    const obj = {}

    Object.defineProperties(obj, {
        isDirty: {
            value: () => some(form, field => field.isDirty()),
        },
        getDirty: {
            value: () => filter(form, field => field.isDirty()),
        },
        getAll: {
            value: () => form,
        },
        toFormObject: {
            value: (onlyDirty = true) => {
                const items = onlyDirty ? obj.getDirty() : obj.getAll()

                if (items.length === 0) {
                    return null
                }

                return tap(new FormObject(), formObject => {
                    each(items, (field) => {
                        if (!field.commitCallback) {
                            throw new Error('No Commit Callback set on the field ' + field.name)
                        }

                        field.commitCallback(formObject)
                    })
                })
            }
        }
    })

    each(form, field => {
        Object.defineProperty(obj, field.name, {
            get () {
                return field.value
            },
            enumerable: true,
        })
    })

    return obj
}

/**
 * Generate a new Form Schema instance.
 *
 * @param data
 * @param schema
 */
export function FormSchemaFactory (data, schema) {
    let __fields = {}

    each(schema, field => {
        const component = components[camelCase(field.type)].form

        const value = data.hasOwnProperty(field.name)
            ? data[field.name]
            : field.default

        __fields[field.name] = Vue.observable(
            !!component.buildForSchema
                ? component.buildForSchema({ field, component, value, data })
                : FormFieldFactory(field, component, value)
        )
    })

    return generateMethods(__fields)
}
