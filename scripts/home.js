function createDropHandler(dropAreaId, inputId, previewId, multiple = false) {
    const dropArea = document.getElementById(dropAreaId);
    const fileInput = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    dropArea.addEventListener('click', () => fileInput.click());

    dropArea.addEventListener('dragover', e => {
        e.preventDefault();
        dropArea.classList.add('highlight');
    });

    dropArea.addEventListener('dragleave', () => dropArea.classList.remove('highlight'));

    dropArea.addEventListener('drop', e => {
        e.preventDefault();
        dropArea.classList.remove('highlight');

        const files = e.dataTransfer.files;
        if (!multiple) {
            preview.innerHTML = ''; // pouze jeden soubor
            fileInput.files = files;
        }

        handleFiles(files, preview, multiple);
    });

    fileInput.addEventListener('change', () => {
        if (!multiple) preview.innerHTML = '';
        handleFiles(fileInput.files, preview, multiple);
    });

    function handleFiles(files, container, allowMultiple) {
        [...files].forEach((file, i) => {
            if (!allowMultiple && i > 0) return;

            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = () => {
                const wrapper = document.createElement('div');
                wrapper.className = 'accom-images__preview';

                wrapper.innerHTML = `
          <img src="${reader.result}" alt="náhled">
          <span class="accom-images__delete">×</span>
        `;

                wrapper.querySelector('.accom-images__delete').addEventListener('click', () => {
                    wrapper.remove();

                    if (!allowMultiple) {
                        fileInput.value = '';
                    }
                });

                container.appendChild(wrapper);
            };
        });
    }
}

// Spuštění pro obě oblasti
document.addEventListener('DOMContentLoaded', () => {
    createDropHandler('main-drop-area', 'mainImageInput', 'main-preview', false); // hlavní obrázek
    createDropHandler('gallery-drop-area', 'galleryInput', 'gallery-preview', true); // více obrázků
});
