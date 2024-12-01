<?php
session_start();
require('../../includes/config/conection.php');

// Habilitar visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
$db = connectTo2DB(); 

// Obtener el ID de la entrega de la solicitud GET
$entregaId = filter_input(INPUT_GET, 'entregaId', FILTER_VALIDATE_INT);

if (!$entregaId) {
    echo '<p style="font-size:2rem;">Error: Entrega no localizado.</p>';
    exit;
}

// Consulta para obtener todos los detalles de la entrega y sus productos
$queryEntrega = "
    SELECT e.num AS entregaId, 
           e.fechaRegistro, 
           e.fechaEntrega, 
           CONCAT(e.horaInicio, ' - ', e.horaFin) AS ventanaHorario, 
           p.descripcion AS prioridad, 
           es.descripcion AS estado,
           tc.descripcion AS tipoCarga,
           c.nomEmpresa AS cliente,
           ubS.nombreUbicacion AS ubicacionSalida,
           pr.nombre AS productoNombre,
           pr.alto AS productoAlto,
           pr.ancho AS productoAncho,
           pr.largo AS productoLargo,
           pr.peso AS productoPeso,
           ep.cantidad AS productoCantidad,
           (pr.alto * pr.ancho * pr.largo * ep.cantidad) AS volumen_total,
           (pr.peso * ep.cantidad) AS peso_total
    FROM entrega e
    INNER JOIN prioridad p ON e.prioridad = p.codigo
    INNER JOIN entre_estado en ON en.entrega = e.num
    INNER JOIN estado_entre es ON en.estadoEntrega = es.codigo
    INNER JOIN cliente c ON e.cliente = c.num
    INNER JOIN tipo_carga tc ON e.tipoCarga = tc.codigo
    INNER JOIN ubi_entrega_salida ues ON ues.entrega = e.num
    INNER JOIN ubicacion ubS ON ues.ubicacion = ubS.num
    INNER JOIN entre_producto ep ON e.num = ep.entrega
    INNER JOIN producto pr ON ep.producto = pr.num
    WHERE e.num = ?";

$stmt = $db->prepare($queryEntrega);
if (!$stmt) {
    echo '<p style="font-size:2rem;">Error al preparar la consulta para la entrega.</p>';
    exit;
}

$stmt->bind_param('i', $entregaId);
$stmt->execute();
$resultEntrega = $stmt->get_result();

// Validar si se obtuvieron resultados
if (!$resultEntrega || $resultEntrega->num_rows == 0) {
    echo '<p style="font-size:2rem;">Error: No se encontró la entrega.</p>';
    exit;
}

// Variables para almacenar la información y los productos
$detalleEntrega = null;
$productos = [];
$volumenTotalE = 0;
$pesoTotalE = 0;

// Recopilar detalles de la entrega y productos
while ($detalle = $resultEntrega->fetch_assoc()) {
    if ($detalleEntrega === null) {
        // Solo se necesita una vez la información de la entrega
        $detalleEntrega = $detalle;
    }

    // Recopilar información de los productos
    $productos[] = [
        'nombre' => $detalle['productoNombre'],
        'alto' => $detalle['productoAlto'],
        'ancho' => $detalle['productoAncho'],
        'largo' => $detalle['productoLargo'],
        'peso' => $detalle['productoPeso'],
        'cantidad' => $detalle['productoCantidad'],
        'volumen_total' => $detalle['volumen_total'],
        'peso_total' => $detalle['peso_total'],
    ];

    // Calcular el total de volumen y peso
    $volumenTotalE += $detalle['volumen_total'];
    $pesoTotalE += $detalle['peso_total'];
}

// Consulta para obtener todas las ubicaciones de destino
$queryUbicacionesDestino = "
    SELECT ubL.nombreUbicacion AS ubicacionDestino
    FROM ubi_entrega_llegada uel
    INNER JOIN ubicacion ubL ON uel.ubicacion = ubL.num
    WHERE uel.entrega = ?";

$stmtUbicacionesDestino = $db->prepare($queryUbicacionesDestino);
$stmtUbicacionesDestino->bind_param('i', $entregaId);
$stmtUbicacionesDestino->execute();
$resultUbicacionesDestino = $stmtUbicacionesDestino->get_result();

// Almacenar las ubicaciones de destino en un array
$ubicacionesDestino = [];
while ($ubicacion = $resultUbicacionesDestino->fetch_assoc()) {
    $ubicacionesDestino[] = $ubicacion['ubicacionDestino']; // Solo el valor de la ubicación
}
?>
    <div class="entrega-detalle">
        <!-- Encabezado Principal -->
        <div class="encabezado-entrega">
            <h1>Entrega: #<?= htmlspecialchars($entregaId); ?></h1>
            <span class="estado-entrega <?= strtolower($detalleEntrega['estado'] ?? 'desconocido'); ?>">
                <?= htmlspecialchars($detalleEntrega['estado'] ?? 'Desconocido'); ?>
            </span>
        </div>

        <!-- Información General de la Entrega -->
        <div class="card">
            <h3>Información General</h3>
            <p><strong>Cliente:</strong> <?= htmlspecialchars($detalleEntrega['cliente'] ?? 'Sin definir'); ?></p>
            <p><strong>Fecha de Pedido:</strong> <?= htmlspecialchars($detalleEntrega['fechaRegistro'] ?? 'Sin definir'); ?></p>
            <p><strong>Fecha de Entrega:</strong> <?= htmlspecialchars($detalleEntrega['fechaEntrega'] ?? 'Sin definir'); ?></p>
            <p><strong>Prioridad:</strong> <?= htmlspecialchars($detalleEntrega['prioridad'] ?? 'Sin definir'); ?></p>
            <p><strong>Tipo de Carga:</strong> <?= htmlspecialchars($detalleEntrega['tipoCarga'] ?? 'Sin definir'); ?></p>
        </div>

        <!-- Ubicaciones Seleccionadas -->
        <div class="card">
            <h3>Ubicaciones</h3>
            <p><strong>Ubicación de Salida:</strong><br> <?= htmlspecialchars($detalleEntrega['ubicacionSalida'] ?? 'Sin definir'); ?></p><br>
            
            <h4>Ubicaciones de Llegada:</h4>
            <ul>
                <?php if (!empty($ubicacionesDestino)): ?>
                    <?php foreach ($ubicacionesDestino as $ubicacion): ?>
                        <li><?= htmlspecialchars($ubicacion); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>Sin definir</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Detalles del Producto -->
    <div class="card">
        <h3>Detalles de los Productos</h3>
        <table>
            <thead>
                <tr>
                    <th>Nombre del Producto</th>
                    <th>Altura (m)</th>
                    <th>Ancho (m)</th>
                    <th>Longitud (m)</th>
                    <th>Peso (kg)</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                <tr>
                    <td><?= htmlspecialchars($producto['nombre'] ?? 'No disponible'); ?></td>
                    <td><?= number_format($producto['alto'] ?? 0, 2); ?></td>
                    <td><?= number_format($producto['ancho'] ?? 0, 2); ?></td>
                    <td><?= number_format($producto['largo'] ?? 0, 2); ?></td>
                    <td><?= number_format($producto['peso'] ?? 0, 2); ?></td>
                    <td><?= htmlspecialchars($producto['cantidad'] ?? '0'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Resumen Total del Peso y Volumen -->
    <div class="card">
        <h3>Resumen de la Entrega</h3>
        <p><strong>Peso Total:</strong> <?= number_format($pesoTotalE, 2); ?> kg</p>
        <p><strong>Volumen Total:</strong> <?= number_format($volumenTotalE, 2); ?> m³</p>
    </div>

    <!-- Botón para Asignación de Recursos -->
    <!-- Agregar botón para Asignar Recursos -->
    <div class="asignacion-recursos">
       
        <button class="btn-guardar" onclick="mostrarModalAsignacion(<?= $detalleEntrega['entregaId']; ?>)">Asignar Recursos</button>
    </div>
</div>

<?php
// Cerrar conexiones y liberar recursos
$stmt->close();
$stmtUbicacionesDestino->close();
$db->close();
?>
