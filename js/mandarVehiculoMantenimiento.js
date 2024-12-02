// Función para enviar un vehículo a mantenimiento
function enviarAMantenimiento(vehiculoId) {
    // Confirmación para evitar clics accidentales
    if (!confirm("¿Estás seguro de que deseas enviar este vehículo a mantenimiento?")) {
        return;
    }

    fetch('php/mantenimientoRecursos/logicaMandarMantenimiento.php', {
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
            // Redirigir usando JavaScript después de una respuesta exitosa
            window.location.href = 'menuCHD.php?section=mantenimiento&mantenimiento=vehiculos&herramienta=mandar&status=success';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        alert('Ocurrió un error al procesar la solicitud.');
    });
}

function registrarMantenimiento(vehiculoId) {
    // Confirmación para evitar clics accidentales
    if (!confirm("¿Estás seguro que este es el vehículo que se le dio mantenimiento?")) {
        return;
    }

    // Establece el valor del vehículo en el formulario del modal
    document.getElementById('vehiculoId').value = vehiculoId;

    // Muestra el modal
    document.getElementById('modalMantenimiento').style.display = 'block';
}

// Función para cerrar el modal
function cerrarModal() {
    document.getElementById('modalMantenimiento').style.display = 'none';
}
