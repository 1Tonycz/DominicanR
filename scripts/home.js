/**
 * Init drag & drop areas, preview generation and image deletion.
 */
document.addEventListener('DOMContentLoaded', () => {
    const mainInput = document.getElementById('mainImageInput');
    const mainDrop = document.getElementById('main-drop-area');
    const mainPreview = document.getElementById('main-preview');

    const galleryInput = document.getElementById('galleryInput');
    const galleryDrop = document.getElementById('gallery-drop-area');
    const galleryPreview = document.getElementById('gallery-preview');

    if (mainInput && mainDrop && mainPreview) {
        initSingleUpload(mainInput, mainDrop, mainPreview);
    }

    if (galleryInput && galleryDrop && galleryPreview) {
        initMultiUpload(galleryInput, galleryDrop, galleryPreview);
    }

    initExistingDeletes();
});

/**
 * Single file upload (main image)
 */
function initSingleUpload(input, dropArea, previewList) {
    let current = null;

    const add = (file) => {
        current = file;
        previewList.innerHTML = '';
        createPreview(file, previewList, () => {
            current = null;
        });
    };

    setupDropEvents(dropArea, (files) => {
        if (files.length) {
            add(files[0]);
        }
    });

    dropArea.addEventListener('click', () => input.click());
    input.addEventListener('change', () => {
        if (input.files.length) {
            add(input.files[0]);
            input.value = '';
        }
    });

    input.closest('form').addEventListener('submit', () => {
        const dt = new DataTransfer();
        if (current) dt.items.add(current);
        input.files = dt.files;
    });
}

/**
 * Multiple files upload (gallery)
 */
function initMultiUpload(input, dropArea, previewList) {
    let files = [];

    const addFiles = (list) => {
        Array.from(list).forEach((file) => {
            files.push(file);
            createPreview(file, previewList, (wrapper) => {
                const idx = Array.from(previewList.children).indexOf(wrapper);
                files.splice(idx, 1);
            });
        });
    };

    setupDropEvents(dropArea, addFiles);

    dropArea.addEventListener('click', () => input.click());
    input.addEventListener('change', () => {
        if (input.files.length) {
            addFiles(input.files);
            input.value = '';
        }
    });

    input.closest('form').addEventListener('submit', () => {
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        input.files = dt.files;
    });
}

/**
 * Basic drag & drop listeners
 */
function setupDropEvents(dropArea, onFiles) {
    ['dragenter', 'dragover'].forEach(evt => {
        dropArea.addEventListener(evt, (e) => {
            e.preventDefault();
            dropArea.classList.add('highlight');
        });
    });
    ['dragleave', 'drop'].forEach(evt => {
        dropArea.addEventListener(evt, (e) => {
            e.preventDefault();
            dropArea.classList.remove('highlight');
        });
    });
    dropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        onFiles(Array.from(e.dataTransfer.files));
    });
}

/**
 * Creates preview item with delete button
 */
function createPreview(file, previewList, removeCb) {
    const wrapper = document.createElement('div');
    wrapper.className = 'accom-images__preview';

    const img = document.createElement('img');
    wrapper.appendChild(img);

    const del = document.createElement('span');
    del.className = 'accom-images__delete';
    del.textContent = '×';
    wrapper.appendChild(del);

    del.addEventListener('click', (e) => {
        e.stopPropagation();
        if (removeCb) removeCb(wrapper);
        wrapper.remove();
    });

    const reader = new FileReader();
    reader.onload = (e) => {
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);

    previewList.appendChild(wrapper);
}

/**
 * Handle delete of already uploaded images (edit form)
 */
function initExistingDeletes() {
    document.querySelectorAll('#gallery-preview .accom-images__delete').forEach(deleteBtn => {
        deleteBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const imageId = deleteBtn.dataset.id;
            if (!imageId) {
                console.warn('Chybí data-id u obrázku');
                return;
            }

            const wrapper = deleteBtn.closest('.accom-images__preview');

            fetch(`?do=deleteImage&id=${imageId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                if (response.ok) {
                    wrapper.remove();
                } else {
                    alert('Mazání se nezdařilo.');
                }
            }).catch(error => {
                console.error('Chyba požadavku:', error);
            });
        });
    });
}
