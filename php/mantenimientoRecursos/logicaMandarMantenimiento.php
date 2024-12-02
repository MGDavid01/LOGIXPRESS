<?php
require ('../../includes/config/conection.php');
$db = connectTo2DB();
header('Content-Type: application/json');

// Obtener los datos de la solicitud POST
$input = json_decode(file_get_contents('php://input'), true);
$recursoId = $input['recursoId'];
$tipoRecurso = $input['tipoRecurso']; // Puede ser 'vehiculo' o 'remolque'

// Validar que se haya proporcionado el ID del recurso y el tipo de recurso
if (!$recursoId || !$tipoRecurso) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID de recurso o el tipo de recurso.']);
    exit;
}

// Determinar la tabla y el campo a actualizar según el tipo de recurso
if ($tipoRecurso === 'vehiculo') {
    $query = "UPDATE vehiculo SET disponibilidad = 'MANTE' WHERE num = ?";
} elseif ($tipoRecurso === 'remolque') {
    $query = "UPDATE remolque SET disponibilidad = 'MANTE' WHERE num = ?";
} else {
    echo json_encode(['success' => false, 'message' => 'Tipo de recurso no válido.']);
    exit;
}

// Preparar y ejecutar la consulta
$stmt = $db->prepare($query);
$stmt->bind_param('i', $recursoId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el estado del recurso.']);
}
?>