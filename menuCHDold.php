<?php
session_start();
require('includes/config/conection.php');
$db = connectTo2DB();
// Funcion para mostrar el inicio
function vistaInicial(){
    echo "<p id='welcome'>Welcome, " . $_SESSION['nombre'] . "</p>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && $_POST['accion'] === 'logout') {
        // Cerrar sesión
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }
}
include_once('includes/headUsers.php');
?>

    <nav>
        <div class="logo-container">
            <img src="imagenes/LOGIXPRESS_LOGO_F2.png" alt="Logo">
        </div>
        <ul>
            <li><a href="?section=asignarEntregas">Assign Deliveries</a></li>
            <li><a href="?section=entregasPendientes">Pending Deliveries</a></li>
            <li><a href="?section=historialEntregas">Delivery History</a></li>
            <li><a href="?section=vehiculosMantenimiento">Send to Maintenance</a></li>
        </ul>
        <!-- Botón de Logout -->
        <form action="" method="post" >
            <button type="submit" name="accion" value="logout">Log out</button>
        </form>
    </nav>
    <div class="main-content">
        <?php
        switch ($section) {
            case 'asignarEntregas':
                
                break;
            case 'entregasPendientes':
                
                break;
            case 'historialEntregas':
                
                break;
            case 'vehiculosMantenimiento':
            
                break;
            default:
                vistaInicial();
                break;
        }
        ?>
    </div>
</div>
</body>
</html>