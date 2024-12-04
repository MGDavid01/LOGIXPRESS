<?php

// Verificar la conexión a la base de datos
if (!isset($db)) {
    try {
        $db = new mysqli('localhost', 'root', '', 'logixpress'); // Ajusta los parámetros de conexión según corresponda
        if ($db->connect_error) {
            die('Error de conexión a la base de datos: ' . $db->connect_error);
        }
    } catch (Exception $e) {
        die('Error al conectar con la base de datos: ' . $e->getMessage());
    }
}

// Obtener el recurso desde la URL
$recurso = isset($_GET['recurso']) ? $_GET['recurso'] : null;

// Lógica principal basada en el recurso seleccionado
try {
    if (!$recurso) {
        throw new Exception('El recurso no está definido. Por favor, seleccione un recurso válido.');
    }

    echo '<div class="btn-back">';
    echo '<button onclick="window.history.back()">Go Back</button>';
    echo '</div>';

    echo '<div class="content-card">';
    echo '<div class="crud">';
    switch ($recurso) {
        case 'empleado':
            $empleados = obtenerEmpleados();
            mostrarTarjetas($empleados, 'empleado');
            break;
        case 'remolque':
            $remolques = obtenerRemolques();
            mostrarTarjetas($remolques, 'remolque');
            break;
        case 'vehiculo':
            $vehiculos = obtenerVehiculos();
            mostrarTarjetas($vehiculos, 'vehiculo');
            break;
        default:
            vistaElegirRecurso();
            break;
    }
    echo '</div>'; // Cierra div "crud"
    echo '</div>'; // Cierra div "content-card"
} catch (Exception $e) {
    echo '<p>Error: ' . $e->getMessage() . '</p>';
}
?>

<?php

// Funciones para obtener los recursos de la base de datos
function obtenerEmpleados() {
    global $db;
    try {
        $stmt = $db->prepare("SELECT e.num, e.nombre, e.primerApe, e.segundoApe, e.telefono, e.email, p.descripcion AS puesto, ee.descripcion AS estadoEmpleado FROM empleado e JOIN estado_emple ee ON e.estadoEmpleado = ee.codigo JOIN puesto p ON e.puesto = p.codigo");
        $stmt->execute();
        $result = $stmt->get_result();
        $empleados = [];
        while ($row = $result->fetch_assoc()) {
            $empleados[] = $row;
        }
        return $empleados;
    } catch (Exception $e) {
        echo '<p>Error al obtener empleados: ' . $e->getMessage() . '</p>';
        return [];
    }
}

function obtenerRemolques() {
    global $db;
    try {
        $stmt = $db->prepare("SELECT r.num, r.numSerie, r.capacidadCarga, tc.descripcion AS tipoCarga, d.descripcion AS disponibilidad FROM remolque r JOIN tipo_carga tc ON r.tipoCarga = tc.codigo JOIN disponibilidad d ON r.disponibilidad = d.codigo WHERE r.num != 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $remolques = [];
        while ($row = $result->fetch_assoc()) {
            $remolques[] = $row;
        }
        return $remolques;
    } catch (Exception $e) {
        echo '<p>Error al obtener remolques: ' . $e->getMessage() . '</p>';
        return [];
    }
}

function obtenerVehiculos() {
    global $db;
    try {
        $stmt = $db->prepare("SELECT v.num, v.numSerie, v.marca, v.modelo, v.categoriaVehiculo, cv.tipoCarga AS tipoCarga, ed.descripcion AS disponibilidad FROM vehiculo v JOIN cat_vehi cv ON v.categoriaVehiculo = cv.codigo JOIN disponibilidad ed ON v.disponibilidad = ed.codigo");
        $stmt->execute();
        $result = $stmt->get_result();
        $vehiculos = [];
        while ($row = $result->fetch_assoc()) {
            $vehiculos[] = $row;
        }
        return $vehiculos;
    } catch (Exception $e) {
        echo '<p>Error al obtener vehículos: ' . $e->getMessage() . '</p>';
        return [];
    }
}

// Mostrar los datos en una vista de tarjetas
function mostrarTarjetas($datos, $tipoRecurso) {
    if (!empty($datos)) {
        echo '<div class="tarjetas-container">';
        foreach ($datos as $fila) {
            echo '<div class="tarjeta">';
            echo '<div class="tarjeta-contenido">';
            foreach ($fila as $campo => $valor) {
                $campoPersonalizado = obtenerCampoPersonalizado($campo);
                echo '<p><strong>' . htmlspecialchars($campoPersonalizado) . ':</strong> ' . htmlspecialchars($valor) . '</p>';
            }
            echo '</div>'; // Cierra div "tarjeta-contenido"
            echo '<div class="tarjeta-acciones">';
            echo '<button onclick="editarRecurso(\'' . $tipoRecurso . '\', ' . htmlspecialchars($fila['num']) . ')">Editar</button>';
            echo '<button onclick="eliminarRecurso(\'' . $tipoRecurso . '\', ' . htmlspecialchars($fila['num']) . ')">Eliminar</button>';
            echo '</div>'; // Cierra div "tarjeta-acciones"
            echo '</div>'; // Cierra div "tarjeta"
        }
        echo '</div>'; // Cierra div "tarjetas-container"
    } else {
        echo '<p>No hay registros disponibles para este recurso.</p>';
    }
}

function obtenerCampoPersonalizado($campo) {
    $nombresPersonalizados = [
        'num' => 'Número',
        'nombre' => 'Nombre',
        'primerApe' => 'Primer Apellido',
        'segundoApe' => 'Segundo Apellido',
        'telefono' => 'Teléfono',
        'email' => 'Correo Electrónico',
        'puesto' => 'Puesto',
        'estadoEmpleado' => 'Estado del Empleado',
        'numSerie' => 'Número de Serie',
        'capacidadCarga' => 'Capacidad de Carga',
        'tipoCarga' => 'Tipo de Carga',
        'disponibilidad' => 'Disponibilidad',
        'marca' => 'Marca',
        'modelo' => 'Modelo',
        'categoriaVehiculo' => 'Categoría del Vehículo'
    ];
    return isset($nombresPersonalizados[$campo]) ? $nombresPersonalizados[$campo] : $campo;
}
?>

<style>
.content-origin {
    all: unset;
    width: 80%;
    max-height: 100vh;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: start;
}

.content-card {
    width: 100%;
    display: flex;
    justify-content: center;
    align-content: start;
    padding: 20px;
    background-color: #f9f9f9;
}

.tarjetas-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    align-content: start;
}

.tarjeta {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 15px;
    width: 250px;
    background-color: #ffffff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.tarjeta:hover {
    transform: scale(1.05);
}

.tarjeta-contenido p {
    margin: 10px 0;
}

.tarjeta-acciones {
    margin-top: 15px;
    text-align: center;
}

.tarjeta-acciones button {
    margin: 5px;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    background-color: #007bff;
    color: white;
}

.tarjeta-acciones button:hover {
    background-color: #0056b3;
}

.btn-back {
    margin: 20px;
    text-align: center;
}

.btn-back button {
    padding: 10px 20px;
    background-color: #6c757d;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-back button:hover {
    background-color: #5a6268;
}
</style>

<script>
function editarRecurso(tipo, id) {
    // Redirigir a la página de edición del recurso con mejor manejo de URL
    const baseURL = window.location.origin + window.location.pathname;
    window.location.href = `${baseURL}?action=editar&tipo=${tipo}&id=${id}`;
}

function eliminarRecurso(tipo, id) {
    if (confirm('Está seguro de que desea eliminar este recurso?')) {
        // Redirigir a la página de eliminación del recurso con mejor manejo de URL
        const baseURL = window.location.origin + window.location.pathname;
        window.location.href = `${baseURL}?action=eliminar&tipo=${tipo}&id=${id}`;
    }
}
</script>