import FBoxedCard from './Components/BoxedCard'
import FButton from './Components/Button'
import FFormField from './Components/Form/Field'
import FFormDateTimeInput from './Components/Form/DateTimeInput'
import FFormRadioInput from './Components/Form/RadioInput'
import FFormRichEditorInput from './Components/Form/RichEditorInput'
import FFormSelectInput from './Components/Form/SelectInput'
import FFormStringInput from './Components/Form/StringInput'
import FFormToggleInput from './Components/Form/ToggleInput'
import FFormTextInput from './Components/Form/TextInput'
import FformFileInput from './Components/Form/FileInput'
import FFormFieldPanel from './Components/Form/FieldPanel'
import FFormRoot from './Components/Form/FormRoot'
import FIcon from './Components/Icon'
import FLink from './Components/Link'
import FListCard from './Components/ListCard'
import FListItemAction from './Components/ListItemAction'
import FMenuGroup from './Components/MenuGroup'
import FMenuLink from './Components/MenuLink'
import FNavigationList from './Components/NavigationList'
import FSidebar from './Components/Sidebar'
import FSpinner from './Components/Spinner'
import FTableCard from './Components/TableCard'
import FTheAppLayout from './Components/TheAppLayout'
import FTrix from './Components/Trix'

export default {
    install (Vue) {
        Vue.config.ignoredElements = ['trix-editor']

        Vue.component('f-boxed-card', FBoxedCard)
        Vue.component('f-button', FButton)
        Vue.component('f-icon', FIcon)
        Vue.component('f-link', FLink)
        Vue.component('f-list-card', FListCard)
        Vue.component('f-list-item-action', FListItemAction)
        Vue.component('f-navigation-list', FNavigationList)
        Vue.component('f-table-card', FTableCard)
        Vue.component('f-sidebar', FSidebar)
        Vue.component('f-spinner', FSpinner)
        Vue.component('f-the-app-layout', FTheAppLayout)
        Vue.component('f-trix', FTrix)

        // Menu specific..)
        Vue.component('f-menu-group', FMenuGroup)
        Vue.component('f-menu-link', FMenuLink)

        // Form specific..)
        Vue.component('f-form-root', FFormRoot)
        Vue.component('f-form-field', FFormField)
        Vue.component('f-form-field-panel', FFormFieldPanel)
        Vue.component('f-form-date-time-input', FFormDateTimeInput)
        Vue.component('f-form-file-input', FformFileInput)
        Vue.component('f-form-radio-input', FFormRadioInput)
        Vue.component('f-form-rich-editor-input', FFormRichEditorInput)
        Vue.component('f-form-single-choice-input', FFormSelectInput)
        Vue.component('f-form-string-input', FFormStringInput)
        Vue.component('f-form-text-input', FFormTextInput)
        Vue.component('f-form-toggle-input', FFormToggleInput)
    }
}
