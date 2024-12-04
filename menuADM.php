<?php
session_start();
require('includes/config/conection.php');
$db = connectTo2DB();
// Funcion para mostrar el inicio
function vistaInicial(){
    echo "<p id='welcome'>Welcome, " . $_SESSION['nombre'] . "</p>";
}

// Función para mostrar el formulario de empleados
function formularioEmpleado($empleado = null) {
    global $db;

    $puestos = [];
    $query = "SELECT codigo, descripcion FROM puesto";
    $result = mysqli_query($db, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $puestos[] = $row;
    }

    $num = $empleado['num'] ?? '';
    $nombre = $empleado['nombre'] ?? '';
    $primerApe = $empleado['primerApe'] ?? '';
    $segundoApe = $empleado['segundoApe'] ?? '';
    $telefono = $empleado['telefono'] ?? '';
    $email = $empleado['email'] ?? '';
    $password = '';
    $puesto = $empleado['puesto'] ?? '';
    ?>
    <h3>Formulario de Empleado</h3>
    <form action="" method="post">
        <input type="hidden" name="num" value="<?php echo $num; ?>">
        <label>Name: <input type="text" name="nombre" value="<?php echo $nombre; ?>"></label>
        <label>Last Name: <input type="text" name="primerApe" value="<?php echo $primerApe; ?>"></label>
        <label>Second Last Name: <input type="text" name="segundoApe" value="<?php echo $segundoApe; ?>"></label>
        <label>Phone: <input type="text" name="telefono" value="<?php echo $telefono; ?>"></label>
        <label>Email: <input type="email" name="email" value="<?php echo $email; ?>"></label>
        <label>Password: <input type="password" name="password" value="<?php echo $password; ?>"></label>
        <label>Position:
            <select name="puesto">
                <option value="" selected>Seleccione un puesto</option>
                <?php foreach ($puestos as $p) : ?>
                    <option value="<?php echo $p['codigo']; ?>" <?php echo $puesto == $p['codigo'] ? 'selected' : ''; ?>>
                        <?php echo $p['descripcion']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" name="accion" value="guardar_empleado">Guardar</button>
    </form>
    <?php
}

// Función para mostrar el formulario de vehículos
function formularioVehiculo($vehiculo = null) {
    $placa = $vehiculo['placa'] ?? '';
    $numSerie = $vehiculo['numSerie'] ?? '';
    $marca = $vehiculo['marca'] ?? '';
    $modelo = $vehiculo['modelo'] ?? '';
    $anio = $vehiculo['anio'] ?? '';
    $tipoCarga = $vehiculo['tipoCarga'] ?? '';
    $categoria = $vehiculo['categoria'] ?? '';
    $capacidad = $vehiculo['capacidad'] ?? '';
    $disponibilidad = $vehiculo['disponibilidad'] ?? '';
    ?>
    <h3>Formulario de Vehículo</h3>
    <form action="" method="post">
        <label>Plate: <input type="text" name="placa" value="<?php echo $placa; ?>"></label>
        <label>Serial Number: <input type="text" name="numSerie" value="<?php echo $numSerie; ?>"></label>
        <label>Brand: <input type="text" name="marca" value="<?php echo $marca; ?>"></label>
        <label>Model: <input type="text" name="modelo" value="<?php echo $modelo; ?>"></label>
        <label>Year: <input type="number" name="anio" value="<?php echo $anio; ?>"></label>
        <label>Type of Load: <input type="text" name="tipoCarga" value="<?php echo $tipoCarga; ?>"></label>
        <label>Category: <input type="text" name="categoria" value="<?php echo $categoria; ?>"></label>
        <label>Capacity: <input type="number" name="capacidad" value="<?php echo $capacidad; ?>"></label>
        <label>Availability: <input type="text" name="disponibilidad" value="<?php echo $disponibilidad; ?>"></label>
        <button type="submit" name="accion" value="guardar_vehiculo">Guardar</button>
    </form>
    <?php
}

// Función para mostrar el formulario de remolques
function formularioRemolque($remolque = null) {
    $numSerie = $remolque['numSerie'] ?? '';
    $marca = $remolque['marca'] ?? '';
    $modelo = $remolque['modelo'] ?? '';
    $anio = $remolque['anio'] ?? '';
    $tipoCarga = $remolque['tipoCarga'] ?? '';
    $capacidad = $remolque['capacidad'] ?? '';
    $disponibilidad = $remolque['disponibilidad'] ?? '';
    ?>
    <h3>Formulario de Remolque</h3>
    <form action="" method="post">
        <label>Serial Number: <input type="text" name="numSerie" value="<?php echo $numSerie; ?>"></label>
        <label>Brand: <input type="text" name="marca" value="<?php echo $marca; ?>"></label>
        <label>Model: <input type="text" name="modelo" value="<?php echo $modelo; ?>"></label>
        <label>Year: <input type="number" name="anio" value="<?php echo $anio; ?>"></label>
        <label>Load Type: <input type="text" name="tipoCarga" value="<?php echo $tipoCarga; ?>"></label>
        <label>Capacity: <input type="number" name="capacidad" value="<?php echo $capacidad; ?>"></label>
        <label>Availability: <input type="text" name="disponibilidad" value="<?php echo $disponibilidad; ?>"></label>
        <button type="submit" name="accion" value="guardar_remolque">Guardar</button>
    </form>
    <?php
}

// Lógica de inserción y verificación de duplicados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && $_POST['accion'] === 'logout') {
        // Cerrar sesión
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }

    $accion = $_POST['accion'];
    switch ($accion) {
        case 'guardar_empleado':
            $nombre = $_POST['nombre'];
            $primerApe = $_POST['primerApe'];
            $segundoApe = $_POST['segundoApe'];
            $telefono = $_POST['telefono'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $puesto = $_POST['puesto'];

            $stmt = $db->prepare("SELECT email FROM empleado WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo "Error: El correo ya está registrado para otro empleado.";
            } else {
                $stmt->close();
                $stmt = $db->prepare("INSERT INTO empleado (nombre, primerApe, segundoApe, telefono, email, password, puesto) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $nombre, $primerApe, $segundoApe, $telefono, $email, $password, $puesto);

                if ($stmt->execute()) {
                    echo "Empleado insertado correctamente.";
                } else {
                    echo "Error al insertar el empleado: " . $stmt->error;
                }
                $stmt->close();
            }
            break;

        case 'guardar_vehiculo':
            $placa = $_POST['placa'];
            $numSerie = $_POST['numSerie'];
            $marca = $_POST['marca'];
            $modelo = $_POST['modelo'];
            $anio = $_POST['anio'];
            $tipoCarga = $_POST['tipoCarga'];
            $categoria = $_POST['categoria'];
            $capacidad = $_POST['capacidad'];
            $disponibilidad = $_POST['disponibilidad'];

            $stmt = $db->prepare("SELECT placa FROM vehiculo WHERE placa = ? OR numSerie = ?");
            $stmt->bind_param("ss", $placa, $numSerie);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo "Error: La placa o el número de serie ya están registrados para otro vehículo.";
            } else {
                $stmt->close();
                $stmt = $db->prepare("INSERT INTO vehiculo (placa, numSerie, marca, modelo, anio, tipoCarga, categoria, capacidad, disponibilidad) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssiiiii", $placa, $numSerie, $marca, $modelo, $anio, $tipoCarga, $categoria, $capacidad, $disponibilidad);

                if ($stmt->execute()) {
                    echo "Vehículo insertado correctamente.";
                } else {
                    echo "Error al insertar el vehículo: " . $stmt->error;
                }
                $stmt->close();
            }
            break;

        case 'guardar_remolque':
            $numSerie = $_POST['numSerie'];
            $marca = $_POST['marca'];
            $modelo = $_POST['modelo'];
            $anio = $_POST['anio'];
            $tipoCarga = $_POST['tipoCarga'];
            $capacidad = $_POST['capacidad'];
            $disponibilidad = $_POST['disponibilidad'];

            $stmt = $db->prepare("SELECT numSerie FROM remolque WHERE numSerie = ?");
            $stmt->bind_param("s", $numSerie);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo "Error: El número de serie ya está registrado para otro remolque.";
            } else {
                $stmt->close();
                $stmt = $db->prepare("INSERT INTO remolque (numSerie, marca, modelo, anio, tipoCarga, capacidad, disponibilidad) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssiiii", $numSerie, $marca, $modelo, $anio, $tipoCarga, $capacidad, $disponibilidad);

                if ($stmt->execute()) {
                    echo "Remolque insertado correctamente.";
                } else {
                    echo "Error al insertar el remolque: " . $stmt->error;
                }
                $stmt->close();
            }
            break;
    }
}

// Selección de sección para mostrar formulario
$section = isset($_GET['section']) ? $_GET['section'] : '';
include_once('includes/headUsers.php');
?>
    <link rel="stylesheet" href="css/menuADM/menuADM.css">
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/tables.css">
    <nav class="side-nav">
        <div class="logo-container">
            <a href="menuADM.php" id="logo-hover"><img src="imagenes/LOGIXPRESS_LOGO_F2.png" alt="Logo"></a>
        </div>
        <ul>
            <li><a href="?section=gestion">Resource Management</a></li>
        </ul>
        <!-- Botón de Logout -->
        <form action="" method="post" >
            <button type="submit" name="accion" value="logout">Log out</button>
        </form>
    </nav>
    <div class="content-origin">
        <?php
        switch ($section) {
            case 'gestion':
                ?>  <link rel="stylesheet" href="css/menuADM/gestionRecursos/vistaGestionRecursos.css">
                    <script src="js/adminJS/manejoURL.js"></script>
                <?php
                include_once('php/gestionRecursos/vistaGestionRecursos.php');
                break;
            case 'tarifas':
                ?>  <link rel="stylesheet" href="css/menuADM/vistaHerramientaVehiculos.css">
                <?php
                break;
            case 'reportes':
                ?>  <link rel="stylesheet" href="css/menuADM/vistaHerramientaRemolques.css">
                <?php
                break;
            default:
            ?>  <link rel="stylesheet" href="css/menuADM/vistaInicial.css">
            <?php
                vistaInicial();
                break;
        }
        ?>
    </div>
</div>
</body>
</html>