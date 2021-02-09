/**
 * -----------------------------------------------------------------
 * Fastlane Control Panel main script.
 * -----------------------------------------------------------------
 */

import { start as startTurbo } from '@hotwired/turbo';
import './components';
import './bootstrap';
import './elements/turbo-echo-stream-tag';

startTurbo()
