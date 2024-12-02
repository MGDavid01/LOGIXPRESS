<?php
session_start();
require('includes/config/conection.php');

// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = connectTo2DB(); // Conexión a la base de datos usando MySQLi

// Función para mostrar el inicio
function vistaInicial() {
    echo "<p id='welcome'>Welcome, " . $_SESSION['nombre'] . "</p>";
}

// Cerrar sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && $_POST['accion'] === 'logout') {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    } else if (isset($_POST['accion']) && $_POST['accion'] === 'asignarRecursos') {
        include('php/asignDelivery/logicaAsignarRecursosEntrega.php');
    }
}
// Actualizar disponibilidad del vehículo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateDisponibilidad'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $disponibilidad = filter_input(INPUT_POST, 'disponibilidad', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($id && $disponibilidad) {
        $query = "UPDATE vehiculo SET disponibilidad = ? WHERE num = ?";
        $stmt = $db->prepare($query);
        if ($stmt) {
            $stmt->bind_param('si', $disponibilidad, $id);
            if ($stmt->execute()) {
                header("Location: ?section=vehiculosMantenimiento");
                exit();
            } else {
                echo "<p>Error al ejecutar la consulta: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Error al preparar la consulta: " . $db->error . "</p>";
        }
    } else {
        echo "<p>Datos inválidos. Por favor, revisa tu entrada.</p>";
    }
}

include_once('includes/headUsers.php');
?>
    <link rel="stylesheet" href="css/menuCHD/menuCHD.css">
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/tables.css">
    <nav class="side-nav">
        <div class="logo-container">
            <a href="menuCHD.php" id="logo-hover"><img src="imagenes/LOGIXPRESS_LOGO_F2.png" alt="Logo"></a>
        </div>
        <ul>
            <li><a href="?section=entregasPendientes">Pending Deliveries</a></li>
            <li><a href="?section=historialEntregas">Delivery History</a></li>
            <li><a href="?section=mantenimiento">Maintenance Management</a></li>
        </ul>
        <!-- Botón de Logout -->
        <form action="" method="post">
            <button type="submit" name="accion" value="logout">Log out</button>
        </form>
    </nav>
    <div class="content-origin">
        <?php
        $section = $_GET['section'] ?? null;

        switch ($section) {
            case 'mantenimiento':
                ?> <link rel="stylesheet" href="css/menuCHD/vistaMantenimientoRecursos.css">
                    <script src="js/recargarPaginaParametroMantenimiento.js"></script>
                    <script src="js/filtrosMantenimientoVehiculos.js"></script>
                    <script src="js/mandarVehiculoMantenimiento.js"></script>
                <?php
                include_once('php/mantenimientoRecursos/vistaMantenimientoRecursos.php');
                break;
            case 'entregasPendientes':
                ?>  <link rel="stylesheet" href="css/menuCHD/vistaEntregasPendientes.css">
                    <link rel="stylesheet" href="css/menuCHD/modalInfoDelivery.css">
                    <script src="js/detailsDeliveryModal.js"></script>
                    <script src="js/formularioAsignarRecursos.js"></script>
                <?php
                include_once('php/pendingDeliveries/vistaEntregasPendientes.php');
                break;
            case 'historialEntregas':
                ?>
                <h2>Historial de Entregas Realizadas</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th>Entrega</th>
                        <th>Fecha</th>
                        <th>Empleado</th>
                        <th>Vehículo</th>
                        <th>Remolque</th>
                        <th>Estado</th>
                    </tr>
                    <?php
                    // Consulta ajustada para mostrar entregas completadas
                    $query = "
                        SELECT 
                            e.num AS entregaId,
                            e.fechaRegistro,
                            (SELECT em.nombre
                            FROM entre_empleado emp
                            INNER JOIN empleado em ON emp.empleado = em.num
                            WHERE emp.entrega = e.num) AS empleado,
                            (SELECT v.numSerie
                            FROM entre_vehi_remo ev
                            INNER JOIN vehiculo v ON ev.vehiculo = v.num
                            WHERE ev.entrega = e.num) AS vehiculo,
                            (SELECT r.numSerie
                            FROM entre_vehi_remo ev
                            INNER JOIN remolque r ON ev.remolque = r.num
                            WHERE ev.entrega = e.num) AS remolque,
                            (SELECT estado.descripcion
                            FROM entre_estado ee
                            INNER JOIN estado_entre estado ON ee.estadoEntrega = estado.codigo
                            WHERE ee.entrega = e.num AND ee.estadoEntrega = 'COMP') AS estado
                        FROM entrega e
                        WHERE EXISTS (
                            SELECT 1
                            FROM entre_estado ee
                            WHERE ee.entrega = e.num AND ee.estadoEntrega = 'COMP'
                        );

                    ";
            
                    $result = $db->query($query);
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['entregaId']}</td>";
                            echo "<td>{$row['fechaRegistro']}</td>";
                            echo "<td>{$row['empleado']}</td>";
                            echo "<td>{$row['vehiculo']}</td>";
                            echo "<td>" . ($row['remolque'] ?? 'Sin Asignar') . "</td>";
                            echo "<td>{$row['estado']}</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No hay entregas completadas.</td></tr>";
                    }
                    ?>
                </table>
                <?php
                break;
            default:
                ?> <link rel="stylesheet" href="css/menuCHD/vistaInicial.css"> <?php
                vistaInicial();
                break;
        }
        ?>
    </div>
</body>
</html>