<link rel="stylesheet" href="css/menuCHD/vistaAsignarEntregas.css">
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
                    )
                ";

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
        // Si se seleccionó una entrega para asignar
        // Mostrar el formulario para asignar recursos si se selecciona una entrega
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignarEntrega'])) {
                $entregaId = filter_input(INPUT_POST, 'entrega', FILTER_VALIDATE_INT);

                // Obtener información de la entrega seleccionada
                $queryEntrega = "
                    SELECT e.pesoTotal, e.volumenTotal, e.tipoCarga
                    FROM entrega e
                    WHERE e.num = ?";
                $stmtEntrega = $db->prepare($queryEntrega);
                $stmtEntrega->bind_param('i', $entregaId);
                $stmtEntrega->execute();
                $resultEntrega = $stmtEntrega->get_result();
                $entrega = $resultEntrega->fetch_assoc();

                if ($entrega) {
                    ?>
                    <div class="form">
                        <h3>Asignar Recursos a Entrega <?php echo $entregaId; ?></h3>
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

                            <!-- Vehículos -->
                            <label for="vehiculo">Vehículo:</label>
                            <select name="vehiculo" id="vehiculo" required>
                                <?php
                                $queryVehiculos = "
                                    SELECT v.num, v.numSerie 
                                    FROM vehiculo v
                                    INNER JOIN cat_vehi cv ON v.categoriavehiculo = cv.codigo
                                    WHERE v.disponibilidad = 'DISPO'
                                    AND cv.tipoCarga = ?
                                    AND v.capacidadCarga >= ?";
                                $stmtVehiculos = $db->prepare($queryVehiculos);
                                $stmtVehiculos->bind_param('sd', $entrega['tipoCarga'], $entrega['pesoTotal']);
                                $stmtVehiculos->execute();
                                $resultVehiculos = $stmtVehiculos->get_result();
                                if ($resultVehiculos && $resultVehiculos->num_rows > 0) {
                                    while ($row = $resultVehiculos->fetch_assoc()) {
                                        echo "<option value='{$row['num']}'>Vehículo {$row['numSerie']}</option>";
                                    }
                                } else {
                                    echo "<option disabled>No hay vehículos disponibles</option>";
                                }
                                ?>
                            </select>
                            <br>

                            <!-- Remolques -->
                            <label for="remolque">Remolque (opcional):</label>
                            <select name="remolque" id="remolque">
                                <?php
                                $queryRemolques = "
                                    SELECT r.num, r.numSerie
                                    FROM remolque r
                                    WHERE  r.capacidadCarga >= ?
                                    AND r.disponibilidad = 'DISPO'";
                                $stmtRemolques = $db->prepare($queryRemolques);
                                $stmtRemolques->bind_param('d', $entrega['pesoTotal']);
                                $stmtRemolques->execute();
                                $resultRemolques = $stmtRemolques->get_result();
                                if ($resultRemolques && $resultRemolques->num_rows > 0) {
                                    while ($row = $resultRemolques->fetch_assoc()) {
                                        echo "<option value='{$row['num']}'>Remolque {$row['numSerie']}</option>";
                                    }
                                } else {
                                    echo "<option disabled>No hay remolques disponibles</option>";
                                }
                                ?>
                            </select>
                            <br>

                            <button type="submit" name="guardarAsignacion">Guardar</button>
                        </form>
                    </div>
                    <?php
                } else {
                    echo "<p>No se encontró la entrega seleccionada.</p>";
                }
            }


        // Guardar asignación
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardarAsignacion'])) {
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
        
                    // Asignar vehículo a la entrega
                    $query2 = "INSERT INTO entre_vehi_remo (entrega, vehiculo) VALUES (?, ?)";
                    $stmt2 = $db->prepare($query2);
                    $stmt2->bind_param('ii', $entregaId, $vehiculoId);
                    $stmt2->execute();
        
                    // Si se seleccionó un remolque, asignarlo al vehícu
        
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
        
        ?>
    </div>
</div>
</div>
