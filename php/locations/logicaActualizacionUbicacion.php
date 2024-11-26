<?php
// Recuperar datos del formulario
$ubicacion_id = $_POST['ubicacion_id'];
$nombreUbicacionU = $_POST['nombreUbicacion'];
$nombreCalleU = $_POST['nombreCalle'];
$numCalleU = $_POST['numCalle'];
$codigoPostalU = $_POST['codigoPostal'];
$coloniaU = $_POST['colonia'];

// Validar que el ID sea válido
if (!$ubicacion_id) {
    echo "ID de la ubicación no válido.";
    exit;
}

// Actualizar la información de la ubicación
$query = "UPDATE ubicacion 
        SET nombreUbicacion = '$nombreUbicacionU',
            nombreCalle = '$nombreCalleU',
            numCalle = '$numCalleU',
            colonia = '$coloniaU',
            codigoPostal = '$codigoPostalU'
        WHERE num = '$ubicacion_id'";

$result = mysqli_query($db, $query);

if ($result) {
    header("Location: ?section=locations&tool=edit&location=$ubicacion_id&status=updatededLocation"); // Redirigir a la lista de ubicaciones
    exit;
} else {
    echo "Error al actualizar la ubicación: " . mysqli_error($db);
};
?>