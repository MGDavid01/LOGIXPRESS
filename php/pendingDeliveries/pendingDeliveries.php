<div>
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
                echo "<td>{$row['empleado']}</td>";
                echo "<td>{$row['vehiculo']}</td>";
                echo "<td>" . ($row['remolque'] ?? 'Sin Asignar') . "</td>";
                echo "<td>{$row['estado']}</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No hay entregas pendientes programadas.</td></tr>";
        }
        ?>
    </table>
</div>
