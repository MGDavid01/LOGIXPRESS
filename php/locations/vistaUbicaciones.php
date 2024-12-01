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
    ?>
    <section class="content-tools">
        <div class="tools">
            <div>
                <a class="tool-text" href="?section=locations&tool=add">Add Location</a>
            </div>
            <div>
                <a class="tool-text" href="?section=locations&tool=edit">Edit Location</a>
            </div>
        </div>
        <div class="information">
            <div class="general-info">
                <h2>Location List</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <?php if (isset($_GET['tool']) && $_GET['tool'] === 'edit'): ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                    <?php foreach ($ubicacion as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ubicacion']) ?></td>
                            <td><?= htmlspecialchars($row['nombreUbicacion']) ?></td>
                            <?php if (isset($_GET['tool']) && $_GET['tool'] === 'edit'): ?>
                                <td><a class="btn-ora" style='font-size:1.2rem;' href="?section=locations&tool=edit&location=<?= $row['ubicacion'] ?>">Edit</a></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="info-locations">
                <?php if (isset($_GET['tool'])): ?>
                    <?php if ($_GET['tool'] === 'edit' && isset($_GET['location'])): ?>
                        <?php vistaFormularoProductos($_GET['location']); ?>
                    <?php elseif ($_GET['tool'] === 'add'): ?>
                        <?php vistaFormularioAgregarUbicacion(); ?>
                    <?php else: ?>
                        <p style="font-size:2rem;">Select a location to edit or add.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p style="font-size:2rem;">Select a location to edit or add.</p>
                <?php endif; ?> 
            </div>
        </div>
    </section>
    <?php
}    

// Función para mostrar el formulario de editar ubicación existente.
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

    // Mostrar el formulario para editar la ubicación
    ?>
    <div class="form">
    <?php if (isset($_GET['status']) && $_GET['status'] === 'updatedLocation'): ?>
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

// Función para mostrar el formulario de agregar una nueva ubicación
function vistaFormularioAgregarUbicacion() {
    ?>
    <div class="form">
    <?php if (isset($_GET['status']) && $_GET['status'] === 'addedLocation'): ?>
        <p style="font-size:2rem; text-align: end; color: #57cf8b;">Location Added</p>
    <?php endif; ?>
        <h2>Add New Location</h2>
        <form action="" method="POST">
            <!-- Campo: Nombre de la Ubicación -->
            <div class="form-group">
                <label for="nombreUbicacionI">Location Name:</label>
                <input type="text" id="nombreUbicacionI" name="nombreUbicacionI" required>
            </div>

            <!-- Campo: Dirección -->
            <div class="form-group">
                <label for="nombreCalleI">Street:</label>
                <input type="text" id="nombreCalleI" name="nombreCalleI" required>
            </div>

            <!-- Campo: Número de Calle -->
            <div class="form-group">
                <label for="numCalleI">Street Number:</label>
                <input type="text" id="numCalleI" name="numCalleI" required>
            </div>

            <!-- Campo: Colonia -->
            <div class="form-group">
                <label for="coloniaI">Settlement:</label>
                <input type="text" id="coloniaI" name="coloniaI" required>
            </div>

            <!-- Campo: Código Postal -->
            <div class="form-group">
                <label for="codigoPostalI">Zip code:</label>
                <input type="text" id="codigoPostalI" name="codigoPostalI" required>
            </div>

            <!-- Botón: Añadir Ubicación -->
            <button type="submit" name="accion" value="addLocation" class="btn-guardar">Add Location</button>
        </form>
    </div>
    <?php
}
?>
