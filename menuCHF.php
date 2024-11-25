<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require('includes/config/conection.php');
$db = connectTo2DB();
// Funcion para mostrar el inicio
function vistaInicial(){
    echo "<p id='welcome'>Welcome, " . $_SESSION['nombre'] . "</p>";
}
// Función para mostrar la entrega asignada
function vistaEntrega() {
    global $db;
    $entrega = [];
    $query = "SELECT num, fechaInicio, fechaFin, CONCAT(horaInicio,' - ', horaFin) AS 'ventanaHorario',
     (SELECT es.descripcion
        FROM entre_estado en
        INNER JOIN estado_entre es on es.codigo = en.estadoEntrega
        WHERE e.num = en.entrega) AS estado, (SELECT p.descripcion
                                                FROM prioridad p
                                                WHERE e.prioridad = p.codigo)prioridad FROM entrega e";
    $result = mysqli_query($db, $query);
    if (!$result) {
        die("Error en la consulta: " . mysqli_error($db));
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $entrega[] = $row;
    }

    // Mostrar los datos en una tabla HTML
    if (!empty($entrega)) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr>
                <th>Number</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Time Window</th>
                <th>Status</th>
                <th>Priority</th>
            </tr>";
        
        foreach ($entrega as $row) {
            echo "<tr>
                    <td>{$row['num']}</td>
                    <td>{$row['fechaInicio']}</td>
                    <td>{$row['fechaFin']}</td>
                    <td>{$row['ventanaHorario']}</td>
                    <td>{$row['estado']}</td>
                    <td>{$row['prioridad']}</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p id='no-delivery'>There Are No Deliveries Assigned.</p>";
    }
}
?>
<?php
// Función para mostrar detalles de la entrega
function vistaDetallesEntrega(){

}
// Función para mostrar el formulario de incidentes
function vistaIncidentes(){

}
$section = isset($_GET['section']) ? $_GET['section'] : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && $_POST['accion'] === 'logout') {
        // Cerrar sesión
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }
}
include_once('includes/headUsers.php');
?>
<link rel="stylesheet" href="css/menuDriver.css">
    <nav>
        <div class="logo-container">
            <img src="imagenes/LOGIXPRESS_LOGO_F2.png" alt="Logo">
        </div>
        <ul>
            <li><a href="?section=entrega">Delivery</a></li>
            <li><a href="?section=detallesEntrega">Delivery Details</a></li>
            <li><a href="?section=incidentes">Incidents</a></li>
        </ul>
        <!-- Botón de Logout -->
        <form action="" method="post" >
            <button type="submit" name="accion" value="logout">Log out</button>
        </form>
    </nav>

    <div class="main-content">
        <?php
        switch ($section) {
            case 'entrega':
                vistaEntrega();
                break;
            case 'detailsDelivery':
                vistaDetallesEntrega();
                break;
            case 'incidents':
                vistaIncidentes();
                break;
            default:
                vistaInicial();
                break;
        }
        ?>
    </div>
</div>
</body>
</html>