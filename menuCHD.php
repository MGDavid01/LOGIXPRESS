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
        $entregaId = filter_input(INPUT_POST, 'entrega', FILTER_VALIDATE_INT);
        $empleadoId = filter_input(INPUT_POST, 'empleado', FILTER_VALIDATE_INT);
        $vehiculoId = filter_input(INPUT_POST, 'vehiculo', FILTER_VALIDATE_INT);
        $remolqueId = filter_input(INPUT_POST, 'remolque', FILTER_VALIDATE_INT);
        if (isset($remolqueId) || $remolqueId = "") {
            $remolqueId = 1;
        }
        // Mostrar mensajes de depuración para verificar los valores recibidos
        if ($entregaId === false || $entregaId === null) {
            echo "<p>Error: El valor de 'entrega' no es válido o no se recibió.</p>";
        }
        if ($empleadoId === false || $empleadoId === null) {
            echo "<p>Error: El valor de 'empleado' no es válido o no se recibió.</p>";
        }
        if ($vehiculoId === false || $vehiculoId === null) {
            echo "<p>Error: El valor de 'vehículo' no es válido o no se recibió.</p>";
        }

        // Validar campos obligatorios
        if ($entregaId && $empleadoId && $vehiculoId) {
            try {
                // Preparar la llamada al procedimiento almacenado
                $stmt = $db->prepare("CALL SP_asignarRecursosEntrega(?, ?, ?, ?, @mensaje)");
                if ($stmt) {
                    // Si no se seleccionó un remolque, asignar NULL (o en este caso valor por defecto 1)
                    
                    $stmt->bind_param("iiii", $entregaId, $empleadoId, $vehiculoId, $remolqueId);

                    if ($stmt->execute()) {
                        // Obtener el valor del mensaje de salida
                        $result = $db->query("SELECT @mensaje AS mensaje");
                        $row = $result->fetch_assoc();
                            echo "<p>" . htmlspecialchars($row['mensaje']) . "</p>";
                        if ($row['mensaje'] === "OK.") {
                            
                        } else {
                            echo "<p>Error inesperado al recuperar el mensaje de salida.</p>";
                        }
                    } else {
                        echo "<p>Error al ejecutar la petición: " . $stmt->error . "</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p>Error al preparar la petición: " . $db->error . "</p>";
                }
            } catch (Exception $e) {
                echo "<p>Error al asignar la entrega: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } else {
            echo "<p>Error: Datos inválidos. Por favor, revisa los campos obligatorios.</p>";
        }
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
            <li><a href="?section=vehiculosMantenimiento">Send to Maintenance</a></li>
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
            case 'vehiculosMantenimiento':
                ?> <link rel="stylesheet" href="css/menuCHD/vistaVehiculosMantenimiento.css"> <?php
                $editId = $_GET['edit'] ?? null;

                $query = "SELECT v.num, v.numSerie, v.gasXKM, v.capacidadCarga, v.kilometraje, v.costoAcumulado, 
                                 m.nombre as marca, mo.nombre as modelo, v.disponibilidad as disponibilidad_codigo, 
                                 d.descripcion as disponibilidad_texto
                          FROM vehiculo v
                          INNER JOIN marca m ON v.marca = m.codigo
                          INNER JOIN modelo mo ON v.modelo = mo.codigo
                          INNER JOIN disponibilidad d ON v.disponibilidad = d.codigo
                          ORDER BY v.num ASC";

                if ($db) {
                    $result = $db->query($query);
                    if ($result && $result->num_rows > 0) {
                        ?> <div class="table-size">
                        <h2>Vehículos para Mantenimiento</h2>
                        <table>
                            <tr>
                                <th>No.</th>
                                <th>Num Serie</th>
                                <th>Kilometraje</th>
                                <th>Costo</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Disponibilidad</th>
                                <th>Acciones</th>
                            </tr>
                            <?php while ($vehiculo = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($vehiculo['num']); ?></td>
                                    <td><?= htmlspecialchars($vehiculo['numSerie']); ?></td>
                                    <td><?= htmlspecialchars($vehiculo['kilometraje']); ?></td>
                                    <td><?= htmlspecialchars($vehiculo['costoAcumulado']); ?></td>
                                    <td><?= htmlspecialchars($vehiculo['marca']); ?></td>
                                    <td><?= htmlspecialchars($vehiculo['modelo']); ?></td>

                                    <?php if ($editId == $vehiculo['num']): ?>
                                        <?php
                                        // Ejecutar la consulta de las opciones de disponibilidad
                                        $disponibilidadQuery = "SELECT codigo, descripcion FROM disponibilidad";
                                        $disponibilidades = $db->query($disponibilidadQuery);
                                        ?>
                                        <td>
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($vehiculo['num']); ?>">
                                                <select name="disponibilidad">
                                                    <?php while ($opcion = $disponibilidades->fetch_assoc()): ?>
                                                        <?php $selected = $opcion['codigo'] === $vehiculo['disponibilidad_codigo'] ? 'selected' : ''; ?>
                                                        <option value="<?= htmlspecialchars($opcion['codigo']); ?>" <?= $selected; ?>>
                                                            <?= htmlspecialchars($opcion['descripcion']); ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                        </td>
                                        <td>
                                            <button type="submit" name="updateDisponibilidad">Guardar</button>
                                            </form>
                                        </td>
                                    <?php else: ?>
                                        <td><?= htmlspecialchars($vehiculo['disponibilidad_texto']); ?></td>
                                        <td>
                                            <a href="?section=vehiculosMantenimiento&edit=<?= htmlspecialchars($vehiculo['num']); ?>">Editar</a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    </div> <?php
                    } else {
                        echo "<p>No se encontraron vehículos.</p>";
                    }
                }
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