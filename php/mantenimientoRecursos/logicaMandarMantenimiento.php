<?php
require ('../../includes/config/conection.php');
$db = connectTo2DB();
header('Content-Type: application/json');

// Obtener los datos de la solicitud POST
$input = json_decode(file_get_contents('php://input'), true);
$vehiculoId = $input['vehiculoId'];

// Validar que se haya proporcionado el ID del vehículo
if (!$vehiculoId) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID de vehículo.']);
    exit;
}

// Marcar el vehículo como en mantenimiento en la base de datos
$query = "UPDATE vehiculo SET disponibilidad = 'MANTE' WHERE num = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('i', $vehiculoId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el estado del vehículo.']);
}
?>