import Vue from 'vue'

export class FormField {
    value = null
    #originalValue = null
    #field = null
    #component = null
    #commitCallback = null

    constructor (field, component, value = null) {
        this.value = value
        this.#originalValue = value
        this.#field = field
        this.#component = component
    }

    get name () {
        return this.#field.name
    }

    get component () {
        return this.#component
    }

    get originalValue () {
        return this.#originalValue
    }

    get label () {
        return this.#field.label
    }

    get placeholder () {
        return this.#field.placeholder
    }

    get config () {
        return this.#field.config
    }

    isRequired () {
        return this.#field.required
    }

    isDirty () {
        return this.value !== this.#originalValue
    }

    setCommitCallback (callbackFn) {
        this.#commitCallback = callbackFn
    }

    commit (formObject) {
        if (!this.#commitCallback) {
            throw new Error('No Commit Callback set on the field')
        }

        this.#commitCallback(formObject)
    }
}

export function FormFieldFactory (field, component, value, customData = {}) {
    return Vue.observable({
        component,
        value,
        name: field.name,
        label: field.label,
        placeholder: field.placeholder,
        originalValue: value,
        required: field.required,
        config: field.config,
        commitCallback: null,
        ...customData,
        setCommitCallback (callbackFn) {
            this.commitCallback = callbackFn
        },
        commit (formObject) {
            this.commitCallback(formObject)
        },
        isDirty () {
            return this.value !== this.originalValue
        },
    })
}
