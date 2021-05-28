/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.Popper = require('popper.js').default;
window.$ = window.jQuery = require('jquery');
window.Dropzone = require('dropzone');

// jQuery plugins
require('jquery-datetimepicker');
require('datatables.net');
require('datatables.net-bs4');

// Bootstrap
require('bootstrap');

const headers = {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    'Authorization': 'Bearer ' + $('meta[name="api-token"]').attr('content'),
};

$.ajaxSetup({ headers });

jQuery.datetimepicker.setLocale('ru');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// window.axios = require('axios');

// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: $('meta[name="pusher-app-key"]').attr('content'),
    cluster: $('meta[name="pusher-app-cluster"]').attr('content'),
    encrypted: true
});
