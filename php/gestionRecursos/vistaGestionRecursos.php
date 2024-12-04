
<?php 
    $herramienta = isset($_GET['recurso']) ? $_GET['recurso'] : '';
    switch ($herramienta) {
        case 'empleado':
            include_once('registroRecursos/vistaRegistroRecursos.php');
            break;
        case 'editar':
            include_once('editarRecursos/vistaEditarRecursos.php');
            break;
        case 'eliminar':
            include_once('eliminarRecursos/vistaEliminarRecursos.php');
            break;
        default:
        include_once('vistaElegirRecurso.php');
            break;
    }
?>