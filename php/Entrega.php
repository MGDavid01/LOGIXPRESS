<?php
// Ejemplo de conexión a la base de datos
$conexion = new mysqli('localhost', 'usuario', 'contraseña', 'base_de_datos');

// Consulta para obtener las opciones
$resultado = $conexion->query("SELECT id, nombre_opcion FROM tabla_opciones");

// Generación de opciones en HTML
echo '<select name="opcion">';
while ($fila = $resultado->fetch_assoc()) {
    echo '<option value="' . $fila['id'] . '">' . $fila['nombre_opcion'] . '</option>';
}
echo '</select>';
?>
