<?php








if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['puesto']) || $_SESSION['puesto'] !== 'CHF') {
    echo "Empleado no autenticado o sin permisos.";
    exit();
}

require_once('includes/config/conection.php');

$db = connectTo2DB();
if (!$db) {
    die("Error en la conexión a la base de datos.");
}

// Función para obtener el ID del empleado logueado
function getEmpleadoId() {
    return isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
}

// Función para mostrar entregas pendientes
function vistaEntregasPendientes() {
    global $db;
    $empleadoId = getEmpleadoId();
    $entregas = [];

    if (!$empleadoId) {
        echo "<p>Error: Empleado no autenticado.</p>";
        return;
    }

    $query = "
        SELECT e.num, e.fechaRegistro, e.fechaEntrega, CONCAT(e.horaInicio, ' - ', e.horaFin) AS ventanaHorario
        FROM entrega e
        INNER JOIN entre_empleado ee ON e.num = ee.entrega
        WHERE ee.empleado = ?
          AND (SELECT en.estadoEntrega 
               FROM entre_estado en
               WHERE en.entrega = e.num
               ORDER BY en.fechaCambio DESC
               LIMIT 1) = 'PROG'
    ";

    // Preparar y ejecutar la consulta
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $empleadoId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Recorrer los resultados
    while ($row = $result->fetch_assoc()) {
        $entregas[] = $row;
    }
    
    if (!empty($entregas)) {
        echo "<table border='1'>";
        echo "<tr>
                <th>Número</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Horario</th>
                <th>Acciones</th>
            </tr>";
        
        foreach ($entregas as $row) {
            //$_SESSION['entrega'] = $row['num'];
            echo "<tr>
                    <td>" . htmlspecialchars($row['num']) . "</td>
                    <td>" . htmlspecialchars($row['fechaRegistro']) . "</td>
                    <td>" . htmlspecialchars($row['fechaEntrega']) . "</td>
                    <td>" . htmlspecialchars($row['ventanaHorario']) . "</td>
                    <td><a href='?section=routeDelivery&entrega=" . htmlspecialchars($row['num']) . "'>Ver Ruta</a></td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay entregas pendientes asignadas.</p>";
    }
}

// Renderizar la página principal
$section = isset($_GET['section']) ? htmlspecialchars($_GET['section']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/menuDriver.css">
    <title>Menu CHF</title>
</head>
<body>
<nav>
    <div class="logo-container">
        <img src="imagenes/LOGIXPRESS_LOGO_F2.png" alt="Logo">
    </div>
    <ul>
        <li><a href="?section=entrega">Delivery</a></li>
        <li><a href="?section=detailsDelivery">Delivery Details</a></li>
        <li><a href="?section=routeDelivery">Route</a></li>
    </ul>
    <form style="all:unset;" method="post">
        <button type="submit" name="accion" value="logout">Log out</button>
    </form>
</nav>

<div class="main-content">
    <?php
    switch ($section) {
        case 'entrega':
            vistaEntregasPendientes();
            break;
        case 'routeDelivery':
            require_once('mapas/mapa.php');
            break;
        default:
            vistaEntregasPendientes();
            break;
    }
    ?>
</div>
</body>
</html>
