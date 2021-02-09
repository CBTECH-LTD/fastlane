import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: process.env.MIX_PUSHER_USE_SSL === "true",
    disableStats: true,
    wsHost: process.env.MIX_PUSHER_HOST,
    wsPort: process.env.MIX_PUSHER_PORT || null,
});

export default echo;
