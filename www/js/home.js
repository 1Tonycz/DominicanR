/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./scripts/home.js":
/*!*************************!*\
  !*** ./scripts/home.js ***!
  \*************************/
/***/ (() => {

eval("document.querySelectorAll('#gallery-preview .accom-images__delete').forEach(deleteBtn => {\n    deleteBtn.addEventListener('click', (e) => {\n        e.stopPropagation();\n        const imageId = deleteBtn.dataset.id;\n        if (!imageId) {\n            console.warn('Chybí data-id u obrázku');\n            return;\n        }\n\n        const wrapper = deleteBtn.closest('.accom-images__preview');\n\n        fetch(`?do=deleteImage&id=${imageId}`, {\n            method: 'POST',\n            headers: {\n                'X-Requested-With': 'XMLHttpRequest'\n            }\n        }).then(response => {\n            if (response.ok) {\n                wrapper.remove();\n            } else {\n                alert('Mazání se nezdařilo.');\n            }\n        }).catch(error => {\n            console.error('Chyba požadavku:', error);\n        });\n    });\n});\n\n\n//# sourceURL=webpack://dominicanr/./scripts/home.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./scripts/home.js"]();
/******/ 	
/******/ })()
;