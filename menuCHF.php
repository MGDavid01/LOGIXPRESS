<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['puesto']) || $_SESSION['puesto'] !== 'CHF') {
    echo "Empleado no autenticado o sin permisos.";
    exit();
}

require_once('includes/config/conection.php');

$db = connectTo2DB();
if (!$db) {
    die("Error en la conexión a la base de datos.");
}

//Por ahora solo el log out
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && $_POST['accion'] === 'logout') {
        // Cerrar sesión
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }
}
// Función para obtener el ID del empleado logueado
function getEmpleadoId() {
    return isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
}
// Función para mostrar el inicio
function vistaInicial() {
    echo "<p id='welcome'>Welcome, " . $_SESSION['nombre'] . "</p>";
}

// Función para mostrar entregas pendientes
function vistaEntregasPendientes() {
    global $db;
    $empleadoId = getEmpleadoId();
    $entregas = [];

    if (!$empleadoId) {
        echo "<p>Error: Empleado no autenticado.</p>";
        return;
    }

    $query = "
        SELECT e.num, e.fechaRegistro, e.fechaEntrega, CONCAT(e.horaInicio, ' - ', e.horaFin) AS ventanaHorario
        FROM entrega e
        INNER JOIN entre_empleado ee ON e.num = ee.entrega
        WHERE ee.empleado = ?
          AND (SELECT en.estadoEntrega 
               FROM entre_estado en
               WHERE en.entrega = e.num
               ORDER BY en.fechaCambio DESC
               LIMIT 1) = 'ATEN'
    ";

    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $empleadoId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $entregas[] = $row;
    }
    
    if (!empty($entregas)) {
        echo "<table border='1'>";
        echo "<tr>
                <th>Número</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Horario</th>
                <th>Acciones</th>
            </tr>";
        
        foreach ($entregas as $row) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['num']) . "</td>
                    <td>" . htmlspecialchars($row['fechaRegistro']) . "</td>
                    <td>" . htmlspecialchars($row['fechaEntrega']) . "</td>
                    <td>" . htmlspecialchars($row['ventanaHorario']) . "</td>
                    <td><a class='btn-green' href='?section=routeDelivery&entrega=" . htmlspecialchars($row['num']) . "'>Ver Ruta</a></td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay entregas pendientes asignadas.</p>";
    }
}

// Renderizar la página principal
$section = isset($_GET['section']) ? htmlspecialchars($_GET['section']) : '';
$entrega = isset($_GET['entrega']) ? intval($_GET['entrega']) : null;
include_once('includes/headUsers.php');
?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/menuBase.css">
    <link rel="stylesheet" href="css/menuCHF/menuCHF.css">
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/tables.css">
    <title>Menu CHF</title>
<nav class="side-nav">
    <div class="logo-container">    
        <a href="menuCHF.php" id="logo-hover"><img src="imagenes/LOGIXPRESS_LOGO_F2.png" alt="Logo"></a>
    </div>
    <ul>
        <li><a href="?section=entrega">Delivery</a></li>
        <li><a href="?section=reportarIncidente">Report incident</a></li>
    </ul>
    <form style="all:unset;" method="post">
        <button type="submit" name="accion" value="logout">Log out</button>
    </form>
</nav>

<div class="content-origin">
    <?php
    switch ($section) {
        case 'entrega':
            ?>  <link rel="stylesheet" href="css/menuCHF/vistaEntregasPendientes.css"> <?php
            vistaEntregasPendientes();
            break;
        case 'reportarIncidente':
            break;
        case 'routeDelivery':
            ?>  <link rel="stylesheet" href="css/menuCHF/vistaRutaMapa.css"> <?php
            if (!$entrega) {
                echo "Entrega no definida.";
                exit();
            }
            require_once('mapas/mapa.php');
            break;
        default:
            ?> <link rel="stylesheet" href="css/menuCHF/vistaInicial.css"> <?php
            vistaInicial();
            break;
    }
    ?>
</div>
</body>
</html>
