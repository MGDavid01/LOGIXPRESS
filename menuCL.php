<?php
session_start();
require('includes/config/conection.php');
$db = connectTo2DB();
include_once('php/delivery/verficacionProducto.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//Se queda aqui, esta muy chikito
function vistaInicial() {
    if (isset($_GET['status']) && $_GET['status'] === 'registeredDelivery' && $_SESSION['entrega_id'] != null) {
        echo '<p id="delivery-succ">Delivery registered successfully with Number: '.$_SESSION['entrega_id'].'.</p>';
    }
        
    // Mostrar mensaje de bienvenida, se utiliza la variable global $_session(es tipo array)
    echo "<p id='welcome'>Welcome, " . $_SESSION['nombre'] . "</p>";

    // Verificar productos y ubicaciones
    $nivelVerificadoProdUbica ='';
    $nivelVerificadoProdUbica = verificarProductosUbicaciones(false);

    echo $nivelVerificadoProdUbica;
}
include_once('php/delivery/vistaPedirEntrega.php');

include_once('php/deliveryDetails/vistaDetallesVista.php');

include_once('php/locations/vistaUbicaciones.php');

include_once('php/products/vistaProductos.php');

include_once('php/editInfoCL/editarInformacionCuentaCliente.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'logout') {
    header("Location: index.php");
    exit;
}else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registerDelivery') {
    include_once('php/delivery/logicaInsercionEntrega.php');
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'addProduct') {
        include_once('php/products/logicaInsertarProducto.php');
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'updateProduct') {
        include_once('php/delivery/logicaActualizarProducto.php');
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'addLocation') {
        include_once('php/locations/logicaInsercionUbicacion.php');
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'updateLocation') {
    include_once('php/locations/logicaActualizacionUbicacion.php');
}


$section = isset($_GET['section']) ? $_GET['section'] : '';
include_once('includes/headUsers.php');
?>      
        <script src="js/agregarEliminarProductos.js"></script>
        <script src="js/agregarEliminarUbicaciones.js"></script>
        <link rel="stylesheet" href="css/menuCL/menuCL.css">
        <link rel="stylesheet" href="css/forms.css">
        <link rel="stylesheet" href="css/tables.css">
        
        <nav class="side-nav">
            <div class="logo-container">
                <a href="menuCL.php" id="logo-hover"><img src="imagenes/LOGIXPRESS_LOGO_F2.png" alt="Logo"></a>
            </div>
            <ul>
                <li><a href="?section=delivery">Delivery</a></li>
                <li><a href="?section=deliverDetails">Delivery Details</a></li>
                <li><a href="?section=locations">Locations</a></li>
                <li><a href="?section=products">Products</a></li>
                <li><a href="?section=editAccount">Edit Account</a></li>
            </ul>
            <!-- Botón de Logout -->
            <form action="" style="all:unset; width:100%; text-align:center; " method="post" >
                <button  type="submit" name="accion" value="logout">Log out</button>
            </form>
        </nav>
        <div class="content-origin">
        <?php
            switch ($section) {
                case 'delivery':
                    ?><link rel="stylesheet" href="css/menuCL/vistaFormularioEntrega.css"><?php
                    vistaPedirEntrega();
                    break;
                case 'deliverDetails':
                    ?><link rel="stylesheet" href="css/menuCL/vistaDetallesEntrega.css"><?php
                    vistaDetallesEntrega($_SESSION['user_id']);
                    break;
                case 'locations':
                    ?><link rel="stylesheet" href="css/menuCL/vistaUbicacion.css"><?php
                    vistaUbicaciones($_SESSION['user_id']);
                    break;
                case 'products':
                    ?><link rel="stylesheet" href="css/menuCL/vistaProducto.css"><?php
                    //Utiliza el mismo css de DetallesEntrega
                    vistaProductos($_SESSION['user_id']);
                    break;
                case 'editAccount':
                    ?><link rel="stylesheet" href="css/menuCL/vistaInformacion.css"><?php
                    vistaEditarInfoCuenta($_SESSION['user_id']);
                    break;
                default:
                    ?><link rel="stylesheet" href="css/menuCL/vistaInicial.css"><?php
                    vistaInicial();
                    break;
            }
            ?>
        </div>
</div>
</body>
</html>