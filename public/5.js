(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[5],{

/***/ "./resources/js/bootstrap.js":
/*!***********************************!*\
  !*** ./resources/js/bootstrap.js ***!
  \***********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _echo__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./echo */ "./resources/js/echo.js");
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(axios__WEBPACK_IMPORTED_MODULE_1__);


/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = axios__WEBPACK_IMPORTED_MODULE_1___default.a;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.Echo = _echo__WEBPACK_IMPORTED_MODULE_0__["default"];
document.addEventListener('turbo:before-fetch-request', function (e) {
  e.detail.fetchOptions.headers['X-Socket-ID'] = _echo__WEBPACK_IMPORTED_MODULE_0__["default"].socketId();
});

/***/ }),

/***/ "./resources/js/echo.js":
/*!******************************!*\
  !*** ./resources/js/echo.js ***!
  \******************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(process) {/* harmony import */ var laravel_echo__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! laravel-echo */ "./node_modules/laravel-echo/dist/echo.js");
/* harmony import */ var pusher_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! pusher-js */ "./node_modules/pusher-js/dist/web/pusher.js");
/* harmony import */ var pusher_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(pusher_js__WEBPACK_IMPORTED_MODULE_1__);


window.Pusher = pusher_js__WEBPACK_IMPORTED_MODULE_1___default.a;
var echo = new laravel_echo__WEBPACK_IMPORTED_MODULE_0__["default"]({
  broadcaster: 'pusher',
  key: process.env.MIX_PUSHER_APP_KEY,
  cluster: process.env.MIX_PUSHER_APP_CLUSTER,
  forceTLS: process.env.MIX_PUSHER_USE_SSL === "true",
  disableStats: true,
  wsHost: process.env.MIX_PUSHER_HOST,
  wsPort: process.env.MIX_PUSHER_PORT || null
});
/* harmony default export */ __webpack_exports__["default"] = (echo);
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../node_modules/process/browser.js */ "./node_modules/process/browser.js")))

/***/ })

}]);