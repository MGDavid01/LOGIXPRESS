<?php
    $mantenimientoTool = filter_input(INPUT_GET, 'mantenimiento');
    switch ($mantenimientoTool) {
        case 'vehiculos':
            $herramienta = filter_input(INPUT_GET, 'herramienta');
            switch ($herramienta) {
                case 'mandar':
                    include_once('herramientaUniversalMantenimiento.php');
                    break;
                case 'registrar':
                    include_once('herramientaUniversalMantenimiento.php');
                    break;
                default:
                    ?>
                    <div class="title-mainte">
                        <h1>Vehicle<br>Maintenance</h1>
                    </div>
                    <div class="content-card">
                        <button id="vehiculos" onclick="mostrarRecurso('mandar')">
                            <div class="card">
                                <div class="content-img">
                                    <img src="imagenes/mandar.png" alt="Mandar">
                                </div>
                                <h2>Send to Maintenance</h2>
                            </div>
                        </button>
                        <button id="remolques" onclick="mostrarRecurso('registrar')">
                            <div class="card">
                                <div class="content-img">
                                    <img src="imagenes/registrar.png" alt="Registrar">
                                </div>
                                <h2>Register Maintenance</h2>
                            </div>
                        </button>
                    </div>
                    <button onclick="removerMantenimiento()" class="btn-back">Go Back</button>
                    <?php
                    break;
            }
            
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
                <h1>Maintenance</h1>
            </div>
            <div class="content-card">
                <button id="vehiculos" onclick="elegirRecurso('vehiculos')">
                    <div class="card">
                        <div class="content-img">
                            <img src="imagenes/vehiculo.png" alt="Vehículo">
                        </div>
                        <h2>Vehículos</h2>
                    </div>
                </button>
                <button id="remolques" onclick="elegirRecurso('remolques')">
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