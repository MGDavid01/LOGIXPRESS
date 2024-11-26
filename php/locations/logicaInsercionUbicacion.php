<?php
// Recuperar datos del formulario
$nombreUbicacionI = $_POST['nombreUbicacionI'];
$nombreCalleI = $_POST['nombreCalleI'];
$numCalleI = $_POST['numCalleI'];
$coloniaI = $_POST['coloniaI'];
$codigoPostalI = $_POST['codigoPostalI'];

// Actualizar la información de la ubicación
$queryInsertUbi = "INSERT INTO ubicacion (nombreUbicacion, nombreCalle, numCalle, colonia, codigoPostal) VALUES
        ('$nombreUbicacionI', '$nombreCalleI', '$numCalleI', '$coloniaI', '$codigoPostalI')";

$resultInsertUbi = mysqli_query($db, $queryInsertUbi);

if ($resultInsertUbi) {
    header("Location: ?section=locations&tool=add&status=addedLocation"); // Redirigir a la lista de ubicaciones
} else {
    echo "Error al actualizar la ubicación: " . mysqli_error($db);
};

?>