import FBoxedCard from './Components/BoxedCard'
import FButton from './Components/Button'
import FFormField from './Components/FormField'
import FFormDateTimeInput from './Components/FormDateTimeInput'
import FFormRadioInput from './Components/FormRadioInput'
import FFormSingleChoiceInput from './Components/FormSingleChoiceInput'
import FFormStringInput from './Components/FormStringInput'
import FFormSwitchInput from './Components/FormSwitchInput'
import FFormTextInput from './Components/FormTextInput'
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

export default {
    install (Vue) {
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

        // Menu specific..)
        Vue.component('f-menu-group', FMenuGroup)
        Vue.component('f-menu-link', FMenuLink)

        // Form specific..)
        Vue.component('f-form-field', FFormField)
        Vue.component('f-form-date-time-input', FFormDateTimeInput)
        Vue.component('f-form-radio-input', FFormRadioInput)
        Vue.component('f-form-single-choice-input', FFormSingleChoiceInput)
        Vue.component('f-form-string-input', FFormStringInput)
        Vue.component('f-form-text-input', FFormTextInput)
        Vue.component('f-form-switch-input', FFormSwitchInput)
    }
}
