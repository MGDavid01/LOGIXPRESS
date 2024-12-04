// detailsDeliveryModal.js (agregar función)
function mostrarModalAsignacion(entregaId) {
    // Cargar el ID de la entrega en el modal
    document.getElementById("entregaIdAsignacion").textContent = entregaId;

    document.getElementById('entregaHidden').value = entregaId;
    // Aquí se hace la solicitud AJAX para llenar los datos del formulario (empleados, vehículos, etc.)
    obtenerRecursos(entregaId);

    // Mostrar el modal
    const modalAsignacion = document.getElementById("modalAsignacionRecursos");
    modalAsignacion.style.display = "block";
}

// Función para cerrar el modal de asignación
function cerrarModalAsignacion() {
    const modalAsignacion = document.getElementById("modalAsignacionRecursos");
    modalAsignacion.style.display = "none";
}

async function obtenerRecursos(entregaId) {
    try {
        // Solicitar empleados disponibles
        let responseEmpleados = await fetch('php/asignDelivery/obtenerEmpleados.php?entregaId=' + entregaId);
        let dataEmpleados = await responseEmpleados.json();
        
        let empleadosSelect = document.getElementById('empleado');
        empleadosSelect.innerHTML = '<option value="">Seleccione un empleado</option>';
        dataEmpleados.empleados.forEach(empleado => {
            empleadosSelect.innerHTML += `<option value="${empleado.id}">${empleado.nombre}</option>`;
        });

        // Solicitar categorías de vehículos
        let responseCategorias = await fetch('php/asignDelivery/obtenerCategoriasVehiculos.php?entregaId=' + entregaId);
        let dataCategorias = await responseCategorias.json();

        let categoriaSelect = document.getElementById('categoriaVehiculo');
        categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';
        dataCategorias.categorias.forEach(categoria => {
            categoriaSelect.innerHTML += `<option value="${categoria.id}">${categoria.nombre}</option>`;
        });

        // Agregar un listener al cambio del selector de categoría de vehículo para cargar los vehículos correspondientes
        document.getElementById('categoriaVehiculo').addEventListener('change', async function() {
            const categoriaId = this.value;
            if (categoriaId) {
                // Solicitar vehículos según la categoría seleccionada
                let responseVehiculos = await fetch('php/asignDelivery/obtenerVehiculosPorCategoria.php?categoriaId=' + categoriaId);
                let dataVehiculos = await responseVehiculos.json();

                let vehiculosSelect = document.getElementById('vehiculo');
                vehiculosSelect.innerHTML = '<option value="">Seleccione un vehículo</option>';
                dataVehiculos.vehiculos.forEach(vehiculo => {
                    vehiculosSelect.innerHTML += `<option value="${vehiculo.id}">${vehiculo.numSerie}</option>`;
                });

                // Mostrar el campo de remolque si la categoría seleccionada es 'Tractocamión Articulado'
                if (categoriaId === "CAMAP") {
                    document.getElementById('remolqueField').style.display = 'block';

                    // Obtener remolques disponibles
                    let responseRemolques = await fetch('php/asignDelivery/obtenerRemolques.php?entregaId=' + entregaId);
                    let dataRemolques = await responseRemolques.json();

                    let remolquesSelect = document.getElementById('remolque');
                    remolquesSelect.innerHTML = '<option value="">Seleccione un remolque</option>';
                    dataRemolques.remolques.forEach(remolque => {
                        remolquesSelect.innerHTML += `<option value="${remolque.num}">${remolque.numSerie}</option>`;
                    });

                } else {
                    document.getElementById('remolqueField').style.display = 'none';
                    document.getElementById('remolque').innerHTML = '<option value="">Seleccione un remolque</option>';
                }
            } else {
                // Limpiar el selector de vehículos si no hay categoría seleccionada
                document.getElementById('vehiculo').innerHTML = '<option value="">Seleccione un vehículo</option>';
                document.getElementById('remolqueField').style.display = 'none';
                document.getElementById('remolque').innerHTML = '<option value="">Seleccione un remolque</option>';
            }
        });

    } catch (error) {
        console.error('Error al obtener recursos:', error);
    }
}