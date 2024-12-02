// Función para enviar un recurso (vehículo o remolque) a mantenimiento
function enviarAMantenimiento(recursoId, tipoRecurso) {
    // Confirmación para evitar clics accidentales
    if (!confirm("¿Estás seguro de que deseas enviar este recurso a mantenimiento?")) {
        return;
    }

    fetch('php/mantenimientoRecursos/logicaMandarMantenimiento.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            recursoId: recursoId,
            tipoRecurso: tipoRecurso // 'vehiculo' o 'remolque'
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
            if (tipoRecurso === 'vehiculo') {
                window.location.href = 'menuCHD.php?section=mantenimiento&mantenimiento=vehiculos&herramienta=mandar&status=success';
            } else if (tipoRecurso === 'remolque') {
                window.location.href = 'menuCHD.php?section=mantenimiento&mantenimiento=remolques&herramienta=mandar&status=success';
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

function registrarMantenimiento(recursoId, tipoRecurso) {
    const modal = document.getElementById('modalMantenimiento');
    modal.style.display = 'block';

    // Establecer el ID y tipo en los campos ocultos del formulario
    document.getElementById('recursoId').value = recursoId;
    document.getElementById('tipoRecurso').value = tipoRecurso;

    // Confirmación para evitar clics accidentales
    if (!confirm("¿Estás seguro que deseas registrar este recurso como mantenido?")) {
        cerrarModal();
        return;
    }
}

// Función para cerrar el modal
function cerrarModal() {
    document.getElementById('modalMantenimiento').style.display = 'none';
}
