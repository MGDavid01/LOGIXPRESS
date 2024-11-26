<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registerDelivery') {
    $fechaEntrega = $_POST['fechaEntrega'];
    $horaInicio = date("H:i", strtotime($_POST['horaInicio']));
    $horaFin = date("H:i", strtotime($_POST['horaFin']));
    $tipoCarga = $_POST['tipoCarga'];
    $prioridad = $_POST['prioridad'];
    $clienteId = $_SESSION['user_id'];
    $productos = $_POST['producto'];
    $cantidades = $_POST['cantidad'];
    $origen = $_POST['originLocation']; // Añadido: obtener el valor de la ubicación de origen
    $destinos = $_POST['desLocation']; // Lista de destinos (array)

    // Inicializar acumulador para volumen total
    $volumenTotalEntrega = 0;

    // Registrar la entrega base
    $query = "CALL SP_registrarEntregaBase('$fechaEntrega', '$horaInicio', '$horaFin', 
              '$tipoCarga', $clienteId, '$prioridad', @entrega_id, @mensaje)";
    $resultRegistroBase = mysqli_query($db, $query);

    if ($resultRegistroBase) {
        // Obtener ID de la entrega
        $resultSalidaRegistroBase = $db->query("SELECT @entrega_id AS entrega_id, @mensaje AS mensaje");
        $row = $resultSalidaRegistroBase->fetch_assoc();
        $entrega_id = $row['entrega_id'];
        $mensaje = $row['mensaje'];
        $_SESSION['entrega_id'] = $entrega_id;

        if ($entrega_id) {
            // Asociar la ubicación de origen a la entrega
            $queryAsociarOrigen = "CALL SP_asociarOrigenEntrega($entrega_id, '$origen', @mensaje)";
            $resultAsociarOrigen = mysqli_query($db, $queryAsociarOrigen);

            if ($resultAsociarOrigen) {
                $resultAsociarOrigen = $db->query("SELECT @mensaje AS mensaje");
                $mensajeAsociacion = $resultAsociarOrigen->fetch_assoc()['mensaje'];

                if ($mensajeAsociacion === 'OK') {
                    // Asociar cada ubicación de destino a la entrega
                    foreach ($destinos as $destino) {
                        $queryAsociarDestino = "CALL SP_asociarDestinosEntrega($entrega_id, '$destino', @mensaje)";
                        $resultAsociarDestino = mysqli_query($db, $queryAsociarDestino);

                        if ($resultAsociarDestino) {
                            $resultAsociarDestino = $db->query("SELECT @mensaje AS mensaje");
                            $mensajeAsociacionDestino = $resultAsociarDestino->fetch_assoc()['mensaje'];

                            if ($mensajeAsociacionDestino !== 'OK') {
                                echo "<p>Error al asociar el destino $destino: $mensajeAsociacionDestino</p>";
                                exit;
                            }
                        } else {
                            echo "<p>Error al asociar el destino $destino.</p>";
                            exit;
                        }
                    }

                    // Proceso de productos y cálculo de la entrega continúa aquí
                    if (!empty($productos) && !empty($cantidades) && count($productos) === count($cantidades)) {
                        for ($a = 0; $a < count($productos); $a++) {
                            $producto = $productos[$a];
                            $cantidad = (int)$cantidades[$a];

                            // Registrar el producto en la entrega
                            $queryRegistrarProducto = "CALL SP_registrarProductoEntrega($entrega_id, '$producto', $cantidad, @mensaje)";
                            $resultRegistrarProducto = mysqli_query($db, $queryRegistrarProducto);

                            if ($resultRegistrarProducto) {
                                // Calcular el volumen del producto
                                $queryVolumen = "CALL SP_calcularVolumen('$producto', $cantidad, @volumenProducto, @mensaje)";
                                $resultVolumen = mysqli_query($db, $queryVolumen);

                                if ($resultVolumen) {
                                    $resultVolumen = $db->query("SELECT @volumenProducto AS volumenProducto, @mensaje AS mensaje");
                                    $rowVolumen = $resultVolumen->fetch_assoc();
                                    $volumenProducto = $rowVolumen['volumenProducto'];
                                    $mensajeVolumen = $rowVolumen['mensaje'];

                                    if ($mensajeVolumen === 'OK') {
                                        $volumenTotalEntrega += $volumenProducto;
                                    } else {
                                        echo "<p>Error al calcular el volumen del producto $producto: $mensajeVolumen</p>";
                                        exit;
                                    }
                                } else {
                                    echo "<p>El producto $producto tuvo un error.</p>";
                                    exit;
                                }
                            } else {
                                echo "<p>Error al registrar el producto $producto.</p>";
                                exit;
                            }
                        }

                        // Calcular la entrega completa
                        $queryCalcularEntrega = "CALL SP_calcularEntrega($entrega_id, $volumenTotalEntrega, @mensaje)";
                        $resultCalcularEntrega = mysqli_query($db, $queryCalcularEntrega);

                        if ($resultCalcularEntrega) {
                            $resultCalcularEntrega = $db->query("SELECT @mensaje AS mensaje");
                            $mensajeCalculo = $resultCalcularEntrega->fetch_assoc()['mensaje'];

                            if ($mensajeCalculo === 'OK') {
                                header("Location: menuCL.php?status=registeredDelivery");
                                exit;
                            } else {
                                echo "<p>Error al calcular la entrega: $mensajeCalculo</p>";
                                exit;
                            }
                        } else {
                            echo "<p>Error al calcular la entrega.</p>";
                            exit;
                        }
                    } else {
                        echo "<p>Error: No se encontraron productos o las cantidades no coinciden.</p>";
                    }
                } else {
                    echo "<p>Error al asociar la ubicación de origen: $mensajeAsociacion</p>";
                    exit;
                }
            } else {
                echo "<p>Error al ejecutar SP_asociarOrigenEntrega.</p>";
                exit;
            }
        } else {
            echo "<p>Error: $mensaje</p>";
        }

    } else {
        echo "<p>Error al registrar la entrega base.</p>";
    }
}
?>