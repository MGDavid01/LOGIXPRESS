<?php 
function vistaPedirEntrega() {
    global $db;
    /* Verificar productos y ubicaciones */
    $verificacion = verificarProductosUbicaciones(true);
    if($verificacion != 5) {
        echo verificarProductosUbicaciones(false);
    } else {
        $cliente_id = $_SESSION['user_id'];
        // Consultas para obtener datos de productos, ubicaciones, tipos de carga y prioridades
        $queryProductosTotal = "SELECT COUNT(num) AS total FROM producto WHERE cliente = $cliente_id";
        $resultProductos = mysqli_query($db, $queryProductosTotal);
        $totalProductos = ($resultProductos) ? mysqli_fetch_assoc($resultProductos)['total'] : 0;
        
        $queryUbicaciones = "SELECT cu.ubicacion, u.nombreUbicacion FROM cliente_ubi cu
        INNER JOIN ubicacion u ON u.num = cu.ubicacion WHERE cu.cliente = $cliente_id";
        $resultUbicaciones = mysqli_query($db, $queryUbicaciones);

        $queryTipo = "SELECT codigo, descripcion FROM tipo_carga WHERE codigo != 'UNV'";
        $resultTipo = mysqli_query($db, $queryTipo);

        $queryPrio = "SELECT codigo, descripcion FROM prioridad ORDER BY 
                        CASE 
                            WHEN descripcion = 'Baja' THEN 1
                            WHEN descripcion = 'Media' THEN 2
                            WHEN descripcion = 'Alta' THEN 3
                            WHEN descripcion = 'Urgente' THEN 4
                        END ASC;";
        $resultPrioridad = mysqli_query($db, $queryPrio);

        $queryProd = "SELECT num, nombre FROM producto WHERE cliente = $cliente_id";
        $resultProducto = mysqli_query($db, $queryProd);
    ?>
        <form action="" method="post" id="form-delivery">
            <div class="form-content">
                <div class="form step step-1">
                    <div class="form-field">
                        <label for="fechaEntrega">Delivery Date:</label>
                        <input type="date" id="fechaEntrega" name="fechaEntrega" required>
                    </div>
                    <div class="form-field">
                        <label for="horaInicio">Start Time:</label>
                        <input type="time" id="horaInicio" name="horaInicio" required>
                    </div>
                    <div class="form-field">
                        <label for="horaFin">End Time:</label>
                        <input type="time" id="horaFin" name="horaFin" required>
                    </div>
                    <div class="form-field">
                        <label for="prioridad">Delivery Priority:</label>
                        <select id="prioridad" name="prioridad" required>
                            <?php while ($rowPrio = $resultPrioridad->fetch_assoc()) {
                                echo "<option value='" . $rowPrio['codigo'] . "'>" . $rowPrio['descripcion'] . "</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="form-field">
                            <label for="instrucciones">Instructions:</label>
                            <textarea id="instrucciones" name="instrucciones" rows="4" placeholder="Enter any special instructions here..."></textarea>
                        </div>
                    <div class="step-buttons">
                        <button type="button" class="btn-next" onclick="nextStep(2)">Next</button>
                    </div>
                </div>

                <!-- Paso 2: Productos a entregar -->
                <div class="form step step-2">
                    <div class="form-section">
                        <h3>Step 2: Products to Deliver</h3>
                        <div id="producto-container">
                            <div class="producto-field">
                                <div class="form-field">
                                    <label for="producto1">Product:</label>
                                    <select name="producto[]" required>
                                        <?php $resultProducto->data_seek(0);
                                        while ($rowPro = $resultProducto->fetch_assoc()) {
                                            echo "<option value='" . $rowPro['num'] . "'>" . $rowPro['nombre'] . "</option>";
                                        } ?>
                                    </select>
                                </div>
                                <div class="form-field form-field-inline">
                                    <div class="form-element">
                                        <label for="cantidad1">Amount:</label>
                                        <input type="number" name="cantidad[]" id="cantidad1" required>
                                    </div>
                                    <div class="form-element">
                                        <label for="tipoCarga1">Load Type:</label>
                                        <select name="tipoCarga[]" required>
                                            <?php $resultTipo->data_seek(0);
                                            while ($rowTipo = $resultTipo->fetch_assoc()) {
                                                echo "<option value='" . $rowTipo['codigo'] . "'>" . $rowTipo['descripcion'] . "</option>";
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="buttons-add-delete">
                            <button type="button" class="btn-agregar" onclick="agregarProducto()">Add Product</button>
                            <button type="button" class="btn-eliminar" onclick="eliminarUltimoProducto()">Remove Last Product</button>
                        </div>
                    </div>
                    <div class="step-buttons">
                        <button type="button" class="btn-previous" onclick="previousStep(1)">Previous</button>
                        <button type="button" class="btn-next" onclick="nextStep(3)">Next</button>
                    </div>
                </div>

                <!-- Paso 3: Ubicaciones de Entrega -->
                <div class="form step step-3">
                    <div class="form-section">
                        <h3>Step 3: Delivery Locations</h3>
                        <div id="locations-container">
                            <div class="form-field">
                                <label for="originLocation">Origin Location:</label>
                                <select id="originLocation" name="originLocation" required>
                                    <?php $resultUbicaciones->data_seek(0);
                                    while ($rowLoc = $resultUbicaciones->fetch_assoc()) {
                                        echo "<option value='" . $rowLoc['ubicacion'] . "'>" . $rowLoc['nombreUbicacion'] . "</option>";
                                    } ?>
                                </select>
                            </div>
                            <div class="form-field">
                                <label for="desLocation">Destination Location:</label>
                                <select id="desLocation" name="desLocation[]" required>
                                    <?php $resultUbicaciones->data_seek(0);
                                    while ($rowLoc = $resultUbicaciones->fetch_assoc()) {
                                        echo "<option value='" . $rowLoc['ubicacion'] . "'>" . $rowLoc['nombreUbicacion'] . "</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="buttons-add-delete">
                            <button type="button" class="btn-agregar" onclick="agregarUbicacion()">Add Destination</button>
                            <button type="button" class="btn-eliminar" onclick="eliminarUltimaUbicacion()">Remove Last Destination</button>
                        </div>
                    </div>
                    <div class="step-buttons">
                        <button type="button" class="btn-previous" onclick="previousStep(2)">Previous</button>
                        <button type="button" class="btn-next" onclick="nextStep(4)">Next</button>
                    </div>
                </div>

                <!-- Paso 4: Resumen y Confirmación -->
                <div class="form step step-4">
                    <div class="form-section">
                        <!-- Aquí se podría añadir un resumen dinámico de los datos ingresados -->
                    </div>
                    <div class="step-buttons">
                        <button type="button" class="btn-previous" onclick="previousStep(3)">Previous</button>
                        <button type="submit" name="accion" value="registerDelivery" class="btn-guardar">Confirm Delivery Order</button>
                    </div>
                </div>
            </div>
        </form>
    <?php
    }
}
?>
