<?php
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
?>