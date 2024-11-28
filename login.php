<?php
session_start();
require ('includes/config/conection.php');
$db = connectTo2DB();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (isset($_GET['status']) && $_GET['status'] === 'error') {
    echo "<p id='error-login'>Credenciales incorrectas, por favor intente de nuevo</p>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $hashed_password = hash('sha256', $_POST['password']); 


    if (empty($email) || empty($hashed_password)) {
        echo 'Rellenar todo el formulario';
    } else {
        // Comprobamos en la tabla cliente
        $query = "SELECT * FROM cliente WHERE email='$email' AND password='$hashed_password'";
        $response = mysqli_query($db, $query);
        $user = mysqli_fetch_assoc($response);

        if ($user) {
            // Inicio de sesión exitoso como cliente
            $_SESSION['user_id'] = $user['num']; // Guarda el ID para futuras consultas si es necesario
            $_SESSION['nombre'] = $user['nomPila'];
            header("Location: menuCL.php?status=success");
            exit();
        } else {
            // Comprobamos en la tabla empleado
            $query = "SELECT * FROM empleado WHERE email='$email' AND password='$hashed_password'";
            $response = mysqli_query($db, $query);
            $user = mysqli_fetch_assoc($response);

            if ($user) {
                // Inicio de sesión exitoso como empleado
                $_SESSION['user_id'] = $user['num']; // Guarda el ID para futuras consultas si es necesario
                $_SESSION['puesto'] = $user['puesto']; // Guarda el puesto
                $_SESSION['nombre'] = $user['nombre'];
                
                // Redirección basada en el puesto
                switch ($user['puesto']) {
                    case 'ADM':
                        header("Location: menuADM.php?status=success");
                        break;
                    case 'CHF':
                        header("Location: menuCHF.php?status=success");
                        break;
                    case 'CHD':
                        header("Location: menuCHD.php?status=success");
                        break;
                    default:
                        header("Location: menuEM.php?status=success");
                        break;
                }
                exit();
            } else {
                // Credenciales incorrectas
                header("Location: login.php?status=error");
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <title>Login LOGIXPRESS</title>
</head>
<body>
    <section>
        <div class="legend-form">
            <div id="back-index">
                <a id="href-back" href="/LOGIXPRESS/index.php">Home</a>
                <p>></p>
                <h2>Login</h2>
            </div>
            <h3>LOGIXPRESS</h3>
            <h3 class="border">Login Credentials</h3>
        </div>
        <div class="elements-form">
            <form action="" method="POST">
                <div class="align-items-form">
                    <div class="input-form">
                        <input type="text" name="email" id="email" placeholder="Email">
                    </div>
                    <div class="input-form">
                        <input type="password" name="password" id="password" placeholder="Password">
                    </div>
                    <div class="button-form">
                        <button style="font-size: 1.5rem" type="submit">Log In</button>
                    </div>
                    <div class="input-form">
                        <a id="to-login" href="/LOGIXPRESS/register.php">Don't have an account?</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</body>
</html>