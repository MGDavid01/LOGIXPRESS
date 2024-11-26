<?php
// Recuperar datos del formulario
$nombreUbicacionI = $_POST['nombreUbicacionI'];
$nombreCalleI = $_POST['nombreCalleI'];
$numCalleI = $_POST['numCalleI'];
$coloniaI = $_POST['coloniaI'];
$codigoPostalI = $_POST['codigoPostalI'];
$clienteID = $_SESSION['user_id']; // Asegúrate de que este valor también sea pasado desde el formulario

// Preparar el mensaje que se obtendrá del procedimiento almacenado
$mensaje = "";

// Preparar la consulta para llamar al procedimiento almacenado
$queryCallSP = "CALL SP_registrarAsociarUbicacion(
    '$nombreUbicacionI', 
    '$nombreCalleI', 
    '$numCalleI', 
    '$coloniaI', 
    '$codigoPostalI', 
    $clienteID,
    @mensaje
)";

// Ejecutar la llamada al procedimiento almacenado
$resultCallSP = mysqli_query($db, $queryCallSP);

if ($resultCallSP) {
    // Obtener el mensaje de salida del procedimiento
    $queryGetMessage = "SELECT @mensaje AS mensaje";
    $resultMessage = mysqli_query($db, $queryGetMessage);
    $rowMessage = mysqli_fetch_assoc($resultMessage);
    $mensaje = $rowMessage['mensaje'];

    if ($mensaje == "Ubicacion asociada exitosamente.") {
        // Redirigir o mostrar mensaje de éxito
        header("Location: ?section=locations&tool=add&status=addedLocation");
        exit;
    } else {
        // Mostrar mensaje de error devuelto por el procedimiento almacenado
        echo "<p>Error: $mensaje</p>";
    }
} else {
    // Mostrar el error de MySQL
    echo "Error al ejecutar el procedimiento almacenado: " . mysqli_error($db);
}
?>