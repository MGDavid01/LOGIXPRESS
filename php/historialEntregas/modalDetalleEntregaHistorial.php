<?php
session_start();
require('../../includes/config/conection.php');

// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = connectTo2DB();

// Validar si se ha proporcionado el número de entrega
if (isset($_GET['num'])) {
    $num_entrega = (int)$_GET['num'];
} else {
    die('No valid delivery number provided.');
}

// Consulta unificada para obtener todos los detalles relevantes de una entrega específica
$query = "SELECT e.num, e.fechaEntrega, ee.descripcion AS estado, c.nomEmpresa AS cliente_nombre, 
                 v.numSerie AS vehiculo, r.numSerie AS remolque, u.nombreUbicacion AS origen, 
                 p.descripcion AS prioridad, et.instrucciones, e.fechaRegistro AS fechaCreacion, 
                 GROUP_CONCAT(DISTINCT u_ruta.nombreUbicacion ORDER BY u_ruta.num SEPARATOR ', ') AS ruta, 
                 GROUP_CONCAT(DISTINCT emp.nombre ORDER BY emp.num SEPARATOR ', ') AS empleados,
                 GROUP_CONCAT(DISTINCT p_emp.descripcion ORDER BY p_emp.codigo SEPARATOR ', ') AS roles,
                 GROUP_CONCAT(DISTINCT veh_recurso.numSerie ORDER BY veh_recurso.num SEPARATOR ', ') AS vehiculos_recurso,
                 SUM(etr.distanciaTotal) AS total_distance,
                 GROUP_CONCAT(DISTINCT tc.descripcion ORDER BY tc.codigo SEPARATOR ', ') AS tipos_carga
          FROM entrega e
          INNER JOIN cliente c ON e.cliente = c.num
          LEFT JOIN vehiculo v ON e.num = v.num
          LEFT JOIN remolque r ON e.num = r.num
          LEFT JOIN ubi_entrega_llegada eu ON e.num = eu.entrega
          LEFT JOIN ubicacion u ON eu.ubicacion = u.num
          LEFT JOIN entre_estado enes ON e.num = enes.entrega
          LEFT JOIN estado_entre ee ON enes.estadoEntrega = ee.codigo
          LEFT JOIN ubi_entrega_llegada eu_ruta ON eu_ruta.entrega = e.num
          LEFT JOIN ubicacion u_ruta ON eu_ruta.ubicacion = u_ruta.num
          LEFT JOIN entre_empleado er ON e.num = er.entrega
          LEFT JOIN empleado emp ON er.empleado = emp.num
          LEFT JOIN puesto p_emp ON emp.puesto = p_emp.codigo
          LEFT JOIN vehiculo veh_recurso ON er.empleado = veh_recurso.num
          LEFT JOIN prioridad p ON e.prioridad = p.codigo
          LEFT JOIN entre_tipocarga et ON e.num = et.entrega
          LEFT JOIN tipo_carga tc ON et.tipoCarga = tc.codigo
          LEFT JOIN ruta etr ON e.num = etr.num
          WHERE e.num = ?
          AND enes.fechaCambio = (
              SELECT MAX(fechaCambio)
              FROM entre_estado
              WHERE entrega = e.num
          )
          GROUP BY e.num";

$stmt = $db->prepare($query);
$stmt->bind_param('i', $num_entrega);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $entrega = $result->fetch_assoc();
    // Mostrar detalles de la entrega en tablas con disposición de dos columnas
    echo "<div class='invoice-container'>";
    echo "<div class='invoice-header'>";
    echo "<h1>Delivery Report #".$entrega['num']." </h1>";
    echo "<div class='invoice-content'>";

    // Primera columna de tablas
    echo "<div class='invoice-section'>";
    echo "<h2>Client and Delivery Information</h2>";
    echo "<table class='invoice-table'>";
    echo "<tr><th>Delivery Number</th><td>" . htmlspecialchars($entrega['num']) . "</td></tr>";
    echo "<tr><th>Client</th><td>" . (!empty($entrega['cliente_nombre']) ? htmlspecialchars($entrega['cliente_nombre']) : "Not defined") . "</td></tr>";
    echo "<tr><th>Delivery Date</th><td>" . (!empty($entrega['fechaEntrega']) ? htmlspecialchars($entrega['fechaEntrega']) : "Not defined") . "</td></tr>";
    echo "<tr><th>Status</th><td>" . (!empty($entrega['estado']) ? htmlspecialchars($entrega['estado']) : "Not defined") . "</td></tr>";
    echo "<tr><th>Creation Date</th><td>" . (!empty($entrega['fechaCreacion']) ? htmlspecialchars($entrega['fechaCreacion']) : "Not defined") . "</td></tr>";
    echo "</table>";
    echo "</div>";

    echo "<div class='invoice-section'>";
    echo "<h2>Vehicle and Trailer Information</h2>";
    echo "<table class='invoice-table'>";
    echo "<tr><th>Vehicle</th><td>" . (!empty($entrega['vehiculo']) ? htmlspecialchars($entrega['vehiculo']) : "Not defined") . "</td></tr>";
    echo "<tr><th>Trailer</th><td>" . (($entrega['remolque'] != 'No Aplica') ? htmlspecialchars($entrega['remolque']) : "Not applicable") . "</td></tr>";
    echo "</table>";
    echo "</div>";

    // Segunda columna de tablas
    echo "<div class='invoice-section'>";
    echo "<h2>General Information</h2>";
    echo "<table class='invoice-table'>";
    echo "<tr><th>Priority</th><td>" . (!empty($entrega['prioridad']) ? htmlspecialchars($entrega['prioridad']) : "Not defined") . "</td></tr>";
    echo "<tr><th>Types of Load</th><td>" . (!empty($entrega['tipos_carga']) ? htmlspecialchars($entrega['tipos_carga']) : "Not defined") . "</td></tr>";
    echo "<tr><th>Instructions</th><td>" . (!empty($entrega['instrucciones']) ? htmlspecialchars($entrega['instrucciones']) : "Not defined") . "</td></tr>";
    echo "</table>";
    echo "</div>";

    echo "<div class='invoice-section'>";
    echo "<h2>Route Details</h2>";
    echo "<table class='invoice-table'>";
    echo "<tr><th>Origin Location</th><td>" . (!empty($entrega['origen']) ? htmlspecialchars($entrega['origen']) : "Not defined") . "</td></tr>";
    echo "<tr><th>Destination Locations</th><td>" . (!empty($entrega['ruta']) ? htmlspecialchars($entrega['ruta']) : "Not defined") . "</td></tr>";
    echo "<tr><th>Total Distance</th><td>" . (!empty($entrega['total_distance']) ? htmlspecialchars($entrega['total_distance']) . " km" : "Not defined") . "</td></tr>";
    echo "</table>";
    echo "</div>";

    // Segunda columna continua
    echo "<div class='invoice-section'>";
    echo "<h2>Resources Used</h2>";
    echo "<table class='invoice-table'>";
    echo "<tr><th>Driver</th><td>" . (!empty($entrega['empleados']) ? htmlspecialchars($entrega['empleados']) : "Not defined") . "</td></tr>";
    echo "<tr><th>Vehicles Used</th><td>" . (!empty($entrega['vehiculos_recurso']) ? htmlspecialchars($entrega['vehiculos_recurso']) : "Not defined") . "</td></tr>";
    echo "</table>";
    echo "</div>";
    echo "</div>"; // Cierre de invoice-content
    echo "<div class='invoice-footer'>";
    echo "<p>This is the whole report!</p>";
    echo "</div>";

    echo "</div>";
} else {
    echo "<p>No details found for this delivery.</p>";
}

$stmt->close();
$db->close();
?>
