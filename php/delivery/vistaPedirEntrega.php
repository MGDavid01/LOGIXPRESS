<?php 
function vistaPedirEntrega() {
    global $db;
    //Prueba pull
    /*Obtener verificacion*/
    $verificacion = verificarProductosUbicaciones(true);
    if($verificacion != 5) {
        // Verificar productos y ubicaciones
            $nivelVerificadoProdUbica ='';
            $nivelVerificadoProdUbica = verificarProductosUbicaciones(false);

            echo $nivelVerificadoProdUbica;
    }  else {

        $cliente_id = $_SESSION['user_id'];
        $queryProductosTotal = "SELECT COUNT(num) AS total FROM producto WHERE cliente = $cliente_id";
        $resultProductos = mysqli_query($db, $queryProductosTotal);
        $totalProductos = 0;
        if ($resultProductos) {
            $row = mysqli_fetch_assoc($resultProductos);
            $totalProductos = $row['total']; // Cantidad total de productos asociados
        }
        
        $queryUbicaciones = "SELECT cu.ubicacion, u.nombreUbicacion FROM cliente_ubi cu
        INNER JOIN ubicacion u ON u.num = cu.ubicacion WHERE cu.cliente = $cliente_id";
        $resultUbicaciones = mysqli_query($db, $queryUbicaciones);

        // Obtener los datos registrados de tipo_carga
        $queryTipo = "SELECT codigo, descripcion
                        FROM tipo_carga";
        $resultTipo = mysqli_query($db, $queryTipo);

        $queryPrio = "SELECT codigo, descripcion
                        FROM prioridad ORDER BY 
                        CASE 
                            WHEN descripcion = 'Baja' THEN 1
                            WHEN descripcion = 'Media' THEN 2
                            WHEN descripcion = 'Alta' THEN 3
                            WHEN descripcion = 'Urgente' THEN 4
                        END ASC;";
        $resultPrioridad = mysqli_query($db, $queryPrio);

        $cliente_id = $_SESSION['user_id'];
        $queryProd = "SELECT num, nombre FROM producto WHERE cliente = $cliente_id";
        $resultProducto = mysqli_query($db, $queryProd);
    ?>
        
        <form action="" method="post" id="form-delivery">
            <div class="form">
                <!-- Formulario de Entrega -->
                <div class="formulario">
                    <h3>Delivery form</h3>
                        <div>
                            <label for="fechaEntrega">Delivery Date:</label>
                            <input type="date" id="fechaEntrega" name="fechaEntrega" required><br><br>
                        </div>
                        <div>
                            <label for="horaInicio">Start Time:</label>
                            <input type="time" id="horaInicio" name="horaInicio" required><br><br>
                        </div>
                        <div>
                            <label for="horaFin">End Time:</label>
                            <input type="time" id="horaFin" name="horaFin" required><br><br>
                        </div>
                        <div>
                            <label for="tipoCarga">Load Type:</label>
                            <select id="tipoCarga" name="tipoCarga" required>
                                <?php
                                    while ($rowTipo = $resultTipo->fetch_assoc()) {
                                        echo "<option value='" . $rowTipo['codigo'] . "'>" . $rowTipo['descripcion'] . "</option>";
                                    }
                                ?>
                            </select><br><br>
                        </div>
                        <div>
                            <label for="prioridad">Delivery Priority:</label>
                            <select id="prioridad" name="prioridad" required>
                                <?php
                                    while ($rowPrio = $resultPrioridad->fetch_assoc()) {
                                        echo "<option value='" . $rowPrio['codigo'] . "'>" . $rowPrio['descripcion'] . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                </div>
                <!-- Formulario de Producto -->
                <div class="formulario">
                    <h3>Products Form</h3>
                    <div id="producto-container">
                        <div class="producto-field">
                            <div class="cantidad-producto">
                                <label for="producto1">Product 1:</label>
                                <select name="producto[]" required>
                                    <?php
                                        $resultProducto->data_seek(0); // Reinicia el cursor
                                        while ($rowPro = $resultProducto->fetch_assoc()) {
                                            echo "<option value='" . $rowPro['num'] . "'>" . $rowPro['nombre'] . "</option>";
                                        }
                                    ?>
                                </select>
                            </div><br>
                            <div class="cantidad-producto">
                                <label for="cantidad1">Amount:</label>
                                <input type="number" name="cantidad[]" id="cantidad1" required>
                            </div><br>
                        </div>
                    </div>
                    <div class="buttons-add-delete">
                        <button type="button" class="btn-agregar" onclick="agregarProducto()">Add Product</button>
                        <button type="button" class="btn-eliminar" onclick="eliminarUltimoProducto()">Delete Product</button>
                    </div>
                </div>
                <!-- Formulario de Ubicacion -->
                <div class="formulario">
                    <h3>Locations Form</h3>
                    <div id="locations-container">
                        <div class="location-origin-field">
                            <label for="originLocation">Origin Location:</label>
                            <select id="originLocation" name="originLocation" required>
                                <?php
                                    $resultUbicaciones->data_seek(0); // Reinicia el cursor
                                    while ($rowLoc = $resultUbicaciones->fetch_assoc()) {
                                        echo "<option value='" . $rowLoc['ubicacion'] . "'>" . $rowLoc['nombreUbicacion'] . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="location-destination-field">
                            <label for="desLocation">Destination Location 1:</label>
                            <select id="desLocation" name="desLocation[]" required onchange="actualizarOpcionesUbicacion()">
                                <?php
                                    $resultUbicaciones->data_seek(0); // Reinicia el cursor
                                    while ($rowLoc = $resultUbicaciones->fetch_assoc()) {
                                        echo "<option value='" . $rowLoc['ubicacion'] . "'>" . $rowLoc['nombreUbicacion'] . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="buttons-add-delete">
                        <button type="button" class="btn-agregar" onclick="agregarUbicacion()">Add Location</button>
                        <button type="button" class="btn-eliminar" onclick="eliminarUltimaUbicacion()">Delete Location</button>
                    </div>
                </div>
            </div>
            <div>
                <button type="submit" name="accion" value="registerDelivery" class="btn-guardar">Send Delivery Order</button>
            </div>
        </form>
         
    </div>
        <?php
        }
    }
?>