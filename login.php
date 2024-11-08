<?php 
    session_start();
    require ('includes/config/conection.php');
    $db = connectTo2DB();
    
    if (isset($_GET['status']) && $_GET['status'] === 'error') {
        echo "<p>Credenciales incorrectas, por favor intente de nuevo</p>";
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        if (!isset($email) || $email == '' || !isset($password) || $password == '') {
            echo 'Rellenar todo el formulario';
        } else {
            $query = "SELECT * FROM cliente WHERE email='$email' AND password='$password'";
            $response = mysqli_query($db, $query);
            $user = $response->fetch_assoc();
            if ($response->num_rows > 0 && $password == $user['password']) {
                // Inicio de sesión exitoso
                header("Location: menuCL.php?status=success");
                exit();
            } else {
                $query = "SELECT * FROM empleados WHERE email='$email' AND password='$password'";
                $response = mysqli_query($db, $query);
                if ($response->num_rows > 0 && $password == $user['password']) {
                    // Inicio de sesión exitoso
                    header("Location: menuCL.php?status=success");
                    exit();
                } else {
                    // Credenciales incorrectas
                    header("Location: Login.php?status=error");
                    exit();
                }
                header("Location: index.php?status=errorLog");
                exit();
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
                <h2> Login</h2>
            </div>
            <h3>LOGIXPRESS</h3>
            <h3 class="border">Login Credentials</h3>
        </div>
        <div class="elements-form">
            <form action="" method="POST">
                <div class="align-items-form">
                    <div class="input-form">
                        <input type="text" name="email" id="email" placeholder="Email" required>
                    </div>
                    <div class="input-form">
                        <input type="password" name="password" id="password" placeholder="Password" required>
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