<?php
global $db;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Manejo de diferentes acciones: asignar y guardar asignación
    if (isset($_POST['asignarEntrega'])) {
        $entregaId = filter_input(INPUT_POST, 'entrega', FILTER_VALIDATE_INT);

        if ($entregaId) {
            // Obtener información de la entrega seleccionada
            $queryEntrega = "
                SELECT e.pesoTotal, e.volumenTotal, e.tipoCarga
                FROM entrega e
                WHERE e.num = ?";
            $stmtEntrega = $db->prepare($queryEntrega);
            if ($stmtEntrega) {
                $stmtEntrega->bind_param('i', $entregaId);
                $stmtEntrega->execute();
                $resultEntrega = $stmtEntrega->get_result();
                $entrega = $resultEntrega->fetch_assoc();
            } else {
                echo "<p>Error al preparar la consulta para obtener la entrega.</p>";
            }
        } else {
            echo "<p>Error: ID de entrega inválido.</p>";
        }
    } elseif (isset($_POST['guardarAsignacion'])) {
        $entregaId = filter_input(INPUT_POST, 'entrega', FILTER_VALIDATE_INT);
        $empleadoId = filter_input(INPUT_POST, 'empleado', FILTER_VALIDATE_INT);
        $vehiculoId = filter_input(INPUT_POST, 'vehiculo', FILTER_VALIDATE_INT);
        $remolqueId = filter_input(INPUT_POST, 'remolque', FILTER_VALIDATE_INT);

        // Validar campos obligatorios
        if ($entregaId && $empleadoId && $vehiculoId) {
            try {
                // Iniciar transacción
                $db->begin_transaction();

                // Asignar empleado a la entrega
                $query1 = "INSERT INTO entre_empleado (entrega, empleado) VALUES (?, ?)";
                $stmt1 = $db->prepare($query1);
                $stmt1->bind_param('ii', $entregaId, $empleadoId);
                $stmt1->execute();

                // Asignar vehículo a la entrega
                $query2 = "INSERT INTO entre_vehi_remo (entrega, vehiculo) VALUES (?, ?)";
                $stmt2 = $db->prepare($query2);
                $stmt2->bind_param('ii', $entregaId, $vehiculoId);
                $stmt2->execute();

                // Si se seleccionó un remolque, asignarlo al vehículo
                if ($remolqueId) {
                    $query3 = "UPDATE entre_vehi_remo SET remolque = ? WHERE entrega = ? AND vehiculo = ?";
                    $stmt3 = $db->prepare($query3);
                    $stmt3->bind_param('iii', $remolqueId, $entregaId, $vehiculoId);
                    $stmt3->execute();
                }

                // Confirmar la transacción
                $db->commit();
                echo "<p>Asignación realizada correctamente.</p>";
            } catch (Exception $e) {
                $db->rollback();
                echo "<p>Error al guardar la asignación: {$e->getMessage()}</p>";
            }
        } else {
            echo "<p>Error: Datos inválidos. Por favor, revisa los campos obligatorios.</p>";
        }
    }
}
?>

<link rel="stylesheet" href="css/menuCHD/vistaAsignarEntregas.css">
<link rel="stylesheet" href="css/menuCHD/detailsDeliveryModal.css">
<script>
// Obtener el modal y el botón de cierre
const modal = document.getElementById('modalDetallesEntrega');
const span = document.getElementsByClassName('close')[0];

// Cuando el usuario hace clic en <span> (x), cierra el modal
span.onclick = function() {
    modal.style.display = 'none';
}

// Cuando el usuario hace clic en cualquier parte fuera del modal, también lo cierra
window.onclick = function(event) {
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

// Cuando el usuario hace clic en el botón "Ver detalles de la entrega"
function mostrarDetallesEntrega(entregaId) {
    // Llamada AJAX para obtener los detalles de la entrega
    fetch(`php/asignDelivery/detailsDeliveryModal.php?entregaId=${entregaId}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            document.getElementById('entregaIdModal').textContent = entregaId;
            document.getElementById('detallesContenido').innerHTML = `
                <p>Fecha de Registro: ${data.fechaRegistro}</p>
                <p>Peso Total: ${data.pesoTotal} kg</p>
                <p>Volumen Total: ${data.volumenTotal} m³</p>
                <p>Tipo de Carga: ${data.tipoCarga}</p>
            `;
        } else {
            document.getElementById('detallesContenido').innerHTML = `<p>${data.message}</p>`;
        }
        // Mostrar el modal
        modal.style.display = 'block';
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('detallesContenido').innerHTML = `<p>Error al obtener los detalles de la entrega: ${error.message}</p>`;
        modal.style.display = 'block';
    });
}
</script>

<div class="tools">
    <!-- Contenedor Tabla -->
    <div class="table-size">
        <h2>Asignar Empleados, Vehículos y Remolques a Entregas</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <th>Entrega ID</th>
                <th>Fecha</th>
                <th>Tipo de Carga</th>
                <th>Acciones</th>
            </tr>
            <?php
            // Consulta para seleccionar entregas sin empleado, vehículo y remolque asignados
            $query = "
                SELECT e.num AS entregaId, e.fechaRegistro, e.pesoTotal, e.volumenTotal, tc.descripcion AS tipoCarga
                FROM entrega e
                INNER JOIN tipo_carga tc ON e.tipoCarga = tc.codigo
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM entre_empleado emp
                    WHERE emp.entrega = e.num
                )
                OR NOT EXISTS (
                    SELECT 1
                    FROM entre_vehi_remo ev
                    WHERE ev.entrega = e.num AND ev.remolque IS NOT NULL
                )";
            $result = $db->query($query);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['entregaId']}</td>";
                    echo "<td>{$row['fechaRegistro']}</td>";
                    echo "<td>{$row['tipoCarga']}</td>";
                    echo "<td>
                        <button type='button' onclick='mostrarDetallesEntrega({$row['entregaId']})'>Ver detalles de la entrega</button>
                        <form method='POST' action=''>
                            <input type='hidden' name='entrega' value='{$row['entregaId']}'>
                            <button type='submit' name='asignarEntrega'>Asignar</button>
                        </form>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No hay entregas pendientes sin asignar.</td></tr>";
            }
            ?>
        </table>
    </div>

    <!-- Contenedor Modal -->
    <div id="modalDetallesEntrega" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Detalles de la Entrega <span id="entregaIdModal"></span></h2>
            <div id="detallesContenido">
                <!-- Aquí se cargarán los detalles desde la petición AJAX -->
            </div>
        </div>
    </div>
</div>