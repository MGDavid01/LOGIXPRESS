<?php
require('../../includes/config/conection.php');
$db = connectTo2DB();

header('Content-Type: application/json');

// Consulta para obtener las categorías de vehículos
$queryCategorias = "SELECT codigo AS id, descripcion AS nombre
FROM cat_vehi";
$result = $db->query($queryCategorias);

$categorias = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row;
    }
}

echo json_encode(['categorias' => $categorias]);
?>
