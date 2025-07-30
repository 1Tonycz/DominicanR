/**
 * Init drag & drop areas, preview generation and image deletion.
 */
document.addEventListener('DOMContentLoaded', () => {
    const mainInput = document.getElementById('mainImageInput');
    const mainDrop  = document.getElementById('main-drop-area');
    const mainPrev  = document.getElementById('main-preview');

    const galleryInput = document.getElementById('galleryInput');
    const galleryDrop  = document.getElementById('gallery-drop-area');
    const galleryPrev  = document.getElementById('gallery-preview');

    if (mainInput && mainDrop && mainPrev) {
        setupSingleUploader(mainInput, mainDrop, mainPrev);
    }

    if (galleryInput && galleryDrop && galleryPrev) {
        setupGalleryUploader(galleryInput, galleryDrop, galleryPrev);
    }

    initExistingDeletes();
});

/**
 * Single file upload (main image)
 */
function setupSingleUploader(input, dropArea, previewList) {
    let file = null;

    const updateInput = () => {
        const dt = new DataTransfer();
        if (file) dt.items.add(file);
        input.files = dt.files;
    };

    const handle = (list) => {
        file = list[0];
        previewList.innerHTML = '';
        const {wrapper, del} = createPreview(file);
        del.addEventListener('click', (e) => {
            e.stopPropagation();
            file = null;
            wrapper.remove();
            updateInput();
        });
        previewList.appendChild(wrapper);
        updateInput();
    };

    input.addEventListener('change', () => {
        if (input.files.length) {
            handle(input.files);
            input.value = '';
        }
    });

    setupDropEvents(dropArea, handle);
    input.closest('form').addEventListener('submit', updateInput);
}

/**
 * Multiple files upload (gallery)
 */
function setupGalleryUploader(input, dropArea, previewList) {
    const files = [];

    const updateInput = () => {
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        input.files = dt.files;
    };

    const addFiles = (list) => {
        Array.from(list).forEach((file) => {
            files.push(file);
            const {wrapper, del} = createPreview(file);
            del.addEventListener('click', (e) => {
                e.stopPropagation();
                const idx = files.indexOf(file);
                if (idx > -1) files.splice(idx, 1);
                wrapper.remove();
                updateInput();
            });
            previewList.appendChild(wrapper);
        });
        updateInput();
    };

    input.addEventListener('change', () => {
        if (input.files.length) {
            addFiles(input.files);
            input.value = '';
        }
    });

    setupDropEvents(dropArea, addFiles);
    input.closest('form').addEventListener('submit', updateInput);
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
function createPreview(file) {
    const wrapper = document.createElement('div');
    wrapper.className = 'accom-images__preview';

    const img = document.createElement('img');
    wrapper.appendChild(img);

    const del = document.createElement('span');
    del.className = 'accom-images__delete';
    del.textContent = '×';
    wrapper.appendChild(del);

    const reader = new FileReader();
    reader.onload = (e) => {
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);

    return {wrapper, del};
}

/**
 * Handle delete of already uploaded images (edit form)
 */
function initExistingDeletes() {
    document.querySelectorAll('#gallery-preview .accom-images__delete[data-id]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const imageId = btn.dataset.id;
            if (!imageId) return;

            const wrapper = btn.closest('.accom-images__preview');

            fetch(`?do=deleteImage&id=${imageId}`, {
                method: 'POST',
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            }).then(res => {
                if (res.ok) {
                    wrapper.remove();
                } else {
                    alert('Mazání se nezdařilo.');
                }
            }).catch(err => {
                console.error('Chyba požadavku:', err);
            });
        });
    });
}
