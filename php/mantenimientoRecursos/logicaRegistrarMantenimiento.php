<?php
require ('../../includes/config/conection.php');
$db = connectTo2DB();
// Validar que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener los datos del formulario POST
    $vehiculoId = filter_input(INPUT_POST, 'vehiculoId', FILTER_VALIDATE_INT);
    $costoMantenimiento = filter_input(INPUT_POST, 'costoMantenimiento', FILTER_VALIDATE_FLOAT);
    $descripcionMantenimiento = filter_input(INPUT_POST, 'descripcionMantenimiento', FILTER_SANITIZE_STRING);

    // Validar que se haya proporcionado el ID del vehículo
    if (!$vehiculoId) {
        echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID.']);
        exit;
    }

    // Realizar la inserción en la tabla de mantenimiento
    $query = "INSERT INTO mantenimiento (fechas, costo, descripcion, vehiculo) VALUES (NOW(), ?, ?, ?)";
    $stmt = $db->prepare($query);
    if ($stmt) {
        // Vincular los parámetros de la consulta
        $stmt->bind_param('dsi', $costoMantenimiento, $descripcionMantenimiento, $vehiculoId);
        if ($stmt->execute()) {
            header('Location: ../../menuCHD.php?section=mantenimiento&mantenimiento=vehiculos&herramienta=registrar&status=success');
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo registrar el mantenimiento: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $db->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida.']);
}

?>