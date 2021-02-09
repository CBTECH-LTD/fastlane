import Echo  from './echo';
import Axios from 'axios';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = Axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.Echo = Echo;

document.addEventListener('turbo:before-fetch-request', (e) => {
    e.detail.fetchOptions.headers['X-Socket-ID'] = Echo.socketId();
});
