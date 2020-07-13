'use strict'

import each from 'lodash/each'
import some from 'lodash/some'
import filter from 'lodash/filter'
import map from 'lodash/map'
import tap from 'lodash/tap'
import camelCase from 'lodash/camelCase'
import RichEditorInput from '../Components/Form/RichEditorInput'
import DateTimeInput from '../Components/Form/DateTimeInput'
import StringInput from '../Components/Form/StringInput'
import SelectInput from '../Components/Form/SelectInput'
import ToggleInput from '../Components/Form/ToggleInput'
import TextInput from '../Components/Form/TextInput'
import FormObject from './FormObject'

const types = {
    string: StringInput,
    text: TextInput,
    toggle: ToggleInput,
    select: SelectInput,
    file: StringInput,
    date: DateTimeInput,
    richEditor: RichEditorInput,
}

export function defaultProperties ({ value, type, field }) {
    return {
        component: {
            enumerable: true,
            value: type,
        },
        name: {
            enumerable: true,
            value: field.name,
        },
        originalValue: {
            enumerable: true,
            value: value,
        },
        label: {
            enumerable: true,
            value: field.label,
        },
        placeholder: {
            enumerable: true,
            value: field.placeholder,
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

function makeSchemaField ({ value, field, type }) {
    // First we add the schema field to the form data object.
    const obj = {
        value,
    }

    const props = defaultProperties.call(obj, {
        field,
        type,
        value,
    })

    Object.defineProperties(obj, props)

    return obj
}

export default function FormSchema (data, schema) {
    each(schema, field => {
        const type = types[camelCase(field.type)]

        const params = {
            field,
            type,
            value: data.hasOwnProperty(field.name)
                ? data[field.name]
                : field.default
        }

        const obj = makeSchemaField(params)

        if (type.buildForSchema) {
            type.buildForSchema(obj, params)
        }

        this[field.name] = obj
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
