// Función para abrir el modal y cargar los detalles de la entrega
function mostrarModal(entregaId) {
    const modal = document.getElementById('modalDetallesEntrega');
    const detallesContenido = document.getElementById('detallesContenido');
    const entregaIdModal = document.getElementById('entregaIdModal');

    // Restablece el contenido del modal antes de realizar la petición AJAX
    detallesContenido.innerHTML = '<p>Cargando detalles...</p>';

    // Mostrar el modal
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden'; // Evita que la página se desplace

    // Llamada AJAX para obtener los detalles de la entrega
    fetch(`php/pendingDeliveries/detallesEntrega.php?entregaId=${entregaId}`)
        .then(response => response.text())
        .then(data => {
            entregaIdModal.textContent = entregaId;
            detallesContenido.innerHTML = data; // Actualiza el contenido del modal con los datos recibidos
        })
        .catch(error => {
            console.error('Error:', error);
            detallesContenido.innerHTML = '<p>Error al obtener los detalles de la entrega.</p>';
        });
}

// Función para cerrar el modal
function cerrarModal() {
    const modal = document.getElementById('modalDetallesEntrega');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto'; // Permite nuevamente que la página se desplace
}

// Cuando el usuario hace clic en cualquier parte fuera del modal, también lo cierra
window.onclick = function (event) {
    const modal = document.getElementById('modalDetallesEntrega');
    if (event.target === modal) {
        cerrarModal();
    }
}
