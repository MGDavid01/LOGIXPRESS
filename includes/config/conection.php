<?php
function connectTo2DB() : mysqli {
    $db = mysqli_connect("localhost","root","","LOGIXPRESS");
    if($db){
        echo "Conectado";
    } else {
        echo "No conectado";
    }
    return $db;
}
?>