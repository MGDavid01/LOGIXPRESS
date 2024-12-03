<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registerDelivery') {
    // Extraer y preparar datos
    $fechaEntrega = $_POST['fechaEntrega'];
    $horaInicio = date("H:i", strtotime($_POST['horaInicio']));
    $horaFin = date("H:i", strtotime($_POST['horaFin']));
    $tipoCarga = isset($_POST['tipoCarga']) ? $_POST['tipoCarga'] : [];
    if (!is_array($tipoCarga)) {
        $tipoCarga = [$tipoCarga]; // Convertirlo en un array si no lo es
    } // Asegurarse que es un string
    $prioridad = $_POST['prioridad'];
    $clienteId = $_SESSION['user_id'];
    $productos = $_POST['producto'];
    $cantidades = $_POST['cantidad'];
    $origen = $_POST['originLocation'];
    $instrucciones = $_POST['instrucciones'];
    $destinos = isset($_POST['desLocation']) ? $_POST['desLocation'] : [];
    if (!is_array($tipoCarga)) {
        $destinos = [$destinos]; // Convertirlo en un array si no lo es
    }

    // Inicializar acumulador para volumen total
    $volumenTotalEntrega = 0;

    // Registrar entrega base
    $entrega_id = registrarEntregaBase($fechaEntrega, $horaInicio, $horaFin, $clienteId, $prioridad);

    if ($entrega_id) {
        $_SESSION['entrega_id'] = $entrega_id;

        // Asociar ubicación de origen
        if (asociarUbicacionOrigen($entrega_id, $origen)) {
            // Asociar cada ubicación de destino
            if (asociarUbicacionesDestino($entrega_id, $destinos)) {
                // Registrar productos en la entrega
                if (registrarProductosEntrega($entrega_id, $productos, $cantidades, $volumenTotalEntrega)) {
                    // Registrar los tipos de carga que lleva la entrega
                    if (asociarTiposCarga($entrega_id, $tipoCarga, $instrucciones)) {
                        // Calcular la entrega completa
                        if (calcularEntrega($entrega_id, $volumenTotalEntrega)) {
                            header("Location: menuCL.php?status=registeredDelivery");
                            exit;
                        } else {
                            mostrarError("Error al calcular la entrega.");
                        }
                    } else {
                        mostrarError("Error al asociar tipos de carga.");
                    }
                } else {
                    mostrarError("Error al registrar productos.");
                }
            } else {
                mostrarError("Error al asociar ubicaciones de destino.");
            }
        } else {
            mostrarError("Error al asociar la ubicación de origen.");
        }
    } else {
        mostrarError("Error al registrar la entrega base.");
    }
}

/**
 * Función para registrar la entrega base.
 */
function registrarEntregaBase($fechaEntrega, $horaInicio, $horaFin, $clienteId, $prioridad) {
    global $db;

    $query = "CALL SP_registrarEntregaBase(?, ?, ?, ?, ?, @entrega_id, @mensaje)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("sssis", $fechaEntrega, $horaInicio, $horaFin, $clienteId, $prioridad);
    
    if (!$stmt->execute()) {
        return false; // Error en la ejecución
    }

    // Obtener los valores de salida
    $result = $db->query("SELECT @entrega_id AS entrega_id, @mensaje AS mensaje");
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['mensaje'] === 'OK') {
            return $row['entrega_id'];
        }
    }

    return false;
}

/**
 * Función para asociar la ubicación de origen a una entrega.
 */
function asociarUbicacionOrigen($entrega_id, $origen) {
    global $db;

    $query = "CALL SP_asociarOrigenEntrega(?, ?, @mensaje)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("is", $entrega_id, $origen);
    
    if (!$stmt->execute()) {
        return false; // Error en la ejecución
    }

    // Obtener mensaje de resultado
    $result = $db->query("SELECT @mensaje AS mensaje");
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['mensaje'] === 'OK';
    }

    return false;
}

/**
 * Función para asociar las ubicaciones de destino a una entrega.
 */
function asociarUbicacionesDestino($entrega_id, $destinos) {
    global $db;

    foreach ($destinos as $destino) {
        $query = "CALL SP_asociarDestinosEntrega(?, ?, @mensaje)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("is", $entrega_id, $destino);
        
        if (!$stmt->execute()) {
            return false; // Error en la ejecución
        }

        // Obtener mensaje de resultado
        $result = $db->query("SELECT @mensaje AS mensaje");
        if ($result) {
            $row = $result->fetch_assoc();
            if ($row['mensaje'] !== 'OK') {
                return false;
            }
        } else {
            return false;
        }
    }

    return true;
}

/**
 * Función para asociar los tipos de carga a una entrega.
 */
/**
 * Función para asociar los tipos de carga a una entrega.
 */
/**
 * Función para asociar los tipos de carga a una entrega.
 */
function asociarTiposCarga($entrega_id, $tiposCarga, $instrucciones) {
    global $db;

    // Filtrar los tipos de carga para mantener solo los valores únicos
    $tiposCargaUnicos = array_unique($tiposCarga);

    foreach ($tiposCargaUnicos as $tipoCarga) {
        $query = "CALL SP_asociarTiposdeCargaEntrega(?, ?, ?, @mensaje)";
        $stmt = $db->prepare($query);

        if (!$stmt) {
            mostrarError("Error en la preparación de la consulta: " . $db->error);
            return false;
        }

        // Asociar tipo de carga con las instrucciones
        $stmt->bind_param("iss", $entrega_id, $tipoCarga, $instrucciones);

        if (!$stmt->execute()) {
            mostrarError("Error en la ejecución del SP_asociarTiposdeCargaEntrega: " . $stmt->error);
            return false;
        }

        // Obtener el mensaje de salida de la consulta
        $result = $db->query("SELECT @mensaje AS mensaje");
        if ($result) {
            $row = $result->fetch_assoc();
            if ($row['mensaje'] !== 'OK') {
                mostrarError("Error en el tipo de carga asociado: " . $row['mensaje']);
                return false;
            }
        } else {
            mostrarError("Error al obtener el mensaje del SP_asociarTiposdeCargaEntrega.");
            return false;
        }

        // Reiniciar instrucciones solo después de procesar cada carga
        $instrucciones = null;
    }

    // Si todas las asociaciones fueron exitosas
    return true;
}



/**
 * Función para registrar los productos de una entrega.
 */
function registrarProductosEntrega($entrega_id, $productos, $cantidades, &$volumenTotalEntrega) {
    global $db;

    if (!empty($productos) && !empty($cantidades) && count($productos) === count($cantidades)) {
        for ($i = 0; $i < count($productos); $i++) {
            $producto = $productos[$i];
            $cantidad = (int)$cantidades[$i];

            // Registrar el producto en la entrega
            $query = "CALL SP_registrarProductoEntrega(?, ?, ?, @mensaje)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("isi", $entrega_id, $producto, $cantidad);
            
            if (!$stmt->execute()) {
                return false; // Error en la ejecución
            }

            // Calcular el volumen del producto
            $queryVolumen = "CALL SP_calcularVolumen(?, ?, @volumenProducto, @mensaje)";
            $stmtVolumen = $db->prepare($queryVolumen);
            $stmtVolumen->bind_param("si", $producto, $cantidad);
            
            if (!$stmtVolumen->execute()) {
                return false; // Error en la ejecución
            }

            $resultVolumen = $db->query("SELECT @volumenProducto AS volumenProducto, @mensaje AS mensaje");
            if ($resultVolumen) {
                $rowVolumen = $resultVolumen->fetch_assoc();
                if ($rowVolumen['mensaje'] === 'OK') {
                    $volumenTotalEntrega += $rowVolumen['volumenProducto'];
                } else {
                    mostrarError("Error al calcular el volumen del producto $producto: {$rowVolumen['mensaje']}");
                    return false;
                }
            } else {
                mostrarError("El producto $producto tuvo un error.");
                return false;
            }
        }
        return true;
    }
    return false;
}

/**
 * Función para calcular la entrega completa.
 */
function calcularEntrega($entrega_id, $volumenTotalEntrega) {
    global $db;

    $query = "CALL SP_calcularEntrega(?, ?, @mensaje)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $entrega_id, $volumenTotalEntrega);
    
    if (!$stmt->execute()) {
        return false; // Error en la ejecución
    }

    $result = $db->query("SELECT @mensaje AS mensaje");
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['mensaje'] === 'OK';
    }

    return false;
}

/**
 * Función para mostrar errores de una manera consistente.
 */
function mostrarError($mensaje) {
    echo "<p>$mensaje</p>";
    exit;
}
?>
