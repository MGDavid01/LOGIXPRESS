<?php
// Consulta para obtener el historial de entregas
$query = "SELECT e.num, e.fechaEntrega, ee.descripcion, c.nomEmpresa AS cliente_nombre
          FROM entrega e
          INNER JOIN cliente c ON e.num = c.num
          INNER JOIN entre_estado enes ON e.num = enes.entrega
          INNER JOIN estado_entre ee ON enes.entrega = ee.codigo
          ORDER BY e.fechaEntrega DESC";


$resultCategorias = mysqli_query($db, $query);
$entregas = [];
while ($row = mysqli_fetch_assoc($resultCategorias)) {
    $entregas[] = $row;
}
?>

    <title>Historial de Entregas - Checador</title>

</head>
<body>
<div class="container">
    <h2>Historial de Entregas</h2>
    <table>
        <thead>
        <tr>
            <th>Número de Entrega</th>
            <th>Fecha de Entrega</th>
            <th>Estado</th>
            <th>Cliente</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($entregas as $entrega): ?>
            <tr>
                <td><?php echo htmlspecialchars($entrega['num']); ?></td>
                <td><?php echo htmlspecialchars($entrega['fechaEntrega']); ?></td>
                <td><?php echo htmlspecialchars($entrega['estado']); ?></td>
                <td><?php echo htmlspecialchars($entrega['cliente_nombre']); ?></td>
                <td>
                    <a href="detalle_entrega.php?num=<?php echo $entrega['num']; ?>" class="btn">Ver Detalles</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php
// detalle_entrega.php
if (isset($_GET['num'])) {
    $num_entrega = $_GET['num'];

    // Consulta para obtener los detalles de una entrega específica
    $query = "SELECT e.num, e.fechaEntrega, e.estado, c.nombre AS cliente_nombre, v.numSerie AS vehiculo, r.numSerie AS remolque, u.direccion AS ubicacion
              FROM ENTREGA e
              INNER JOIN CLIENTE c ON e.cliente_num = c.num
              LEFT JOIN VEHICULO v ON e.vehiculo_num = v.num
              LEFT JOIN REMOLQUE r ON e.remolque_num = r.num
              LEFT JOIN UBICACION u ON e.ubicacion_origen = u.codigo
              WHERE e.num = :num";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':num', $num_entrega, PDO::PARAM_INT);
    $stmt->execute();
    $entrega = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($entrega) {
        // Mostrar detalles de la entrega
        echo "<h3>Detalles de la Entrega #" . htmlspecialchars($entrega['num']) . "</h3>";
        echo "<p>Fecha de Entrega: " . htmlspecialchars($entrega['fechaEntrega']) . "</p>";
        echo "<p>Estado: " . htmlspecialchars($entrega['estado']) . "</p>";
        echo "<p>Cliente: " . htmlspecialchars($entrega['cliente_nombre']) . "</p>";
        echo "<p>Vehículo: " . htmlspecialchars($entrega['vehiculo']) . "</p>";
        echo "<p>Remolque: " . htmlspecialchars($entrega['remolque']) . "</p>";
        echo "<p>Ubicación Origen: " . htmlspecialchars($entrega['ubicacion']) . "</p>";
    } else {
        echo "<p>No se encontraron detalles para esta entrega.</p>";
    }
}
?>