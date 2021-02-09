(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[3],{

/***/ "./resources/js/components.js":
/*!************************************!*\
  !*** ./resources/js/components.js ***!
  \************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var alpinejs__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! alpinejs */ "./node_modules/alpinejs/dist/alpine.js");
/* harmony import */ var alpinejs__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(alpinejs__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/block-editor */ "./resources/js/components/block-editor.js");
/* harmony import */ var _components_form__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/form */ "./resources/js/components/form.js");
/* harmony import */ var _components_item_action_delete__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/item-action-delete */ "./resources/js/components/item-action-delete.js");
/* harmony import */ var _components_modal__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/modal */ "./resources/js/components/modal.js");
/* harmony import */ var _components_select__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/select */ "./resources/js/components/select.js");
/* harmony import */ var _components_spinner__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components/spinner */ "./resources/js/components/spinner.js");
/* harmony import */ var _components_sticky_title_bar__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./components/sticky-title-bar */ "./resources/js/components/sticky-title-bar.js");
// import 'alpine-turbo-drive-adapter'








window.fl = {
  BlockEditor: _components_block_editor__WEBPACK_IMPORTED_MODULE_1__["BlockEditor"],
  Form: _components_form__WEBPACK_IMPORTED_MODULE_2__["Form"],
  ItemActionDelete: _components_item_action_delete__WEBPACK_IMPORTED_MODULE_3__["ItemActionDelete"],
  Modal: _components_modal__WEBPACK_IMPORTED_MODULE_4__["Modal"],
  Select: _components_select__WEBPACK_IMPORTED_MODULE_5__["Select"],
  Spinner: _components_spinner__WEBPACK_IMPORTED_MODULE_6__["Spinner"],
  StickyTitleBar: _components_sticky_title_bar__WEBPACK_IMPORTED_MODULE_7__["StickyTitleBar"]
};

/***/ }),

/***/ "./resources/js/components/block-editor.js":
/*!*************************************************!*\
  !*** ./resources/js/components/block-editor.js ***!
  \*************************************************/
/*! exports provided: BlockEditor */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "BlockEditor", function() { return BlockEditor; });
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);


function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

function BlockEditor(options) {
  return {
    newBlockPosition: -1,

    get shouldShowAvailableBlocks() {
      return this.newBlockPosition > -1;
    },

    showAvailableBlocks: function showAvailableBlocks(position) {
      this.newBlockPosition = position;
      document.body.style.overflow = 'hidden';
    },
    hideAvailableBlocks: function hideAvailableBlocks() {
      this.newBlockPosition = -1;
      document.body.style.overflow = 'auto';
    },
    selectNewBlock: function selectNewBlock(key) {
      var _this = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _context.next = 2;
                return _this.$wire.addBlock(key, _this.newBlockPosition);

              case 2:
              case "end":
                return _context.stop();
            }
          }
        }, _callee);
      }))();
    }
  };
}

/***/ }),

/***/ "./resources/js/components/form.js":
/*!*****************************************!*\
  !*** ./resources/js/components/form.js ***!
  \*****************************************/
/*! exports provided: Form */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Form", function() { return Form; });
function Form(options) {
  return {
    submit: function submit() {
      var _this = this;

      this.$el.focus();
      setTimeout(function () {
        _this.$wire.submit();
      }, 200);
    }
  };
}

/***/ }),

/***/ "./resources/js/components/item-action-delete.js":
/*!*******************************************************!*\
  !*** ./resources/js/components/item-action-delete.js ***!
  \*******************************************************/
/*! exports provided: ItemActionDelete */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ItemActionDelete", function() { return ItemActionDelete; });
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);


function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

function ItemActionDelete() {
  return {
    waitingConfirmation: false,
    attemptingDelete: false,
    classes: 'bg-transparent',
    init: function init() {
      var _this = this;

      console.log(this.$wire);
      this.$watch('waitingConfirmation', function (val) {
        _this.classes = !!val ? 'bg-yellow-300' : 'bg-transparent';
      });
    },
    showConfirmation: function showConfirmation() {
      this.waitingConfirmation = true;
    },
    cancel: function cancel() {
      this.waitingConfirmation = false;
    },
    confirm: function confirm() {
      var _this2 = this;

      return _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                if (!_this2.attemptingDelete) {
                  _context.next = 2;
                  break;
                }

                return _context.abrupt("return");

              case 2:
                _this2.attemptingDelete = true;
                _context.next = 5;
                return _this2.$wire.confirmAction();

              case 5:
                _this2.attemptingDelete = false;
                _this2.waitingConfirmation = false;

              case 7:
              case "end":
                return _context.stop();
            }
          }
        }, _callee);
      }))();
    }
  };
}

/***/ }),

/***/ "./resources/js/components/modal.js":
/*!******************************************!*\
  !*** ./resources/js/components/modal.js ***!
  \******************************************/
/*! exports provided: Modal */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Modal", function() { return Modal; });
function Modal(show) {
  return {
    show: show,
    classes: 'opacity-0',
    init: function init() {
      var _this = this;

      this.$watch('show', function (isOpen) {
        if (isOpen) {
          _this.classes = 'opacity-25';
          return;
        }

        _this.classes = 'opacity-0';
      });
    },
    close: function close() {
      this.show = false;
    }
  };
}

/***/ }),

/***/ "./resources/js/components/select.js":
/*!*******************************************!*\
  !*** ./resources/js/components/select.js ***!
  \*******************************************/
/*! exports provided: Select */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Select", function() { return Select; });
/* harmony import */ var slim_select__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! slim-select */ "./node_modules/slim-select/dist/slimselect.min.js");
/* harmony import */ var slim_select__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(slim_select__WEBPACK_IMPORTED_MODULE_0__);
 // import 'slim-select/dist/slimselect.min.css'

function Select(options) {
  return {
    instance: null,
    attribute: options.attribute,
    taggable: options.taggable,
    multiple: options.multiple,
    init: function init() {
      var _this = this;

      this.instance = new slim_select__WEBPACK_IMPORTED_MODULE_0___default.a({
        select: this.$el,
        closeOnSelect: this.multiple === false,
        addable: options.taggable ? function (value) {
          if (value.trim() === '') {
            return false;
          }

          return value;
        } : false,
        onChange: function onChange(data) {
          var value = _this.multiple ? data.map(function (it) {
            return it.value;
          }) : data.value;

          _this.$wire.set('value', value);
        }
      });
    }
  };
}

/***/ }),

/***/ "./resources/js/components/spinner.js":
/*!********************************************!*\
  !*** ./resources/js/components/spinner.js ***!
  \********************************************/
/*! exports provided: Spinner */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Spinner", function() { return Spinner; });
function Spinner() {
  return {
    active: false,
    setStatus: function setStatus(status) {
      this.active = status;
    }
  };
}

/***/ }),

/***/ "./resources/js/components/sticky-title-bar.js":
/*!*****************************************************!*\
  !*** ./resources/js/components/sticky-title-bar.js ***!
  \*****************************************************/
/*! exports provided: StickyTitleBar */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "StickyTitleBar", function() { return StickyTitleBar; });
function StickyTitleBar() {
  return {
    init: function init() {
      var _this = this;

      var observer = new IntersectionObserver(function (entries) {
        _this.stickyBarClass = !entries[0].isIntersecting ? 'is-floating' : '';
      }, {
        rootMargin: '-20px 0px',
        threshold: 1
      });
      observer.observe(this.$el);
    }
  };
}

/***/ })

}]);