

<div class="tables-deliveries">
<?php
        $status = filter_input(INPUT_GET, 'status');
        if ($status == 'success') {
            ?>
            <script>
                // Recargar la página para actualizar el listado (vehículo/remolque eliminado)
                window.addEventListener('load', () => {
                    // Redirigir eliminando el parámetro `status` de la URL
                    set meout(() => {
                        const url = new URL(window.location.href);
                        url.searchParams.delete('status');
                        window.history.replaceState({}, document.title, url.toString());
                        // Mostrar el mensaje después de que la URL esté limpia
                        document.getElementById('successMessage').innerText = 'Registration Successful';
                    }, 500); // Esperar 500ms para recargar y eliminar el parámetro
                });
            </script>
            <?php
        
        ?>
        <!-- Mensaje HTML que se mostrará después de la actualización -->
        <h2 id="successMessage"></h2>
        <?php
        }else{
            ?><h2></h2><?php
        }
        ?>
    <div class="section">
        <h2>Pending Deliveries</h2>
            <table>
                <tr>
                    <th>Delivery</th>
                    <th>Date</th>
                    <th>Empleado</th>
                    <th>Employee</th>
                    <th>Trailer</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php
                // Consulta actualizada con `entre_estado` como tabla principal
                $query = "
                    SELECT 
                        e.num AS entregaId,
                        e.fechaRegistro,
                        em.nombre AS empleado,
                        v.numSerie AS vehiculo,
                        r.numSerie AS remolque,
                        estado.descripcion AS estado
                    FROM entrega e
                    LEFT JOIN entre_empleado emp ON emp.entrega = e.num
                    LEFT JOIN empleado em ON emp.empleado = em.num
                    LEFT JOIN entre_vehi_remo ev ON ev.entrega = e.num
                    LEFT JOIN vehiculo v ON ev.vehiculo = v.num
                    LEFT JOIN remolque r ON ev.remolque = r.num
                    LEFT JOIN (
                        SELECT ee1.entrega, ee1.estadoEntrega
                        FROM entre_estado ee1
                        WHERE ee1.fechaCambio = (
                            SELECT MAX(ee2.fechaCambio)
                            FROM entre_estado ee2
                            WHERE ee2.entrega = ee1.entrega
                        )
                    ) AS ult_estado ON ult_estado.entrega = e.num
                    LEFT JOIN estado_entre estado ON ult_estado.estadoEntrega = estado.codigo
                    WHERE ult_estado.estadoEntrega = 'PROG';
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
<!-- Modal para Asignación de Recursos -->
<div id="modalAsignacionRecursos" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalAsignacion()">&times;</span>
        <h2>Assign Resources to Delivery<span id="entregaIdAsignacion"></span></h2>
        <form method="POST">
            <label for="empleado">Employee:</label>
            <select name="empleado" id="empleado" required>

            </select>

            <label for="categoriaVehiculo">Vehicle Category:</label>
            <select name="categoriaVehiculo" id="categoriaVehiculo" required>

            </select>

            <label for="vehiculo">Vehicle:</label>
            <select name="vehiculo" id="vehiculo" required>

            </select>

            <!-- Campo Remolque, inicialmente oculto -->
            <div id="remolqueField" style="display: none;">
                <label for="remolque">Trailer:</label>
                <select name="remolque" id="remolque">
                    
                </select>
            </div>
            <input type="hidden" name="entrega" id="entregaHidden">
            <button type="submit" name="accion" value="asignarRecursos" class="btn-guardar">Save</button>
        </form>
    </div>
</div>
