function mostrarRecurso(param) {
    // Crear una nueva URL basada en la ubicación actual
    const url = new URL(window.location.href);
    
    // Añadir o actualizar el parámetro "mantenimiento" con el valor recibido
    url.searchParams.set('herramienta', param);
    
    // Redirigir a la nueva URL
    window.location.href = url.toString();
}

function elegirRecurso(param) {
    // Crear una nueva URL basada en la ubicación actual
    const url = new URL(window.location.href);
    
    // Añadir o actualizar el parámetro "mantenimiento" con el valor recibido
    url.searchParams.set('mantenimiento', param);
    
    // Redirigir a la nueva URL
    window.location.href = url.toString();
}

function removerHerramienta() {
    // Remueve el parámetro `mantenimiento` de la URL para regresar al estado original
    const url = new URL(window.location.href);
    url.searchParams.delete('herramienta');
    window.location.href = url.toString();
}

function removerMantenimiento() {
    // Remueve el parámetro `mantenimiento` de la URL para regresar al estado original
    const url = new URL(window.location.href);
    url.searchParams.delete('mantenimiento');
    window.location.href = url.toString();
}