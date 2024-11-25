<?php 
function verificarProductosUbicaciones($soloVerificar) {
    global $db;
    $verificacion = 0;

    // Consultar la cantidad de productos asociados
    $cantidadProductos = 0;
    $queryCountProductos = "SELECT COUNT(codigo) AS total FROM producto WHERE cliente = " . $_SESSION['user_id'];
    $resultProductos = mysqli_query($db, $queryCountProductos);
    if ($resultProductos) {
        $row = mysqli_fetch_assoc($resultProductos);
        $cantidadProductos = $row['total'];
    }

    // Consultar la cantidad de ubicaciones asociadas
    $cantidadUbicaciones = 0;
    $queryCountUbicaciones = "SELECT COUNT(ubicacion) AS total FROM cliente_ubi WHERE cliente = " . $_SESSION['user_id'];
    $resultUbicaciones = mysqli_query($db, $queryCountUbicaciones);
    if ($resultUbicaciones) {
        $row = mysqli_fetch_assoc($resultUbicaciones);
        $cantidadUbicaciones = $row['total'];
    }
    
    // Evaluar las cantidades y retornar resultado
    if ($cantidadProductos <= 0 && $cantidadUbicaciones <= 0) {
        $verificacion = 0; // Sin productos ni ubicaciones
    } else if ($cantidadProductos <= 0 && $cantidadUbicaciones == 1) {
        $verificacion = 1; // Sin productos sin ubicaciones necesarias
    } else if ($cantidadProductos > 0 && $cantidadUbicaciones == 1) {
            $verificacion = 2; // Con productos sin ubicaciones necesarias
    } else if ($cantidadProductos <= 0) {
        $verificacion = 3; // Sin productos
    } else if ($cantidadUbicaciones < 1) {
        $verificacion = 4; // Sin ubicaciones necesarias
    } else {
        $verificacion = 5; //Con ambos
    }
    $nivelEchoProdUbica = '';
    if ($soloVerificar == false){ // Se quiere saber que texto le pertenece a la verificacion
        // Evaluar resultado de la verificación
        if ($verificacion == 0) {
            $nivelEchoProdUbica = "<p id='delivery-succ'>You don't have associated Products and Locations</p>
                <p id='delivery-succ'>Do you want to register your Products and Locations?</p>";
        } else if ($verificacion == 1) {
            $nivelEchoProdUbica = "<p id='delivery-succ'>You don't have any associated products and you don't have the necessary locations</p>
                    <p id='delivery-succ'>Do you want to register your products and more Locations?</p>";
        } else if ($verificacion == 2) {
            $nivelEchoProdUbica = "<p id='delivery-succ'>You don't have the necessary locations</p>
                    <p id='delivery-succ'>Do you want to register more Location?</p>";
        } else if ($verificacion == 3) {
            $nivelEchoProdUbica = "<p id='delivery-succ'>You do not have any associated products</p>
                    <p id='delivery-succ'>Do you want to register your products?</p>";
        } else if ($verificacion == 4) {
            $nivelEchoProdUbica = "<p id='delivery-succ'>You don't have any associated locations</p>
                    <p id='delivery-succ'>Do you want to register your locations?</p>";
        } else if ($verificacion == 5) {
            $nivelEchoProdUbica = "<p id='delivery-succ'>Request your delivery</p>";
        }
    } else {
        $nivelEchoProdUbica = $verificacion; // No se quiere saber el texto
    }
    return $nivelEchoProdUbica;
}
?>