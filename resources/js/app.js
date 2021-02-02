/**
 * -----------------------------------------------------------------
 * Fastlane Control Panel main script.
 * -----------------------------------------------------------------
 */

import 'alpinejs'
import { BlockEditor } from './components/block-editor'
import { Form } from './components/form'
import { ItemActionDelete } from './components/item-action-delete'
import { Modal } from './components/modal'
import { Select } from './components/select'
import { Spinner } from './components/spinner'
import { StickyTitleBar } from './components/sticky-title-bar'

window.fl = {
    BlockEditor,
    Form,
    ItemActionDelete,
    Modal,
    Select,
    Spinner,
    StickyTitleBar,
}
