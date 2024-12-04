<?php
require('../../includes/config/conection.php');
$db = connectTo2DB();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$entregaId = filter_input(INPUT_GET, 'entregaId', FILTER_SANITIZE_NUMBER_INT);

header('Content-Type: application/json');

// Obtener los tipos de carga de la entrega específica
$queryTipoCarga = "SELECT tipoCarga FROM entre_tipoCarga WHERE entrega = ?";
$stmtTipoCarga = $db->prepare($queryTipoCarga);
$stmtTipoCarga->bind_param('i', $entregaId);
$stmtTipoCarga->execute();
$resultTipoCarga = $stmtTipoCarga->get_result();

$tiposCarga = [];
if ($resultTipoCarga && $resultTipoCarga->num_rows > 0) {
    while ($row = $resultTipoCarga->fetch_assoc()) {
        $tiposCarga[] = $row['tipoCarga'];
    }
} else {
    echo json_encode(['error' => 'Error: No se encontró la entrega o los tipos de carga']);
    exit;
}

// Verificar si hay al menos un tipo de carga
if (empty($tiposCarga)) {
    echo json_encode(['error' => 'No se encontraron tipos de carga asociados a la entrega']);
    exit;
}

$remolquesDisponibles = [];

// Crear una consulta para buscar remolques dependiendo de los tipos de carga
if (in_array('GEN', $tiposCarga)) {
    // Buscar remolques que soporten carga general
    $query = "SELECT num, numSerie FROM remolque WHERE tipoCarga = 'GEN' AND disponibilidad = 'DISPO'";
    $result = $db->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $remolquesDisponibles[] = [
                'num' => $row['num'],
                'numSerie' => $row['numSerie']
            ];
        }
    }
}

if (in_array('GRN', $tiposCarga)) {
    // Buscar remolques que soporten carga a granel
    $query = "SELECT num, numSerie FROM remolque WHERE tipoCarga = 'GRN' AND disponibilidad = 'DISPO'";
    $result = $db->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $remolquesDisponibles[] = [
                'num' => $row['num'],
                'numSerie' => $row['numSerie']
            ];
        }
    }
}

if (in_array('PER', $tiposCarga)) {
    // Buscar remolques que soporten carga perecedera
    $query = "SELECT num, numSerie FROM remolque WHERE tipoCarga = 'PER' AND disponibilidad = 'DISPO'";
    $result = $db->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $remolquesDisponibles[] = [
                'num' => $row['num'],
                'numSerie' => $row['numSerie']
            ];
        }
    }
}

echo json_encode(['remolques' => $remolquesDisponibles]);
?>