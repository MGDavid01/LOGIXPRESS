<?php
header('Content-Type: application/json');
global $db;

$entregaId = filter_input(INPUT_GET, 'entregaId', FILTER_VALIDATE_INT);

if ($entregaId) {
    // Obtener información de la entrega seleccionada
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
            echo json_encode([
                'success' => true,
                'entregaId' => htmlspecialchars($entrega['entregaId']),
                'fechaRegistro' => htmlspecialchars($entrega['fechaRegistro']),
                'pesoTotal' => htmlspecialchars($entrega['pesoTotal']),
                'volumenTotal' => htmlspecialchars($entrega['volumenTotal']),
                'tipoCarga' => htmlspecialchars($entrega['tipoCarga'])
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró la entrega seleccionada.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ID de entrega inválido.']);
}
?>
