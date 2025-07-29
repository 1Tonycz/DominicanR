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
