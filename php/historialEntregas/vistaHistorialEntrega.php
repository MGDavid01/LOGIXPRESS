<?php
// Consulta para obtener el historial de entregas
$query = "SELECT e.num, e.fechaEntrega, ee.descripcion, c.nomEmpresa
          FROM entrega e
          INNER JOIN cliente c ON e.num = c.num
          INNER JOIN entre_estado enes ON e.num = enes.entrega
          INNER JOIN estado_entre ee ON enes.estadoEntrega = ee.codigo
          WHERE enes.fechaCambio = (
              SELECT MAX(fechaCambio)
              FROM entre_estado
              WHERE entrega = e.num
          )
          ORDER BY e.fechaEntrega DESC";

$resultCategorias = mysqli_query($db, $query);
if (!$resultCategorias) {
    die("Error en la consulta: " . mysqli_error($db));
}

$entregas = [];
while ($row = mysqli_fetch_assoc($resultCategorias)) {
    $entregas[] = $row;
}
?>
    <script>
        function sortTable(columnIndex) {
            const table = document.querySelector(".sortable-table tbody");
            const rows = Array.from(table.rows);

            const sortedRows = rows.sort((a, b) => {
                const aText = a.cells[columnIndex].innerText;
                const bText = b.cells[columnIndex].innerText;
                return aText.localeCompare(bText);
            });

            table.innerHTML = "";
            sortedRows.forEach(row => table.appendChild(row));
        }

        function filterTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toLowerCase();
            const table = document.querySelector(".sortable-table tbody");
            const rows = Array.from(table.rows);

            rows.forEach(row => {
                const fechaEntrega = row.cells[1].innerText.toLowerCase();
                const cliente = row.cells[3].innerText.toLowerCase();
                
                const match =  fechaEntrega.includes(filter) || cliente.includes(filter);

                row.style.display = match ? "" : "none";
            })
        }
        function openModal(entregaNum) {
            const modal = document.getElementById("detalleModal");
            const modalContent = document.getElementById("modalContent");

            // Hacer una solicitud AJAX para obtener los detalles de la entrega
            fetch(`php/historialEntregas/modalDetalleEntregaHistorial.php?num=${entregaNum}`)
                .then(response => response.text())
                .then(data => {
                    modalContent.innerHTML = data;
                    modal.style.display = "flex";
                })
                .catch(error => console.error('Error al cargar los detalles:', error));
        }

        function closeModal() {
            const modal = document.getElementById("detalleModal");
            modal.style.display = "none";
        }

        // Cerrar el modal al hacer clic fuera del contenido
        window.onclick = function(event) {
            const modal = document.getElementById("detalleModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
    <div class="buscador-historial">
        <h2>Historial de Entregas</h2>
        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Buscar por fecha de entrega o cliente">
    </div>
    <?php if (!empty($entregas)): ?>
        <table class="sortable-table">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Entrega</th>
                    <th onclick="sortTable(1)">Fecha de Entrega</th>
                    <th onclick="sortTable(2)">Estado</th>
                    <th onclick="sortTable(3)">Cliente</th>
                    <th class="no-pointer">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entregas as $entrega): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($entrega['num']); ?></td>
                        <td><?php echo htmlspecialchars($entrega['fechaEntrega']); ?></td>
                        <td><?php echo htmlspecialchars($entrega['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($entrega['nomEmpresa']); ?></td>
                        <td><button class="btn" onclick="openModal(<?php echo $entrega['num']; ?>)">Ver Detalles</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay entregas disponibles para mostrar.</p>
    <?php endif; ?>
    <!-- Modal para mostrar los detalles de la entrega -->
    <div id="detalleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalContent">Cargando detalles...</div>
        </div>
    </div>