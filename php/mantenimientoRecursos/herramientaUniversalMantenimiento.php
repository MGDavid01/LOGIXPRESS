<link rel="stylesheet" href="css/menuCHD/vistaVehiculosMantenimiento.css">
<link rel="stylesheet" href="css/menuCHD/modalFormularioRegistroMantenimiento.css">
<?php
include_once('logicaObtenerFiltrosVehiculosMantenimientoVehiculos.php');
echo '<div id="vehicleCards" class="cards-container">';
// Mostrar los datos, ya sea vehículos o remolques
if ($mantenimientoTool == 'vehiculos') {
    $resultItems = $resultVehiculos; // Resultado de vehículos
} else {
    $resultItems = $resultoRemolques; // Resultado de remolques
}
while ($item = mysqli_fetch_assoc($resultItems)) {
    ?>
     <div id="card-<?= htmlspecialchars($item['num']) ?>" class="card" 
        data-category="<?= htmlspecialchars(($mantenimientoTool == 'vehiculos') ? $item['categoriaVehiculo'] : $item['tipoRemolque']) ?>" 
        data-brand="<?= htmlspecialchars($item['Marca']) ?>" 
        data-model="<?= htmlspecialchars($item['Modelo']) ?>" 
        data-serial="<?= htmlspecialchars($item['numSerie']) ?>">
        <div class="content-img">
            <?php
            if ($mantenimientoTool == 'vehiculos') {
                // Iconos específicos para los vehículos según la categoría
                switch ($item['categoriaVehiculo']) {
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
            } else {
                // Iconos específicos para remolques según el tipo
                switch ($item['tipoRemolque']) {
                    case 'FURGG':
                        ?><img src="imagenes/remolqueFurgoneta.png" alt="Remolque"><?php
                        break;
                    case 'FURGR':
                        ?><img src="imagenes/remolqueFurgonetaRefri.png" alt="Remolque"><?php
                        break;
                    case 'CARTO':
                        ?><img src="imagenes/remolqueTolva.png" alt="Remolque"><?php
                        break;
                    case 'CARCG':
                        ?><img src="imagenes/remolqueCajaCerrada.png" alt="Remolque"><?php
                        break;
                    case 'CARCR':
                        ?><img src="imagenes/remolqueCajaCerradaRefri.png" alt="Remolque"><?php
                        break;
                    case 'CAMRP':
                        ?><img src="imagenes/remolquePlataforma.png" alt="Remolque"><?php
                        break;
                    case 'CAMAP':
                        ?><img src="imagenes/remolqueTracto.png" alt="Remolque"><?php
                        break;
                    default:
                        ?><img src="imagenes/remolque.png" alt="Remolque Desconocido"><?php
                        break;
                }
            }
            ?>
        </div>
        <div class="card-details">
            <h3>Serial Number: <?= htmlspecialchars($item['numSerie']); ?></h3>
            <p>Brand: <?= htmlspecialchars($item['Marca']); ?></p>
            <p>Model: <?= htmlspecialchars($item['Modelo']); ?></p>
            <?php
                // Obtener el tipo de recurso para la lógica del botón
                $tipoRecurso = ($mantenimientoTool == 'vehiculos') ? 'vehiculo' : 'remolque';

                if ($herramienta == 'mandar') {
                    // Enviar el tipo de recurso junto con el ID
                    ?>
                    <button onclick="enviarAMantenimiento('<?= $item['num'] ?>', '<?= $tipoRecurso ?>')" class="btn-send-maintenance">
                        Send to Maintenance
                    </button>
                    <?php
                } else {
                    // Botón para registrar mantenimiento
                    ?>
                    <button onclick="registrarMantenimiento('<?= $item['num'] ?>', '<?= $tipoRecurso ?>')" class="btn-register-maintenance">
                        Register Maintenance
                    </button>
                    <?php
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
                <input type="hidden" id="recursoId" name="recursoId">
                <input type="hidden" id="tipoRecurso" name="tipoRecurso">
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