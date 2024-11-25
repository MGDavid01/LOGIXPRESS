<?php
function connectTo2DB() : mysqli {
    $db = mysqli_connect("localhost","root","","LOGIXPRESS");
    if (!$db) {
        die("Error en la conexión: " . mysqli_connect_error());
    } 
    // else {
    //     echo 'Conectado';
    // }
    return $db;
}
?>