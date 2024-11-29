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
                        echo "<td><a href='?section=entregasPendientes&deliveryDetails={$row['entregaId']}'>See Details and Assing</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No hay entregas pendientes programadas.</td></tr>";
                }
                ?>
            </table>
    </div>
    <?php
    if($deliveryDetailsCHD != ''){
        global $db;
    
        // Consulta los detalles de la entrega con LEFT JOIN
        $queryPrecio = "SELECT e.num, e.fechaRegistro, e.fechaEntrega, 
                        CONCAT(e.horaInicio, ' - ', e.horaFin) AS ventanaHorario, 
                        p.descripcion AS prioridad, 
                        es.descripcion AS estado, 
                        e.subtotal, e.IVA, e.precio,
                        e.tarifaPeso, e.tarifaDistancia, e.tarifaVolumen, 
                        e.tarifaPrio, e.tarifaEti, e.tarifaCat
                        FROM entrega e
                        INNER JOIN prioridad p ON e.prioridad = p.codigo
                        INNER JOIN entre_estado en ON en.entrega = e.num
                        INNER JOIN estado_entre es ON en.estadoEntrega = es.codigo
                        WHERE e.num = $deliveryDetailsCHD";
    
        $resultEntrega = mysqli_query($db, $queryPrecio);
        $detalle = mysqli_fetch_assoc($resultEntrega);
    
        if (!$detalle) {
            echo '<p style="font-size:2rem;">Error: No se encontró la entrega.</p>';
            return;
        }

        // Consulta para obtener los productos de la entrega específica
        $queryProductos = "SELECT p.nombre, pe.cantidad 
                            FROM entre_producto pe
                            INNER JOIN producto p ON pe.producto = p.num
                            WHERE pe.entrega = $deliveryDetailsCHD";

        $resultProductos = mysqli_query($db, $queryProductos);
        $productos = mysqli_fetch_assoc($resultProductos);

        if (!$productos) {
            echo '<p style="font-size:2rem;">Error: No se encontró la entrega.</p>';
            return;
        }
        // Consulta para obtener los productos con su volumen, peso y otros detalles
        $queryDetallesProductos = "SELECT p.nombre, 
                                  (p.alto * p.ancho * p.largo) AS volumen,
                                  p.peso, 
                                  pe.cantidad, 
                                  (p.alto * p.ancho * p.largo * pe.cantidad) AS volumen_total,
                                  (p.peso * pe.cantidad) AS peso_total
                           FROM entre_producto pe
                           INNER JOIN producto p ON pe.producto = p.num
                           WHERE pe.entrega = $deliveryDetailsCHD";

        $resultDetallesProductos = mysqli_query($db, $queryDetallesProductos);

        if (!$resultDetallesProductos || mysqli_num_rows($resultDetallesProductos) == 0) {
            echo '<p style="font-size:2rem;">Error: No se encontró información sobre los productos de la entrega.</p>';
            return;
        }?>
        <div class="section">
            <h3 class="border">Product Details (Volume and Weight)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Individual Volume (m³)</th>
                        <th>Individual Weight (kg)</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($producto = mysqli_fetch_assoc($resultDetallesProductos)): ?>
                    <tr>
                        <td><?= htmlspecialchars($producto['nombre']); ?></td>
                        <td><?= number_format($producto['volumen'], 2); ?> m³</td>
                        <td><?= number_format($producto['peso'], 2); ?> kg</td>
                        <td><?= htmlspecialchars($producto['cantidad']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            </table>
        
            <h3 class="border">Product Summary (Total Quantity, Weight, and Volume)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Total Weight (kg)</th>
                        <th>Total Volume (m³)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $volumenTotalE = 0;
                    $pesoTotalE = 0;
                    // Reiniciar el puntero del resultado y recorrer todos los productos nuevamente
                    mysqli_data_seek($resultDetallesProductos, 0);

                    while ($producto = mysqli_fetch_assoc($resultDetallesProductos)):
                        // Sumar la cantidad, volumen y peso totales de cada producto
                        $volumenTotalE += $producto['volumen_total'];
                        $pesoTotalE += $producto['peso_total'];
                    ?>
                        <tr>
                            <td><?= number_format($producto['peso_total'], 2); ?> kg</td>
                            <td><?= number_format($producto['volumen_total'], 2); ?> m³</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th><?= number_format($pesoTotalE, 2); ?> kg</th>
                        <th><?= number_format($volumenTotalE, 2); ?> m³</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php
    }
    ?>
</div>
