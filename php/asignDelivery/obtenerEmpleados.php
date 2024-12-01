<?php
require('../../includes/config/conection.php');
$db = connectTo2DB();

header('Content-Type: application/json');

// Consulta para obtener empleados disponibles
$queryEmpleados = "SELECT num AS id, CONCAT(nombre, ' ', primerApe, ' ', segundoApe) AS nombre
                FROM empleado
                WHERE puesto = 'CHF'";
$result = $db->query($queryEmpleados);

$empleados = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $empleados[] = $row;
    }
}

echo json_encode(['empleados' => $empleados]);
?>
