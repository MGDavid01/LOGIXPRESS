<?php
require ('../../includes/config/conection.php');
$db = connectTo2DB();
// Validar que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener los datos del formulario POST
    $recursoId = filter_input(INPUT_POST, 'recursoId', FILTER_VALIDATE_INT);
    $tipoRecurso = filter_input(INPUT_POST, 'tipoRecurso', FILTER_SANITIZE_STRING);
    $costoMantenimiento = filter_input(INPUT_POST, 'costoMantenimiento', FILTER_VALIDATE_FLOAT);
    $descripcionMantenimiento = filter_input(INPUT_POST, 'descripcionMantenimiento', FILTER_SANITIZE_STRING);

    // Validar que se haya proporcionado el ID del recurso y el tipo de recurso
    if (!$recursoId || !$tipoRecurso) {
        echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID o el tipo de recurso.']);
        exit;
    }

    // Preparar la consulta de inserción en la tabla mantenimiento según el tipo de recurso
    if ($tipoRecurso === 'vehiculo') {
        $query = "INSERT INTO mantenimiento (fechas, costo, descripcion, vehiculo) VALUES (NOW(), ?, ?, ?)";
    } elseif ($tipoRecurso === 'remolque') {
        $query = "INSERT INTO mantenimiento (fechas, costo, descripcion, remolque) VALUES (NOW(), ?, ?, ?)";
    } else {
        echo json_encode(['success' => false, 'message' => 'Tipo de recurso desconocido.']);
        exit;
    }

    // Preparar y ejecutar la consulta
    $stmt = $db->prepare($query);
    if ($stmt) {
        // Vincular los parámetros de la consulta
        $stmt->bind_param('dsi', $costoMantenimiento, $descripcionMantenimiento, $recursoId);
        if ($stmt->execute()) {
            // Redirigir al menú de mantenimiento dependiendo del tipo de recurso
            if ($tipoRecurso === 'vehiculo') {
                header('Location: ../../menuCHD.php?section=mantenimiento&mantenimiento=vehiculos&herramienta=registrar&status=success');
            } elseif ($tipoRecurso === 'remolque') {
                header('Location: ../../menuCHD.php?section=mantenimiento&mantenimiento=remolques&herramienta=registrar&status=success');
            }
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
