<?php

// Verificar la conexión a la base de datos
if (!isset($db)) {
    try {
        $db = new mysqli('localhost', 'root', '', 'logixpress'); // Ajusta los parámetros de conexión según corresponda
        if ($db->connect_error) {
            die('Error de conexión a la base de datos: ' . $db->connect_error);
        }
    } catch (Exception $e) {
        die('Error al conectar con la base de datos: ' . $e->getMessage());
    }
}

// Obtener el recurso desde la URL
$tipoRecurso = isset($_GET['tipo']) ? $_GET['tipo'] : null;
$idRecurso = isset($_GET['id']) ? $_GET['id'] : null;

if (!$tipoRecurso || !$idRecurso) {
    die('Recurso no definido.');
}

// Obtener los datos del recurso a editar
try {
    $stmt = $db->prepare("SELECT * FROM $tipoRecurso WHERE num = ?");
    $stmt->bind_param("i", $idRecurso);
    $stmt->execute();
    $result = $stmt->get_result();
    $recurso = $result->fetch_assoc();

    if (!$recurso) {
        die('Recurso no encontrado.');
    }
} catch (Exception $e) {
    die('Error al obtener el recurso: ' . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Recurso</title>
    <link rel="stylesheet" href="css/editarRecurso.css">
</head>
<body>
    <div class="form-container">
        <h2>Editar <?php echo ucfirst($tipoRecurso); ?></h2>
        <form action="guardarRecurso.php" method="POST">
            <input type="hidden" name="tipoRecurso" value="<?php echo htmlspecialchars($tipoRecurso); ?>">
            <input type="hidden" name="idRecurso" value="<?php echo htmlspecialchars($idRecurso); ?>">
            
            <?php foreach ($recurso as $campo => $valor): ?>
                <?php if ($campo == 'num') continue; ?> <!-- Omitir campo de identificador -->
                <div class="form-group">
                    <label for="<?php echo htmlspecialchars($campo); ?>"><?php echo ucfirst($campo); ?>:</label>
                    <input type="text" name="<?php echo htmlspecialchars($campo); ?>" id="<?php echo htmlspecialchars($campo); ?>" value="<?php echo htmlspecialchars($valor); ?>" required>
                </div>
            <?php endforeach; ?>

            <div class="form-actions">
                <button type="submit">Guardar Cambios</button>
                <button type="button" onclick="window.history.back()">Cancelar</button>
            </div>
        </form>
    </div>
</body>
</html>

<style>
.form-container {
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.form-container h2 {
    text-align: center;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input[type="text"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.form-actions {
    text-align: center;
    margin-top: 20px;
}

.form-actions button {
    padding: 10px 20px;
    margin: 5px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.form-actions button[type="submit"] {
    background-color: #007bff;
    color: white;
}

.form-actions button[type="button"] {
    background-color: #6c757d;
    color: white;
}

.form-actions button:hover {
    opacity: 0.9;
}
</style>
