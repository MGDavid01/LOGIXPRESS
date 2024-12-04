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
           u.nombreUbicacion AS origen, p.descripcion AS prioridad, et.instrucciones, 
           e.fechaRegistro AS fechaCreacion, 
           (
               SELECT GROUP_CONCAT(DISTINCT u_llegada.nombreUbicacion ORDER BY sal_entrega.entrega SEPARATOR ', ')
               FROM ubi_entrega_llegada lle_entrega
               LEFT JOIN ubicacion u_llegada ON lle_entrega.ubicacion = u_llegada.num
               LEFT JOIN ubi_entrega_salida sal_entrega ON sal_entrega.ubicacion = u_llegada.num
               WHERE lle_entrega.entrega = e.num
           ) AS ruta,
           (
               SELECT GROUP_CONCAT(DISTINCT p_emp.descripcion ORDER BY p_emp.codigo SEPARATOR ', ')
               FROM entre_empleado er_empleado
               LEFT JOIN empleado emp ON er_empleado.empleado = emp.num
               LEFT JOIN puesto p_emp ON emp.puesto = p_emp.codigo
               WHERE er_empleado.entrega = e.num
           ) AS roles,
           (
               SELECT GROUP_CONCAT(DISTINCT CONCAT(emp.nombre, ' ' , emp.primerApe, ' ' , emp.segundoApe) ORDER BY emp.num SEPARATOR ', ')
               FROM entre_empleado er_empleado
               LEFT JOIN empleado emp ON er_empleado.empleado = emp.num
               WHERE er_empleado.entrega = e.num
           ) AS empleados,
           (
               SELECT GROUP_CONCAT(DISTINCT v.numSerie ORDER BY v.num SEPARATOR ', ')
               FROM entre_vehi_remo evr_vehi
               LEFT JOIN vehiculo v ON evr_vehi.vehiculo = v.num
               WHERE evr_vehi.entrega = e.num
           ) AS vehiculos_recurso,
           (
               SELECT GROUP_CONCAT(DISTINCT r.numSerie ORDER BY r.num SEPARATOR ', ')
               FROM entre_vehi_remo evr_remo
               LEFT JOIN remolque r ON evr_remo.remolque = r.num
               WHERE evr_remo.entrega = e.num
           ) AS remolques_recurso,
           (
               SELECT SUM(etr.distanciaTotal)
               FROM ruta etr
               WHERE etr.num = e.num
           ) AS total_distance,
           (
               SELECT GROUP_CONCAT(DISTINCT tc.descripcion ORDER BY tc.codigo SEPARATOR ', ')
               FROM entre_tipocarga etc
               LEFT JOIN tipo_carga tc ON etc.tipoCarga = tc.codigo
               WHERE etc.entrega = e.num
           ) AS tipos_carga
    FROM entrega e
    INNER JOIN cliente c ON e.cliente = c.num
    LEFT JOIN ubi_entrega_llegada eu ON e.num = eu.entrega
    LEFT JOIN ubicacion u ON eu.ubicacion = u.num
    LEFT JOIN entre_estado enes ON e.num = enes.entrega
    LEFT JOIN estado_entre ee ON enes.estadoEntrega = ee.codigo
    LEFT JOIN prioridad p ON e.prioridad = p.codigo
    LEFT JOIN entre_tipocarga et ON e.num = et.entrega
    WHERE e.num = ?
    AND enes.fechaCambio = (
        SELECT MAX(fechaCambio)
        FROM entre_estado
        WHERE entrega = e.num
    )
    GROUP BY e.num;
";


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
    echo "<h2>Resources Used</h2>";
    echo "<table class='invoice-table'>";
    echo "<tr><th>Drivers</th><td>" . (!empty($entrega['empleados']) ? htmlspecialchars($entrega['empleados']) : "Not defined") . "</td></tr>";
    echo "<tr><th>Vehicles</th><td>" . (!empty($entrega['vehiculos_recurso']) ? htmlspecialchars($entrega['vehiculos_recurso']) : "Not defined") . "</td></tr>";
    echo "<tr><th>Trailers</th><td>" . 
     ((!empty($entrega['remolques_recurso']) && $entrega['remolques_recurso'] != 'No Aplica') ? 
     htmlspecialchars($entrega['remolques_recurso']) : "Not defined") . 
     "</td></tr>";
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

    echo "</div>";
} else {
    echo "<p>No details found for this delivery.</p>";
}

$stmt->close();
$db->close();
?>
