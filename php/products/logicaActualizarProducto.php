<?php
$prod = $_POST['producto_id'];
$nombreProd = $_POST['nombre'];
$descripcionProd = $_POST['descripcion'];
$categoriaProd = $_POST['categoria'];
$etiquetadoProd = $_POST['etiquetado'];
$altoProd = $_POST['alto'];
$anchoProd = $_POST['ancho'];
$largoProd = $_POST['largo'];
$pesoProd = $_POST['peso'];

// Preparar el mensaje de salida del procedimiento almacenado
$mensaje = "";
// Llamar al procedimiento almacenado para insertar el producto
$queryCallSP = "CALL SP_actualizarProducto($prod, '$nombreProd', '$descripcionProd', '$categoriaProd', 
'$etiquetadoProd', $altoProd, $anchoProd, $largoProd, $pesoProd, @mensaje)";

$resultCallSP = mysqli_query($db, $queryCallSP);

if ($resultCallSP) {
    // Obtener el mensaje de salida del procedimiento
    $queryGetMessage = "SELECT @mensaje AS mensaje";
    $resultMessage = mysqli_query($db, $queryGetMessage);
    $rowMessage = mysqli_fetch_assoc($resultMessage);
    $mensaje = $rowMessage['mensaje'];

    if ($mensaje == "Producto actualizado exitosamente.") {
        // Redirigir o mostrar mensaje de éxito
        header("Location: ?section=products&tool=editProduct&product=$prod&status=productUpdated");
        exit;
    } else {
        // Mostrar mensaje de error devuelto por el procedimiento almacenado
        echo "<p>Error: $mensaje</p>";
    }
} else {
    // Mostrar el error de MySQL
    echo "Error al ejecutar la accion: " . mysqli_error($db);
}
?>
?>