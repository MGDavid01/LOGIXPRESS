<?php
    function vistaDetallesEntrega($cliente) {
        global $db;
        $entrega = [];
        $query = "SELECT num,
             (SELECT es.descripcion
                FROM entre_estado en
                INNER JOIN estado_entre es on es.codigo = en.estadoEntrega
                WHERE e.num = en.entrega) AS estado
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
                        <td><a href='?section=deliverDetails&entrega_id=".$row['num']."'>See Details</a></td>
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
        $queryEntrega = "SELECT e.num, e.fechaRegistro, e.fechaEntrega, CONCAT(e.horaInicio, ' - ', e.horaFin) AS ventanaHorario, 
                        p.descripcion AS prioridad, es.descripcion AS estado, e.subtotal, e.IVA, e.precio 
                        FROM entrega e
                        INNER JOIN prioridad p ON e.prioridad = p.codigo
                        INNER JOIN entre_estado en ON en.entrega = e.num
                        INNER JOIN estado_entre es ON en.estadoEntrega = es.codigo
                        WHERE e.num = $entrega_id";
    
        $resultEntrega = mysqli_query($db, $queryEntrega);
        $detalle = mysqli_fetch_assoc($resultEntrega);
    
        if (!$detalle) {
            echo '<p style="font-size:2rem;">Error: No se encontró la entrega.</p>';
            return;
        }
    
        // Mostrar los datos
        echo '<div class="detalle-entrega">';
        // Estado y Datos Generales
        echo '<div class="section">';
        echo '<h3>General Information</h3>';
        echo '<table>';
        echo '<tr><th>Start Date</th><td>' . htmlspecialchars($detalle['fechaRegistro']) . '</td></tr>';
        echo '<tr><th>End Date</th><td>' . htmlspecialchars($detalle['fechaEntrega']) . '</td></tr>';
        echo '<tr><th>Time Window</th><td>' . htmlspecialchars($detalle['ventanaHorario']) . '</td></tr>';
        echo '<tr><th>Status</th><td>' . htmlspecialchars($detalle['estado'] ?? 'N/A') . '</td></tr>';
        echo '<tr><th>Priority</th><td>' . htmlspecialchars($detalle['prioridad'] ?? 'N/A') . '</td></tr>';
        echo '</table>';
        echo '</div>';
    
        // Desglose del Precio
        echo '<div class="section">';
        echo '<h3>Price Breakdown</h3>';
        echo '<table>';
        echo '<tr><th>Subtotal</th><td>' . $detalle['subtotal'] . '</td></tr>';
        echo '<tr><th>IVA</th><td>' . $detalle['IVA'] . '</td></tr>';
        echo '<tr><th>Total</th><td>' . $detalle['precio'] . '</td></tr>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
    }
      
?>