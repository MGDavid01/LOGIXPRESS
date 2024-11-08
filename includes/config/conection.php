<?php
function connectTo2DB() : mysqli {
    $db = mysqli_connect("localhost","root","","LOGIXPRESS", 3308);
    if (!$db) {
        die("Error en la conexión: " . mysqli_connect_error());
    } else {
        echo "Conectado";
    }
    return $db;
}
?>