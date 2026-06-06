document.addEventListener('DOMContentLoaded', () => {
    // Création dynamique de la modal d'aperçu d'image
    const modal = document.createElement('div');
    modal.className = 'image-modal';
    modal.innerHTML = `
        <div class="image-modal-close">&times;</div>
        <img class="image-modal-content" src="" alt="Aperçu de l'image">
        <div class="image-modal-caption"></div>
    `;
    document.body.appendChild(modal);

    const modalImg = modal.querySelector('.image-modal-content');
    const modalCaption = modal.querySelector('.image-modal-caption');
    const closeBtn = modal.querySelector('.image-modal-close');

    // Cibler toutes les images du rapport
    const images = document.querySelectorAll('.image-container img, .mobile-image-wrapper img, .mobile-map-image-wrapper img');

    images.forEach(img => {
        img.style.cursor = 'zoom-in';
        img.addEventListener('click', () => {
            modal.classList.add('active');
            modalImg.src = img.src;
            modalCaption.textContent = img.alt || 'Aperçu Chronos';
        });
    });

    // Fermeture de la modal
    const closeModal = () => {
        modal.classList.remove('active');
    };

    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal || e.target === modalImg) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    console.log("Projet Chronos - Rapport interactif chargé avec succès.");
});
