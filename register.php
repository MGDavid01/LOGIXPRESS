<?php
    session_start();
    require ('includes/config/conection.php');
    $db = connectTo2DB();
    if (isset($_GET['status']) && $_GET['status'] === 'error') {
        echo "<p>Account create error</p>";
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = $_POST['nombre'];
        $primerApe = $_POST['primerApe'];
        $segundoApe = $_POST['segundoApe'];
        $empresa = $_POST['empresa'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        if ($nombre == '' || $primerApe == '' || $empresa == '' || $telefono == '' || $email == '' || $password == '') {
            echo 'Fill out the entire form';
        } else {
            $query = "INSERT INTO CLIENTE (nomEmpresa, nomPila, primerApe, segundoApe, numTelefono, email, password)
            VALUES ('$empresa', '$nombre', '$primerApe', '$segundoApe', '$telefono', '$email', '$password')";
            
            $response = mysqli_query($db, $query);
            
            if ($response) {
                header("Location: index.php?status=success");
                exit();
            } else {
                header("Location: register.php?status=error");
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
    <link rel="stylesheet" href="css/register.css">
    <title>Register</title>
</head>
<body>
    <section>
        <div class="legend-form">
        <div id="back-index">
                <a id="href-back" href="/LOGIXPRESS/index.php">Home</a>
                <p>></p>
                <h2>Registration</h2>
            </div>
            <h3>LOGIXPRESS</h3>
        </div>
        <div>
            <form action="" method="POST">
                <div class="align-items-form">
                    <div class="row-form">
                        <!-- Formulario 1 -->
                        <h3 class="border">Contact Information</h3>
                        <div class="input-form">
                            <input type="text" id="nombre" name="nombre" placeholder="First Name" required>
                        </div>
                        <div class="input-form">
                            <input type="text" id="primerApe" name="primerApe" placeholder="Last Name" required>
                        </div>
                        <div class="input-form">
                            <input type="text" id="segundoApe" name="segundoApe" placeholder="Second Last Name" >
                        </div>
                    </div>
                    <div class="row-form">
                        <!-- Formulario 2 -->
                        <h3 class="border">Company Information</h3>
                        <div class="input-form">
                            <input type="text" id="empresa" name="empresa" placeholder="Company Name" required>
                        </div>
                        <div class="input-form">
                            <input type="tel" id="telefono" name="telefono" placeholder="Company Phone" required>
                        </div>
                        <div class="input-form">
                            <input type="email" id="email" name="email" placeholder="Company Email" required>
                        </div>
                        <div class="input-form">
                            <input type="password" id="password" name="password" placeholder="Password" required>
                        </div>
                    </div>
                </div>
                <div class="button-form">
                    <button type="submit" style="font-size: 1.5rem">Register</button>
                </div>
                <div class="input-form">
                    <a id="to-login" href="/LOGIXPRESS/login.php">Do you already have an account? Log in</a>
                </div>
            </form>
        </div>
    </section>
</body>    
</html>
