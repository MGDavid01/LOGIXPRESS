<?php
    $mantenimientoTool = filter_input(INPUT_GET, 'mantenimiento');
    switch ($mantenimientoTool) {
        case 'vehiculos':
            ?>
            <link rel="stylesheet" href="css/menuCHD/vistaVehiculosMantenimiento.css">
            <?php
            include_once('logicaObtenerFiltrosVehiculosMantenimientoVehiculos.php');
            echo '<div id="vehicleCards" class="cards-container">';
            while ($vehiculo = mysqli_fetch_assoc($resultVehiculos)) {
                ?>
                <div id="card-<?= htmlspecialchars($vehiculo['num']) ?>" class="card" data-category="<?= htmlspecialchars($vehiculo['categoriaVehiculo']) ?>" data-brand="<?= htmlspecialchars($vehiculo['Marca']) ?>" data-model="<?= htmlspecialchars($vehiculo['Modelo']) ?>" data-serial="<?= htmlspecialchars($vehiculo['numSerie']) ?>">
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
                        <button onclick="enviarAMantenimiento('<?= $vehiculo['num'] ?>')" class="btn-send-maintenance">Enviar a Mantenimiento</button>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
            break;

        case 'remolques':
            // Obtener los remolques desde la base de datos
            $queryRemolques = "SELECT * FROM remolque WHERE disponibilidad = 'DISPO'";
            $resultRemolques = mysqli_query($db, $queryRemolques);
            
            echo '<button onclick="removeParam()" class="btn-back">Regresar</button>';
            echo '<h1>Remolques Disponibles para Mantenimiento</h1>';
            echo '<div class="cards-container">';
            
            while ($remolque = mysqli_fetch_assoc($resultRemolques)) {
                ?>
                <div class="card">
                    <div class="content-img">
                        <img src="imagenes/remolque.png" alt="Remolque">
                    </div>
                    <div class="card-details">
                        <h3>Serial Number: <?= htmlspecialchars($remolque['numSerie']); ?></h3>
                        <p>Capacidad de Carga: <?= htmlspecialchars($remolque['capacidadCarga']); ?> kg</p>
                        <button onclick="enviarMantenimiento('<?= $remolque['num'] ?>')" class="btn-send-maintenance">Enviar a Mantenimiento</button>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
            break;

        default:
            ?>
            <div class="title-mainte">
                <h1>Send to Maintenance</h1>
            </div>
            <div class="content-card">
                <button id="vehiculos" onclick="mostrarRecurso('vehiculos')">
                    <div class="card">
                        <div class="content-img">
                            <img src="imagenes/vehiculo.png" alt="Vehículo">
                        </div>
                        <h2>Vehículos</h2>
                    </div>
                </button>
                <button id="remolques" onclick="mostrarRecurso('remolques')">
                    <div class="card">
                        <div class="content-img">
                            <img src="imagenes/remolque.png" alt="Remolque">
                        </div>
                        <h2>Remolques</h2>
                    </div>
                </button>
            </div>
            <?php
            break;
    }
?>