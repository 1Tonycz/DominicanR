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

eval("function createDropHandler(dropAreaId, inputId, previewId, multiple = false) {\n    const dropArea = document.getElementById(dropAreaId);\n    const fileInput = document.getElementById(inputId);\n    const preview = document.getElementById(previewId);\n\n    dropArea.addEventListener('click', () => fileInput.click());\n\n    dropArea.addEventListener('dragover', e => {\n        e.preventDefault();\n        dropArea.classList.add('highlight');\n    });\n\n    dropArea.addEventListener('dragleave', () => dropArea.classList.remove('highlight'));\n\n    dropArea.addEventListener('drop', e => {\n        e.preventDefault();\n        dropArea.classList.remove('highlight');\n\n        const files = e.dataTransfer.files;\n        if (!multiple) {\n            preview.innerHTML = ''; // pouze jeden soubor\n            fileInput.files = files;\n        }\n\n        handleFiles(files, preview, multiple);\n    });\n\n    fileInput.addEventListener('change', () => {\n        if (!multiple) preview.innerHTML = '';\n        handleFiles(fileInput.files, preview, multiple);\n    });\n\n    function handleFiles(files, container, allowMultiple) {\n        [...files].forEach((file, i) => {\n            if (!allowMultiple && i > 0) return;\n\n            const reader = new FileReader();\n            reader.readAsDataURL(file);\n            reader.onloadend = () => {\n                const wrapper = document.createElement('div');\n                wrapper.className = 'accom-images__preview';\n\n                wrapper.innerHTML = `\n          <img src=\"${reader.result}\" alt=\"náhled\">\n          <span class=\"accom-images__delete\">×</span>\n        `;\n\n                wrapper.querySelector('.accom-images__delete').addEventListener('click', () => {\n                    wrapper.remove();\n\n                    if (!allowMultiple) {\n                        fileInput.value = '';\n                    }\n                });\n\n                container.appendChild(wrapper);\n            };\n        });\n    }\n}\n\n// Spuštění pro obě oblasti\ndocument.addEventListener('DOMContentLoaded', () => {\n    createDropHandler('main-drop-area', 'mainImageInput', 'main-preview', false); // hlavní obrázek\n    createDropHandler('gallery-drop-area', 'galleryInput', 'gallery-preview', true); // více obrázků\n});\n\n\n//# sourceURL=webpack://dominicanr/./scripts/home.js?");

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