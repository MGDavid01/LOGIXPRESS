<?php
$entregaId = filter_input(INPUT_POST, 'entrega', FILTER_VALIDATE_INT);
$empleadoId = filter_input(INPUT_POST, 'empleado', FILTER_VALIDATE_INT);
$vehiculoId = filter_input(INPUT_POST, 'vehiculo', FILTER_VALIDATE_INT);
$remolqueId = filter_input(INPUT_POST, 'remolque', FILTER_VALIDATE_INT);
// Mostrar mensajes de depuración para verificar los valores recibidos
if ($entregaId === false || $entregaId === null) {
    echo "<p>Error: El valor de 'entrega' no es válido o no se recibió.</p>";
}
if ($empleadoId === false || $empleadoId === null) {
    echo "<p>Error: El valor de 'empleado' no es válido o no se recibió.</p>";
}
if ($vehiculoId === false || $vehiculoId === null) {
    echo "<p>Error: El valor de 'vehículo' no es válido o no se recibió.</p>";
}
if ($remolqueId === false || $remolqueId === null) {
    $remolqueId = 1;
}

// Validar campos obligatorios
if ($entregaId && $empleadoId && $vehiculoId && $remolqueId) {
    try {
        // Preparar la llamada al procedimiento almacenado
        $stmt = $db->prepare("CALL SP_asignarRecursosEntrega(?, ?, ?, ?, @mensaje)");
        if ($stmt) {
            // Si no se seleccionó un remolque, asignar NULL (o en este caso valor por defecto 1)
            
            $stmt->bind_param("iiii", $entregaId, $empleadoId, $vehiculoId, $remolqueId);

            if ($stmt->execute()) {
                // Obtener el valor del mensaje de salida
                $result = $db->query("SELECT @mensaje AS mensaje");
                $row = $result->fetch_assoc();
                    echo "<p>" . htmlspecialchars($row['mensaje']) . "</p>";
                if ($row['mensaje'] === "OK.") {
                    
                } else {
                    echo "<p>Error inesperado al recuperar el mensaje de salida.</p>";
                }
            } else {
                echo "<p>Error al ejecutar la petición: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Error al preparar la petición: " . $db->error . "</p>";
        }
    } catch (Exception $e) {
        echo "<p>Error al asignar la entrega: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p>Error: Datos inválidos. Por favor, revisa los campos obligatorios.</p>";
}
?>