<?php
require('../../includes/config/conection.php');
$db = connectTo2DB();

$entregaId = filter_input(INPUT_GET, 'entregaId', FILTER_SANITIZE_STRING);

header('Content-Type: application/json');

// Obtener el tipo de carga de la entrega específica
$queryTipoCarga = "SELECT tipoCarga FROM entrega WHERE num = ?";
$stmtTipoCarga = $db->prepare($queryTipoCarga);
$stmtTipoCarga->bind_param('i', $entregaId);
$stmtTipoCarga->execute();
$resultTipoCarga = $stmtTipoCarga->get_result();
$tipoCarga = null;

if ($resultTipoCarga && $resultTipoCarga->num_rows > 0) {
    $tipoCarga = $resultTipoCarga->fetch_assoc()['tipoCarga'];
} else {
    echo "Error: No se encontró la entrega o el tipo de carga";
    exit;
}

// Consulta para obtener los remolques disponibles según el tipo de carga
$queryVehiculos = "SELECT num AS id, numSerie FROM remolque
                   WHERE tipoCarga = ? AND disponibilidad = 'DISPO'";
$stmtVehiculos = $db->prepare($queryVehiculos);
$stmtVehiculos->bind_param('s', $tipoCarga);
$stmtVehiculos->execute();
$resultVehiculos = $stmtVehiculos->get_result();

$remolquesDisponibles = [];
while ($row = $resultVehiculos->fetch_assoc()) {
    $remolquesDisponibles[] = [
        'id' => $row['id'],
        'numSerie' => $row['numSerie']
    ];
}
echo json_encode(['remolques' => $remolquesDisponibles]);
?>