function applyFilters() {
    const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
    const brandFilter = document.getElementById('brandFilter').value.toLowerCase();
    const modelFilter = document.getElementById('modelFilter').value.toLowerCase();
    const searchInput = document.getElementById('searchInput').value.toLowerCase();

    const cards = document.querySelectorAll('#vehicleCards .card');

    // Filtro de elementos
    cards.forEach(card => {
        const category = card.getAttribute('data-category').toLowerCase();
        const brand = card.getAttribute('data-brand').toLowerCase();
        const model = card.getAttribute('data-model').toLowerCase();
        const serial = card.getAttribute('data-serial').toLowerCase();

        let showCard = true;

        if (categoryFilter && category !== categoryFilter) {
            showCard = false;
        }

        if (brandFilter && brand !== brandFilter) {
            showCard = false;
        }

        if (modelFilter && model !== modelFilter) {
            showCard = false;
        }

        if (searchInput && !serial.includes(searchInput)) {
            showCard = false;
        }

        if (showCard) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });

    // Asegurarse de que todos los elementos filtrados se re-rendericen adecuadamente
    reapplyStylesAndAnimations();
}

function reapplyStylesAndAnimations() {
    const cards = document.querySelectorAll('#vehicleCards .card');

    cards.forEach(card => {
        // Forzar el re-renderizado mediante la actualización de clases o estilos
        card.classList.remove('active');
        void card.offsetWidth; // Esto hace que el navegador "reinicie" la animación
        card.classList.add('active');

        // Aplicar cualquier estilo adicional si es necesario
        card.style.transition = 'transform 0.3s ease-in-out, opacity 0.3s ease';
        card.style.opacity = '1';
        card.style.transform = 'scale(1)';
        card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-1rem)';
    });

    card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0)'; // Regresa a la posición original
    });
    });
}