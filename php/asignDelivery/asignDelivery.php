<?php
// Manejo de solicitudes POST para asignación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['asignarEntrega'])) {
        $entregaId = filter_input(INPUT_POST, 'entrega', FILTER_VALIDATE_INT);

        if ($entregaId) {
            // Obtener información de la entrega seleccionada
            $queryEntrega = "
                SELECT e.pesoTotal, e.volumenTotal, e.tipoCarga
                FROM entrega e
                WHERE e.num = ?";
            $stmtEntrega = $db->prepare($queryEntrega);
            if ($stmtEntrega) {
                $stmtEntrega->bind_param('i', $entregaId);
                $stmtEntrega->execute();
                $resultEntrega = $stmtEntrega->get_result();
                $entrega = $resultEntrega->fetch_assoc();
            } else {
                echo "<p>Error al preparar la consulta para obtener la entrega.</p>";
            }
        } else {
            echo "<p>Error: ID de entrega inválido.</p>";
        }
    } elseif (isset($_POST['guardarAsignacion'])) {
        $entregaId = filter_input(INPUT_POST, 'entrega', FILTER_VALIDATE_INT);
        $empleadoId = filter_input(INPUT_POST, 'empleado', FILTER_VALIDATE_INT);
        $vehiculoId = filter_input(INPUT_POST, 'vehiculo', FILTER_VALIDATE_INT);
        $remolqueId = filter_input(INPUT_POST, 'remolque', FILTER_VALIDATE_INT);

        // Validar campos obligatorios
        if ($entregaId && $empleadoId && $vehiculoId) {
            try {
                // Iniciar transacción
                $db->begin_transaction();

                // Asignar empleado a la entrega
                $query1 = "INSERT INTO entre_empleado (entrega, empleado) VALUES (?, ?)";
                $stmt1 = $db->prepare($query1);
                $stmt1->bind_param('ii', $entregaId, $empleadoId);
                $stmt1->execute();

                // Si no se seleccionó un remolque, asignar NULL
                $remolqueId = !empty($remolqueId) ? $remolqueId : 0;

                // Asignar vehículo a la entrega, junto con el remolque (incluso si es NULL)
                $query2 = "INSERT INTO entre_vehi_remo (entrega, vehiculo, remolque) VALUES (?, ?, ?)";
                $stmt2 = $db->prepare($query2);
                if ($stmt2) {
                    // En bind_param, 'i' representa un valor entero y 's' para string. Para null, usamos 's' y pasamos null como valor
                    $stmt2->bind_param('iii', $entregaId, $vehiculoId, $remolqueId);
                    $stmt2->execute();
                } else {
                    echo "<p>Error al preparar la consulta para asignar el vehículo y remolque a la entrega.</p>";
                }

                // Confirmar la transacción
                $db->commit();
                echo "<p>Asignación realizada correctamente.</p>";
            } catch (Exception $e) {
                $db->rollback();
                echo "<p>Error al guardar la asignación: {$e->getMessage()}</p>";
            }
        } else {
            echo "<p>Error: Datos inválidos. Por favor, revisa los campos obligatorios.</p>";
        }
    }
}
?>

<link rel="stylesheet" href="css/menuCHD/vistaAsignarEntregas.css">

<script>
// Función para mostrar/ocultar el campo de remolque según la categoría seleccionada
function toggleRemolqueField() {
    const categoriaSelect = document.getElementById('categoriaVehiculo');
    const remolqueContainer = document.getElementById('remolqueContainer');
    const selectedCategoria = categoriaSelect.options[categoriaSelect.selectedIndex].getAttribute('data-tipoCarga');

    // Mostrar el campo de remolque solo si la categoría seleccionada permite un remolque
    if (selectedCategoria === 'UNV') {
        remolqueContainer.style.display = 'block';
    } else {
        remolqueContainer.style.display = 'none';
    }

    // Llamar a la función para actualizar los vehículos disponibles
    actualizarVehiculosDisponibles();
}

// Función para actualizar la lista de vehículos según la categoría seleccionada
function actualizarVehiculosDisponibles() {
    const categoriaSelect = document.getElementById('categoriaVehiculo');
    const categoria = categoriaSelect.value;
    const vehiculoSelect = document.getElementById('vehiculo');

    if (categoria) {
        // Llamada AJAX para obtener los vehículos disponibles para la categoría seleccionada
        fetch(`/LOGIXPRESS/php/asignDelivery/obtenerVehiculos.php?categoria=${categoria}`)
            .then(response => response.json())
            .then(data => {
                // Limpiar las opciones actuales
                vehiculoSelect.innerHTML = '';

                // Agregar las nuevas opciones
                if (data.length > 0) {
                    data.forEach(vehiculo => {
                        const option = document.createElement('option');
                        option.value = vehiculo.num;
                        option.textContent = `Vehículo ${vehiculo.numSerie}`;
                        vehiculoSelect.appendChild(option);
                    });
                } else {
                    // Mostrar mensaje cuando no hay vehículos disponibles
                    const option = document.createElement('option');
                    option.disabled = true;
                    option.textContent = 'No hay vehículos disponibles';
                    vehiculoSelect.appendChild(option);
                }
            })
            .catch(error => {
                console.error('Error al obtener vehículos:', error);
            });
    } else {
        // Limpiar las opciones si no hay categoría seleccionada
        vehiculoSelect.innerHTML = '<option disabled>Seleccione una categoría</option>';
    }
}
</script>

<div class="tools">
    <!-- Contenedor Tabla -->
    <div class="table-size">
        <h2>Asignar Empleados, Vehículos y Remolques a Entregas</h2>
        <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <th>Entrega ID</th>
            <th>Fecha</th>
            <th>Tipo de Carga</th>
            <th>Acciones</th>
        </tr>
        <?php
        // Consulta para seleccionar entregas sin empleado, vehículo y remolque asignados
        $query = "
            SELECT e.num AS entregaId, e.fechaRegistro, e.pesoTotal, e.volumenTotal, tc.descripcion AS tipoCarga
            FROM entrega e
            INNER JOIN tipo_carga tc ON e.tipoCarga = tc.codigo
            WHERE NOT EXISTS (
                SELECT 1
                FROM entre_empleado emp
                WHERE emp.entrega = e.num
            )
            OR NOT EXISTS (
                SELECT 1
                FROM entre_vehi_remo ev
                WHERE ev.entrega = e.num AND ev.remolque IS NOT NULL
            )";
        $result = $db->query($query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['entregaId']}</td>";
                echo "<td>{$row['fechaRegistro']}</td>";
                echo "<td>{$row['tipoCarga']}</td>";
                echo "<td>
                    <form method='POST' action=''>
                        <input type='hidden' name='entrega' value='{$row['entregaId']}'>
                        <button type='submit' name='asignarEntrega'>Asignar</button>
                    </form>
                </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No hay entregas pendientes sin asignar.</td></tr>";
        }
        ?>
    </table>
    </div>

    <!-- Contenedor Formulario -->
    <div class="form-resurces">
        <?php
        if (!empty($entrega)) {
            ?>
            <h3>Asignar Recursos a Entrega <?php echo htmlspecialchars($entregaId); ?></h3>
            <form method="POST" action="">
                <input type="hidden" name="entrega" value="<?php echo htmlspecialchars($entregaId); ?>">

                <!-- Empleados -->
                <label for="empleado">Empleado:</label>
                <select name="empleado" id="empleado" required>
                    <?php
                    $queryEmpleados = "
                        SELECT num, nombre 
                        FROM empleado 
                        WHERE puesto = 'CHF' AND estadoEmpleado = 'ACT'";
                    $resultEmpleados = $db->query($queryEmpleados);
                    if ($resultEmpleados && $resultEmpleados->num_rows > 0) {
                        while ($row = $resultEmpleados->fetch_assoc()) {
                            echo "<option value='{$row['num']}'>" . htmlspecialchars($row['nombre']) . "</option>";
                        }
                    } else {
                        echo "<option disabled>No hay choferes disponibles</option>";
                    }
                    ?>
                </select>
                <br>

                <!-- Categoría de Vehículo -->
                <label for="categoriaVehiculo">Categoría del Vehículo:</label>
                <select name="categoriaVehiculo" id="categoriaVehiculo" onchange="toggleRemolqueField()" required>
                    <option value="">Seleccione una categoría</option>
                    <?php
                    // Consulta para obtener las categorías de vehículos desde la base de datos
                    $queryCategorias = "SELECT codigo, descripcion, tipoCarga FROM cat_vehi";
                    $resultCategorias = $db->query($queryCategorias);
                    if ($resultCategorias && $resultCategorias->num_rows > 0) {
                        while ($row = $resultCategorias->fetch_assoc()) {
                            echo "<option value='{$row['codigo']}' data-tipoCarga='{$row['tipoCarga']}'>" . htmlspecialchars($row['descripcion']) . "</option>";
                        }
                    }
                    ?>
                </select>
                <br>

                <!-- Vehículos -->
                <label for="vehiculo">Vehículo:</label>
                <select name="vehiculo" id="vehiculo" required>
                    <option value="" disabled>Seleccione una categoría primero</option>
                </select>
                <br>

                <!-- Remolques -->
                <div id="remolqueContainer" style="display: none;">
                    <label for="remolque">Remolque:</label>
                        <?php
                            // Obtener tipo de carga de la entrega seleccionada
                            $queryTipoCarga = "SELECT e.tipoCarga, e.pesoTotal
                                            FROM entrega e
                                            WHERE e.num = ?";
                            $stmtTipoCarga = $db->prepare($queryTipoCarga);
                            if ($stmtTipoCarga) {
                                $stmtTipoCarga->bind_param('i', $entregaId);
                                $stmtTipoCarga->execute();
                                $resultTipoCarga = $stmtTipoCarga->get_result();
                                $entrega = $resultTipoCarga->fetch_assoc(); // Obtener la entrega con tipo de carga y peso total
                                
                                if ($entrega) {
                                    // Ahora que tenemos tipoCarga, procedemos con la consulta de remolques
                                    $queryRemolques = "
                                        SELECT r.num, r.numSerie
                                        FROM remolque r
                                        WHERE r.tipoCarga = ?
                                        AND r.capacidadCarga >= ?
                                        AND r.disponibilidad = 'DISPO'";
                                    
                                    $stmtRemolques = $db->prepare($queryRemolques);
                                    if ($stmtRemolques) {
                                        // Utilizar los valores obtenidos de la consulta de tipoCarga y pesoTotal
                                        $stmtRemolques->bind_param('sd', $entrega['tipoCarga'], $entrega['pesoTotal']);
                                        $stmtRemolques->execute();
                                        $resultRemolques = $stmtRemolques->get_result();
                                        
                                        // Imprimir las opciones del select
                                        echo '<select name="remolque" id="remolque">';
                                        echo '<option value="">Sin Remolque</option>';
                                        
                                        if ($resultRemolques && $resultRemolques->num_rows > 0) {
                                            while ($row = $resultRemolques->fetch_assoc()) {
                                                echo "<option value='{$row['num']}'>Remolque {$row['numSerie']}</option>";
                                            }
                                        } else {
                                            echo "<option disabled>No hay remolques disponibles</option>";
                                        }

                                        echo '</select>';
                                    } else {
                                        echo "<p>Error al preparar la consulta para obtener los remolques.</p>";
                                    }
                                } else {
                                    echo "<p>Error: No se encontró la entrega seleccionada.</p>";
                                }
                            } else {
                                echo "<p>Error al preparar la consulta para obtener el tipo de carga.</p>";
                            }
                            ?>
                </div>
                <br>

                <button type="submit" name="guardarAsignacion">Guardar</button>
            </form>
            <?php
        } else {
            echo "<p>No se encontró la entrega seleccionada.</p>";
        }
        ?>
    </div>
</div>
