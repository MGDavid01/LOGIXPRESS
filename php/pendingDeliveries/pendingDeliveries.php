<div class="tables-deliveries">
    <div class="section">
        <h2>Entregas Pendientes</h2>
            <table>
                <tr>
                    <th>Entrega</th>
                    <th>Fecha</th>
                    <th>Empleado</th>
                    <th>Vehículo</th>
                    <th>Remolque</th>
                    <th>Estado</th>
                    <th>Actions</th>
                </tr>
                <?php
                // Consulta actualizada con `entre_estado` como tabla principal
                $query = "
                        SELECT 
                            e.num AS entregaId,
                            e.fechaRegistro,
                            (SELECT em.nombre
                            FROM entre_empleado emp
                            INNER JOIN empleado em ON emp.empleado = em.num
                            WHERE emp.entrega = e.num) AS empleado,
                            (SELECT v.numSerie
                            FROM entre_vehi_remo ev
                            INNER JOIN vehiculo v ON ev.vehiculo = v.num
                            WHERE ev.entrega = e.num) AS vehiculo,
                            (SELECT r.numSerie
                            FROM entre_vehi_remo ev
                            INNER JOIN remolque r ON ev.remolque = r.num
                            WHERE ev.entrega = e.num) AS remolque,
                            (SELECT estado.descripcion
                            FROM entre_estado ee
                            INNER JOIN estado_entre estado ON ee.estadoEntrega = estado.codigo
                            WHERE ee.entrega = e.num AND ee.estadoEntrega = 'PROG') AS estado
                        FROM entrega e
                        WHERE EXISTS (
                            SELECT 1
                            FROM entre_estado ee
                            WHERE ee.entrega = e.num AND ee.estadoEntrega = 'PROG'
                        );
                    ";

                $result = $db->query($query);
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['entregaId']}</td>";
                        echo "<td>{$row['fechaRegistro']}</td>";
                        echo "<td>".($row['empleado'] ?? 'Sin Asignar')."</td>";
                        echo "<td>".($row['vehiculo'] ?? 'Sin Asignar')."</td>";
                        echo "<td>".($row['remolque'] ?? 'Sin Asignar')."</td>";
                        echo "<td>{$row['estado']}</td>";
                        echo "<td><button class='btn-green' type='button' onclick='mostrarModal(".$row['entregaId'].")'>Ver detalles de la entrega</button>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No hay entregas pendientes programadas.</td></tr>";
                }
                ?>
            </table>
    </div>
    <div class="sector">
        
    </div>
</div>
<!-- Modal para Detalles de Entrega -->
<div id="modalDetallesEntrega" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h2>Detalles de la Entrega <span id="entregaIdModal"></span></h2>
        <div id="detallesContenido">
            <!-- Aquí se cargarán los detalles desde la petición AJAX -->
        </div>
    </div>
</div>