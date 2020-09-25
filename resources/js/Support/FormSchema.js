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
function generateMethods (form, $bus) {
    const obj = {}

    Object.defineProperties(obj, {
        $on: {
            value: (eventName, callback) => {
                $bus.$on(eventName, callback)
            }
        },
        $emit: {
            value: (eventName, data) => {
                $bus.$emit(eventName, data)
            }
        },
        isDirty: {
            value: () => some(form, field => field.isDirty()),
        },
        getDirty: {
            value: () => filter(form, field => field.isDirty()),
        },
        getField: {
            value: (name) => form[name]
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
                            throw new Error('No Commit Callback set on the field ' + field.attribute)
                        }

                        field.commitCallback(formObject)
                    })
                })
            }
        }
    })

    each(form, field => {
        Object.defineProperty(obj, field.attribute, {
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
 */
export function FormSchemaFactory (data) {
    const $bus = new Vue
    const __fields = {}

    each(data, item => {
        const component = components[camelCase(item.field.component)].form

        const value = data.hasOwnProperty(item.field.attribute)
            ? data[item.field.attribute]
            : item.field.config.default

        __fields[item.field.attribute] = Vue.observable(
            !!component.buildForSchema
                ? component.buildForSchema({
                    field: item.field,
                    value: item.value,
                    component,
                    data
                })
                : FormFieldFactory(item.field, component, item.value, {})
        )

        __fields[item.field.attribute].onValueChanged((value) => {
            $bus.$emit(`${item.field.attribute}:value-changed`, value)
        })
    })

    return generateMethods(__fields, $bus)
}
