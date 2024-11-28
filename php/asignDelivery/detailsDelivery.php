<?php
// detallesEntrega.php
require ('../../includes/config/conection.php'); // Conexión a la base de datos
$db = connectTo2DB();
header('Content-Type: application/json');

// Validar el parámetro entregaId
$entregaId = filter_input(INPUT_GET, 'entregaId', FILTER_VALIDATE_INT);

if ($entregaId) {
    $queryEntrega = "
        SELECT e.num AS entregaId, e.fechaRegistro, e.pesoTotal, e.volumenTotal, tc.descripcion AS tipoCarga
        FROM entrega e
        INNER JOIN tipo_carga tc ON e.tipoCarga = tc.codigo
        WHERE e.num = ?";
    $stmtEntrega = $db->prepare($queryEntrega);
    if ($stmtEntrega) {
        $stmtEntrega->bind_param('i', $entregaId);
        $stmtEntrega->execute();
        $resultEntrega = $stmtEntrega->get_result();
        $entrega = $resultEntrega->fetch_assoc();

        if ($entrega) {
            echo json_encode(['success' => true, 'entrega' => $entrega]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró la entrega seleccionada.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID de entrega inválido.']);
}
?>
