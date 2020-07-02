'use strict'

import each from 'lodash/each'
import some from 'lodash/some'
import find from 'lodash/find'
import camelCase from 'lodash/camelCase'

export function defaultProperties ({ key, value, type, field }) {
    return {
        name: {
            enumerable: true,
            value: key,
        },
        originalValue: {
            enumerable: true,
            value: value,
        },
        component: {
            enumerable: true,
            value: type.component,
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
                return this.transformValue() !== this.originalValue
            }
        },
        transformValue: {
            enumerable: false,
            value: function () {
                return this.value
            }
        }
    }
}

export function defaultMakeFn ({ key, value, field, type }) {
    // First we add the schema field to the form data object.
    this[key] = {
        value,
    }

    const props = defaultProperties.call(this, {
        key,
        field,
        type,
        value,
    })

    Object.defineProperties(this[key], props)
}

const types = {
    string: {
        component: 'f-form-string-input',
        default: '',
        make: null,
    },
    text: {
        component: 'f-form-text-input',
        default: '',
        make: null,
    },
    boolean: {
        component: 'f-form-switch-input',
        default: true,
        make: null,
    },
    singleChoice: {
        component: 'f-form-single-choice-input',
        default: null,
        make ({ key, field, type, value }) {
            const filteredValue = find(field.config.options, o => o.value === value)

            // First we add the schema field to the form data object.
            this[key] = {
                value: filteredValue || type.default,
            }

            const defProps = defaultProperties.call(this, {
                key,
                field,
                type,
                value,
            })

            const props = {
                ...defProps,
                transformValue: {
                    value: function () {
                        return (this.value && this.value.hasOwnProperty('value'))
                            ? this.value.value
                            : null
                    }
                }
            }

            Object.defineProperties(this[key], props)
        },
    },
    file: {
        component: 'f-form-string-input',
        default: null,
        make: null,
    },
}

export default function FormSchema (data, schema) {
    each(schema, (field, key) => {
        const camelCaseType = camelCase(field.type)
        const type = types[camelCaseType]

        const makeFn = type.make === null
            ? defaultMakeFn.bind(this)
            : type.make.bind(this)

        makeFn({
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
                let data = {}

                each(this, (v, k) => {
                    if (v.isDirty) {
                        data[k] = v.hasOwnProperty('transformValue')
                            ? v.transformValue()
                            : v.value
                    }
                })

                return data
            },
        },
        getAll: {
            value: () => {
                let data = {}

                each(this, (v, k) => {
                    data[k] = v.hasOwnProperty('transformValue')
                        ? v.transformValue()
                        : v.value
                })

                return data
            }
        }
    })
}
