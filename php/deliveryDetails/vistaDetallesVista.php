<?php
    function vistaDetallesEntrega($cliente) {
        global $db;
        $entrega = [];
        $query = "SELECT num,
             (SELECT es.descripcion
                FROM entre_estado en
                INNER JOIN estado_entre es on es.codigo = en.estadoEntrega
                WHERE e.num = en.entrega LIMIT 1) AS estado 
             FROM entrega e
             WHERE e.cliente = ".$cliente.";";
        
        $result = mysqli_query($db, $query);
        if (!$result) {
            die("Error en la consulta: " . mysqli_error($db));
        }
        while ($row = mysqli_fetch_assoc($result)) {
            $entrega[] = $row;
        }
    
        // Mostrar los datos en una tabla HTML
        if (!empty($entrega)) {
            echo '<div class="datos-generales">';
                echo '<h2>Delivery Status</h2>';
                echo '<table>';
                echo '<tr>
                        <th>ID</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>';
                
                foreach ($entrega as $row) {
                    echo "<tr>
                        <td>{$row['num']}</td>
                        <td>{$row['estado']}</td>
                        <td><a class='btn-green'style='font-size:1.2rem;' href='?section=deliverDetails&entrega_id=".$row['num']."'>See Details</a></td>
                    </tr>";
                }
                echo '</table>';
            echo '</div>';
            echo '<div class="datos-desglose">';
                // Si se ha seleccionado una entrega, mostrar los detalles
                if (isset($_GET['section']) && $_GET['section'] == "deliverDetails" && isset($_GET['entrega_id'])) {
                    $entrega_id = intval($_GET['entrega_id']);
                    vistaDesgloseDetalles($entrega_id);
                } else {
                    echo '<p style="font-size:2rem;">Select a delivery to see details.</p>';
                }
            echo '</div>';
            
        }
    }    

    function vistaDesgloseDetalles($entrega_id) {
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
                        WHERE e.num = $entrega_id";
    
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
                            WHERE pe.entrega = $entrega_id";

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
                           WHERE pe.entrega = $entrega_id";

        $resultDetallesProductos = mysqli_query($db, $queryDetallesProductos);

        if (!$resultDetallesProductos || mysqli_num_rows($resultDetallesProductos) == 0) {
            echo '<p style="font-size:2rem;">Error: No se encontró información sobre los productos de la entrega.</p>';
            return;
        }

        // Inicializar acumuladores para volumen y peso totales
        $volumenTotal = 0;
        $pesoTotal = 0;
        $cantidadTotal = 0;
        ?>
        <div class="detalle-entrega">
            <!-- Estado y Datos Generales -->
            <div class="section">
                <h3>General Information</h3>
                <table>
                    <tr>
                        <th>Start Date</th>
                        <td><?= htmlspecialchars($detalle['fechaRegistro']); ?></td>
                    </tr>
                    <tr>
                        <th>End Date</th>
                        <td><?= htmlspecialchars($detalle['fechaEntrega']); ?></td>
                    </tr>
                    <tr>
                        <th>Time Window</th>
                        <td><?= htmlspecialchars($detalle['ventanaHorario']); ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?= htmlspecialchars($detalle['estado'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Priority</th>
                        <td><?= htmlspecialchars($detalle['prioridad'] ?? 'N/A'); ?></td>
                    </tr>
                </table>
            </div>

            <!-- Desglose del Precio -->
            <div class="section">
                <h3>Price Breakdown</h3>
                <table>
                    <tr>
                        <th>Weight rate</th>
                        <td><?= $detalle['tarifaPeso'] != 0 ? '$' . number_format($detalle['tarifaPeso'], 2, '.', ',') . ' MXN' : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>Distance Rate</th>
                        <td><?= $detalle['tarifaDistancia'] != 0 ? '$' . number_format($detalle['tarifaDistancia'], 2, '.', ',') . ' MXN' : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>Volume Rate</th>
                        <td><?= $detalle['tarifaVolumen'] != 0 ? '$' . number_format($detalle['tarifaVolumen'], 2, '.', ',') . ' MXN' : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>Priority Rate</th>
                        <td><?= $detalle['tarifaPrio'] != 0 ? '$' . number_format($detalle['tarifaPrio'], 2, '.', ',') . ' MXN' : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>Labeling for Surcharge</th>
                        <td><?= $detalle['tarifaEti'] != 0 ? '$' . number_format($detalle['tarifaEti'], 2, '.', ',') . ' MXN' : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>Surcharge for Categories</th>
                        <td><?= $detalle['tarifaCat'] != 0 ? '$' . number_format($detalle['tarifaCat'], 2, '.', ',') . ' MXN' : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>Subtotal</th>
                        <td>$<?= number_format($detalle['subtotal'], 2, '.', ','); ?> MXN</td>
                    </tr>
                    <tr>
                        <th>IVA</th>
                        <td>$<?= number_format($detalle['IVA'], 2, '.', ','); ?> MXN</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>$<?= number_format($detalle['precio'], 2, '.', ','); ?> MXN</td>
                    </tr>
                </table>
                <p style="margin-top: 1rem; font-family: Arial, sans-serif;">N/A = No Aplica</p>
            </div>
            <div class="section">
                <h3>Product Details (Volume and Weight)</h3>
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
            </div>
            <div class="section">
                <h3>Product Summary (Total Quantity, Weight, and Volume)</h3>
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
        </div>
        <?php

    }
      
?>