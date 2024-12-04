<?php
function vistaElegirRecurso() {
    ?>
    <div class="content-card">
        <!-- Tarjeta para Registrar Recursos -->
        <div class="card">
            <div class="image-container">
                <img src="imagenes/recursoEmpleado.png" alt="Employee Management">
            </div>
            <h3>Employee</h3>
            <p>Add, Edit or Delete Employees.</p>
            <button onclick="agregarRecurso('empleado')">Employee Management</button>
        </div>

        <!-- Tarjeta para Editar Recursos -->
        <div class="card">
            <div class="image-container">
                <img src="imagenes/recursoRemolque.png" alt="Trailer Management">
            </div>
            <h3>Trailer</h3>
            <p>Add, Edit or Delete Trailer.</p>
            <button onclick="agregarRecurso('remolque')">Trailer Management</button>
        </div>

        <!-- Tarjeta para Eliminar Recursos -->
        <div class="card">
            <div class="image-container">
                <img src="imagenes/recursoVehiculo.png" alt="Eliminar Recursos">
            </div>
            <h3>Vehicle</h3>
            <p>Add, Edit or Delete Vehicle.</p>
            <button onclick="agregarRecurso('vehiculo')">Vehicle Management</button>
        </div>
    </div>
    <?php
}
$recurso = isset($_GET['recurso']) ? $_GET['recurso'] : '';
switch ($recurso) {
    case 'empleado':
        include_once('vistaCRUDRecurso.php');
        break;
    case 'remolque':
        include_once('vistaCRUDRecurso.php');
        break;
    case 'vehiculo':
        include_once('vistaCRUDRecurso.php');
        break;
    default:
        vistaElegirRecurso();
        break;
}
?>