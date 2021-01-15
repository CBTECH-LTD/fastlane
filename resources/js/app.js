/**
 * -----------------------------------------------------------------
 * Fastlane Control Panel main script.
 * -----------------------------------------------------------------
 */

import 'alpinejs'
import { BlockEditor } from './components/block-editor'
import { ItemActionDelete } from './components/item-action-delete'
import { Modal } from './components/modal'
import { Select } from './components/select'
import { Spinner } from './components/spinner'
import { StickyTitleBar } from './components/sticky-title-bar'

window.fl = {
    BlockEditor,
    ItemActionDelete,
    Modal,
    Select,
    Spinner,
    StickyTitleBar,
}
