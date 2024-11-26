<?php
function vistaUbicaciones($cliente) {
    global $db;

    $ubicacion = [];
    $queryUbicacion = "SELECT cu.ubicacion, u.nombreUbicacion, cu.fechaRegistro
            FROM ubicacion u
            INNER JOIN cliente_ubi cu ON cu.ubicacion = u.num
            WHERE cu.cliente = $cliente";
    
    $result = mysqli_query($db, $queryUbicacion);
    if (!$result) {
        die("Error en la consulta: " . mysqli_error($db));
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $ubicacion[] = $row;
    }

    // Mostrar los datos en una tabla HTML
    if (!empty($ubicacion)): ?>
    <section class="content-tools">
        <div class="tools">
            <div>
                <a class="tool-text" href="?section=locations&addLocation">Add Location</a>
            </div>
            <div>
                <a class="tool-text" href="?section=locations&editLocation">Edit Location</a>
            </div>
        </div>
        <div class="information">
            <div class="general-info">
                <h2>Location List</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($ubicacion as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ubicacion']) ?></td>
                            <td><?= htmlspecialchars($row['nombreUbicacion']) ?></td>
                            <td><a href="?section=locations&location=<?= urlencode($row['ubicacion']) ?>">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="info-locations">
                <?php if (isset($_GET['section'], $_GET['location']) && $_GET['section'] === "locations"): ?>
                    <?php vistaFormularoProductos($_GET['location']); ?>
                <?php else: ?>
                    <p style="font-size:2rem;">Select a location to edit.</p>
                <?php endif; ?> 
            </div>
        </div>
    </section>
    <?php endif;
    
}    

function vistaFormularoProductos($ubicacion_id) {
    global $db;

    // Consulta los detalles de la ubicación
    $queryUbicacion = "SELECT u.num, u.nombreUbicacion, u.nombreCalle, u.numCalle, u.colonia, u.codigoPostal
        FROM ubicacion u
        INNER JOIN cliente_ubi cu ON cu.ubicacion = u.num
        WHERE cu.ubicacion = '$ubicacion_id'";

    $resultUbicacion = mysqli_query($db, $queryUbicacion);
    $detalle = mysqli_fetch_assoc($resultUbicacion);

    if (!$detalle) {
        echo '<p>Error: No se encontró la ubicación.</p>';
        return;
    }
    // Mostrar el formulario
    ?>
    <div class="form">
    <?php
        if (isset($_GET['status']) && $_GET['status'] === 'updateLocation'): ?>
        <p style="font-size:2rem; text-align: end; color: #57cf8b;">Location Updated</p>
        <?php endif; ?>
        <h2>Edit Location</h2>
        <form action="" method="POST">
            <!-- Campo oculto para el ID de la ubicación -->
            <input type="hidden" name="ubicacion_id" value="<?= htmlspecialchars($detalle['num']) ?>">

            <!-- Campo: Nombre de la Ubicación -->
            <div class="form-group">
                <label for="nombreUbicacion">Location Name:</label>
                <input type="text" id="nombreUbicacion" name="nombreUbicacion" value="<?= htmlspecialchars($detalle['nombreUbicacion']) ?>" required>
            </div>

            <!-- Campo: Dirección -->
            <div class="form-group">
                <label for="nombreCalle">Street:</label>
                <input type="text" id="nombreCalle" name="nombreCalle" value="<?= htmlspecialchars($detalle['nombreCalle']) ?>" required>
            </div>

            <!-- Campo: Número de Calle -->
            <div class="form-group">
                <label for="numCalle">Street Number:</label>
                <input type="text" id="numCalle" name="numCalle" value="<?= htmlspecialchars($detalle['numCalle']) ?>" required>
            </div>

            <!-- Campo: Colonia -->
            <div class="form-group">
                <label for="colonia">Settlement:</label>
                <input type="text" id="colonia" name="colonia" value="<?= htmlspecialchars($detalle['colonia']) ?>" required>
            </div>

            <!-- Campo: Código Postal -->
            <div class="form-group">
                <label for="codigoPostal">Zip code:</label>
                <input type="text" id="codigoPostal" name="codigoPostal" value="<?= htmlspecialchars($detalle['codigoPostal']) ?>" required>
            </div>

            <!-- Botón: Guardar Cambios -->
            <button type="submit" name="accion" value="updateLocation" class="btn-guardar">Update</button>
        </form>
    </div>

    <?php
}
?>
