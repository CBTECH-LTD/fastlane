/**
 * -----------------------------------------------------------------
 * Fastlane Control Panel main script.
 * -----------------------------------------------------------------
 */

import 'alpinejs'
import { Modal } from './components/modal'
import { ItemActionDelete } from './components/item-action-delete'
import { StickyTitleBar } from './components/sticky-title-bar'
import { Spinner } from './components/spinner'

window.fl = {
    ItemActionDelete,
    Modal,
    Spinner,
    StickyTitleBar,
}
