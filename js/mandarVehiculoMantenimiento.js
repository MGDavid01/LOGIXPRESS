// Función para enviar un vehículo a mantenimiento
function enviarAMantenimiento(vehiculoId) {
    // Confirmación para evitar clics accidentales
    if (!confirm("¿Estás seguro de que deseas enviar este vehículo a mantenimiento?")) {
        return;
    }

    fetch('php/mantenimientoRecursos/logicaEnviarMantenimiento.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            vehiculoId: vehiculoId
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('El vehículo ha sido enviado a mantenimiento.');

            // Obtener el elemento por ID y asegurarse de que no sea null antes de manipularlo
            const card = document.getElementById(`card-${vehiculoId}`);
            if (card) {
                card.classList.add('mantenimiento');
                // Aquí podrías hacer alguna animación o efecto visual para indicar el cambio
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        alert('Ocurrió un error al procesar la solicitud.');
    });
}