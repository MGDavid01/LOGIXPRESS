<?php
session_start();
require('../../includes/config/conection.php');

// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = connectTo2DB(); // Conexión a la base de datos usando MySQLi
$categoriaVehiculo = filter_input(INPUT_GET, 'categoria');

if ($categoriaVehiculo) {
    $queryVehiculos = "
        SELECT v.num, v.numSerie 
        FROM vehiculo v
        INNER JOIN cat_vehi cv ON v.categoriavehiculo = cv.codigo
        WHERE v.disponibilidad = 'DISPO'
        AND cv.codigo = ?";
    
    $stmtVehiculos = $db->prepare($queryVehiculos);
    $stmtVehiculos->bind_param('s', $categoriaVehiculo);
    $stmtVehiculos->execute();
    $resultVehiculos = $stmtVehiculos->get_result();
    
    $vehiculos = [];
    while ($row = $resultVehiculos->fetch_assoc()) {
        $vehiculos[] = $row;
    }

    // Devolver los vehículos en formato JSON
    header('Content-Type: application/json');
    echo json_encode($vehiculos);
} else {
    echo json_encode([]);
}
?>