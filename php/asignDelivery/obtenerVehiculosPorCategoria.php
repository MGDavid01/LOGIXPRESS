<?php
require('../../includes/config/conection.php');
$db = connectTo2DB();
// Obtener el ID de la categoría del parámetro GET
$categoriaId = filter_input(INPUT_GET, 'categoriaId', FILTER_SANITIZE_STRING);

header('Content-Type: application/json');

// Consulta para obtener los vehículos según la categoría seleccionada
$queryVehiculos = "SELECT num AS id, numSerie FROM vehiculo
WHERE categoriavehiculo = '$categoriaId' AND disponibilidad = 'DISPO'";
$result = $db->query($queryVehiculos);

$vehiculos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehiculos[] = $row;
    }
}

echo json_encode(['vehiculos' => $vehiculos]);
?>