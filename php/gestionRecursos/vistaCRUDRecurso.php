<?php

// Lógica principal basada en el recurso seleccionado
echo '<div class="content-card">';
echo '<div class="crud">';
switch ($recurso) {
    case 'empleado':
        $empleados = obtenerEmpleados();
        mostrarCrud($empleados, 'empleado');
        break;
    case 'remolque':
        $remolques = obtenerRemolques();
        mostrarCrud($remolques, 'remolque');
        break;
    case 'vehiculo':
        $vehiculos = obtenerVehiculos();
        mostrarCrud($vehiculos, 'vehiculo');
        break;
    default:
        vistaElegirRecurso();
        break;
}
echo '</div>'; // Cierra div "crud"
?>

<div class="btn-back">
    <button onclick="window.history.back()">Go Back</button>
</div>
</div>

<?php

// Funciones para obtener los recursos de la base de datos
function obtenerEmpleados() {
    global $db;
    $stmt = $db->prepare("SELECT * FROM empleado");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerRemolques() {
    global $db;
    $stmt = $db->prepare("SELECT * FROM remolque");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerVehiculos() {
    global $db;
    $stmt = $db->prepare("SELECT * FROM vehiculo");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Mostrar los datos en una tabla CRUD
function mostrarCrud($datos, $tipoRecurso) {
    if (!empty($datos)) {
        echo '<table class="crud-table">';
        echo '<thead><tr>';
        
        // Mostrar los encabezados de las columnas según los datos proporcionados
        foreach (array_keys($datos[0]) as $campo) {
            echo '<th>' . htmlspecialchars($campo) . '</th>';
        }
        echo '<th>Acciones</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        // Mostrar cada fila de datos
        foreach ($datos as $fila) {
            echo '<tr>';
            foreach ($fila as $valor) {
                echo '<td>' . htmlspecialchars($valor) . '</td>';
            }
            echo '<td>';
            echo '<button onclick="editarRecurso(\'' . $tipoRecurso . '\', ' . $fila['num'] . ')">Editar</button>';
            echo '<button onclick="eliminarRecurso(\'' . $tipoRecurso . '\', ' . $fila['num'] . ')">Eliminar</button>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>No hay registros disponibles para este recurso.</p>';
    }
}

?>

<script>
function editarRecurso(tipo, id) {
    // Redirigir a la página de edición del recurso
    window.location.href = tipo + '/editar.php?id=' + id;
}

function eliminarRecurso(tipo, id) {
    if (confirm('Está seguro de que desea eliminar este recurso?')) {
        // Redirigir a la página de eliminación del recurso
        window.location.href = tipo + '/eliminar.php?id=' + id;
    }
}
</script>
