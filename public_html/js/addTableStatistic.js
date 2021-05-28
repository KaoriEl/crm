(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["/js/addTableStatistic"],{

/***/ "./resources/js/addTableStatistic.js":
/*!*******************************************!*\
  !*** ./resources/js/addTableStatistic.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var takeUrl = window.location.href;
var regex = /show_table/gm;
var str = takeUrl;
var m;
var count;

while ((m = regex.exec(str)) !== null) {
  if (m.index === regex.lastIndex) {
    regex.lastIndex++;
  }

  m.forEach(function (match, groupIndex) {
    count = 1;
  });
}

if (count === 1) {
  var hideElem = document.getElementsByClassName("card card-table")[0];
  hideElem.style.display = "block";
} else {}

/***/ }),

/***/ 2:
/*!*************************************************!*\
  !*** multi ./resources/js/addTableStatistic.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! F:\openserver\OpenServer\domains\ytg-die\resources\js\addTableStatistic.js */"./resources/js/addTableStatistic.js");


/***/ })

},[[2,"/js/manifest"]]]);