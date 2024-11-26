<?php
// Recuperar datos del formulario
$ubicacion_id = $_POST['ubicacion_id'];
$nombreUbicacionU = $_POST['nombreUbicacion'];
$nombreCalleU = $_POST['nombreCalle'];
$numCalleU = $_POST['numCalle'];
$codigoPostalU = $_POST['codigoPostal'];
$coloniaU = $_POST['colonia'];

// Actualizar la información de la ubicación
$queryInsertUbi = "INSERT INTO ubicacion (codigo, nombreUbicacion, nombreCalle, numCalle, colonia, codigoPostal) VALUES
        ($codigoGenerado,'$nombreUbicacionU', '$nombreCalleU', '$numCalleU', '$coloniaU', '$codigoPostalU')";

$resultInsertUbi = mysqli_query($db, $queryInsertUbi);

if ($resultInsertUbi) {
    header("Location: ?section=locations&tool=add&status=addLocation"); // Redirigir a la lista de ubicaciones
} else {
    echo "Error al actualizar la ubicación: " . mysqli_error($db);
};

?>