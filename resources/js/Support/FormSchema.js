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
import { FormField } from './FormField'
import ImageInput from '../Components/Form/ImageInput'

const components = {
    string: StringInput,
    text: TextInput,
    toggle: ToggleInput,
    select: SelectInput,
    image: ImageInput,
    file: StringInput,
    date: DateTimeInput,
    richEditor: RichEditorInput,
}

export default function FormSchema (data, schema) {
    each(schema, field => {
        const component = components[camelCase(field.type)]

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
