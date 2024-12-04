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
                        <h1>Vehicles Maintenance Management</h1>
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
                        <h1>Trailers Maintenance Management</h1>
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
                        <h2>Vehicles</h2>
                    </div>
                </button>
                <button id="remolques" onclick="elegirRecurso('remolques')">
                    <div class="card">
                        <div class="content-img">
                            <img src="imagenes/remolque.png" alt="Remolque">
                        </div>
                        <h2>Trailers</h2>
                    </div>
                </button>
            </div>
            <?php
            break;
    }
?>