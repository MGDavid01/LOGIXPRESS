<link rel="stylesheet" href="css/menuCHD/vistaVehiculosMantenimiento.css">
<link rel="stylesheet" href="css/menuCHD/modalFormularioRegistroMantenimiento.css">
<?php
include_once('logicaObtenerFiltrosVehiculosMantenimientoVehiculos.php');
echo '<div id="vehicleCards" class="cards-container">';
while ($vehiculo = mysqli_fetch_assoc($resultVehiculos)) {
    ?>
    <div id="card-<?= htmlspecialchars($vehiculo['num']) ?>" class="card" 
    data-category="<?= htmlspecialchars($vehiculo['categoriaVehiculo']) ?>" data-brand="<?= htmlspecialchars($vehiculo['Marca']) ?>" 
    data-model="<?= htmlspecialchars($vehiculo['Modelo']) ?>" data-serial="<?= htmlspecialchars($vehiculo['numSerie']) ?>">
            <div class="content-img">
            <?php
                switch ($vehiculo['categoriaVehiculo']) {
                    case 'FURGG':
                        ?><img src="imagenes/furgoneta.png" alt="Vehículo"><?php
                        break;
                    case 'FURGR':
                        ?><img src="imagenes/furgonetaRefri.png" alt="Vehículo"><?php
                        break;
                    case 'CARTO':
                        ?><img src="imagenes/camionRigidoTolva.png" alt="Vehículo"><?php
                        break;
                    case 'CARCG':
                        ?><img src="imagenes/camionRigido.png" alt="Vehículo"><?php
                        break;
                    case 'CARCR':
                        ?><img src="imagenes/camionRigidoRefri.png" alt="Vehículo"><?php
                        break;
                    case 'CAMRP':
                        ?><img src="imagenes/camionRigidoPlata.png" alt="Vehículo"><?php
                        break;
                    case 'CAMAP':
                        ?><img src="imagenes/tractoCamion.png" alt="Vehículo"><?php
                        break;
                    default:
                        ?><img src="imagenes/vehiculo.png" alt="Vehículo Desconocido"><?php
                        break;
                }
                ?>
            </div>
        <div class="card-details">
            <h3>Serial Number: <?= htmlspecialchars($vehiculo['numSerie']); ?></h3>
            <p>Brand: <?= htmlspecialchars($vehiculo['Marca']); ?></p>
            <p>Model: <?= htmlspecialchars($vehiculo['Modelo']); ?></p>
            <?php
            if($herramienta == 'mandar'){
                ?><button onclick="enviarAMantenimiento('<?= $vehiculo['num'] ?>')" class="btn-send-maintenance">Send to Maintenance</button><?php
            } else {
                ?><button onclick="registrarMantenimiento('<?= $vehiculo['num'] ?>')" class="btn-register-maintenance">Register Maintenance</button><?php
            }
            ?>
        </div>
    </div>

    <!-- Modal para Registro de Mantenimiento -->
    <div id="modalMantenimiento" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h2>Registrar Mantenimiento</h2>
            <form id="formRegistroMantenimiento" method="POST" action="php/mantenimientoRecursos/logicaRegistrarMantenimiento.php">
                <input type="hidden" id="vehiculoId" name="vehiculoId">
                <div class="input-group">
                    <label for="costoMantenimiento">Costo:</label>
                    <input type="number" id="costoMantenimiento" name="costoMantenimiento" step="0.01" placeholder="Ingrese el costo del mantenimiento" required>
                </div>
                <div class="input-group">
                    <label for="descripcionMantenimiento">Descripción:</label>
                    <textarea id="descripcionMantenimiento" name="descripcionMantenimiento" rows="4" placeholder="Describa el trabajo de mantenimiento realizado" required></textarea>
                </div>
                <button type="submit" class="btn-guardar">Guardar Registro</button>
            </form>
        </div>
    </div>
    <?php
}
echo '</div>';