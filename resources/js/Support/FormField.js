import Vue from 'vue'

export function FormFieldFactory (field, component, value, customData = {}) {
    return Vue.observable({
        ...field,
        component,
        value,
        originalValue: value,
        commitCallback: null,
        setCommitCallback (callbackFn) {
            this.commitCallback = callbackFn
        },
        commit (formObject) {
            this.commitCallback(formObject)
        },
        isDirty () {
            return this.value !== this.originalValue
        },
        ...customData,
    })
}
