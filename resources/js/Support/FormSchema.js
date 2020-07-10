'use strict'

import each from 'lodash/each'
import some from 'lodash/some'
import filter from 'lodash/filter'
import map from 'lodash/map'
import tap from 'lodash/tap'
import camelCase from 'lodash/camelCase'
import FormRichEditorInput from '../Components/FormRichEditorInput'
import FormDateTimeInput from '../Components/FormDateTimeInput'
import FormStringInput from '../Components/FormStringInput'
import FormSingleChoiceInput from '../Components/FormSingleChoiceInput'
import FormSwitchInput from '../Components/FormSwitchInput'
import FormTextInput from '../Components/FormTextInput'
import FormObject from './FormObject'

const types = {
    string: FormStringInput,
    text: FormTextInput,
    boolean: FormSwitchInput,
    singleChoice: FormSingleChoiceInput,
    file: FormStringInput,
    date: FormDateTimeInput,
    richEditor: FormRichEditorInput,
}

export function defaultProperties ({ key, value, type, field }) {
    return {
        component: {
            enumerable: true,
            value: type,
        },
        name: {
            enumerable: true,
            value: key,
        },
        originalValue: {
            enumerable: true,
            value: value,
        },
        label: {
            enumerable: true,
            value: field.label,
        },
        config: {
            enumerable: true,
            value: field.config || {},
        },
        isRequired: {
            enumerable: true,
            value: field.required
        },
        isDirty: {
            enumerable: true,
            get () {
                return this.value !== this.originalValue
            }
        },
    }
}

export function buildForSchema (obj, { key, value, field, type }) {
    // First we add the schema field to the form data object.
    obj[key] = {
        value,
    }

    const props = defaultProperties.call(obj, {
        key,
        field,
        type,
        value,
    })

    Object.defineProperties(obj[key], props)
}

export default function FormSchema (data, schema) {
    each(schema, (field, key) => {
        const camelCaseType = camelCase(field.type)
        const type = types[camelCaseType]

        const makeFn = type.buildForSchema
            ? type.buildForSchema
            : buildForSchema

        makeFn(this, {
            key,
            field,
            type,
            value: data.hasOwnProperty(key) ? data[key] : type.default
        })
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
                return some(this, v => v.isDirty)
            }
        },
        getDirty: {
            value: () => {
                return filter(this, f => f.isDirty)
            },
        },
        getAll: {
            value: () => {
                return map(this, f => f)
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
