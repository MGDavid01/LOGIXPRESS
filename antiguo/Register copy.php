<?php
    session_start();
    require ('includes/config/conection.php');
    $db = connectTo2DB();
    // Guardar datos de `form1` en la sesión si se envía el primer formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form1'])) {
        $_SESSION['nombre'] = $_POST['nombre'];
        $_SESSION['primerApe'] = $_POST['primerApe'];
        $_SESSION['segundoApe'] = $_POST['segundoApe'];
        echo "Datos del Formulario 1 guardados en sesión:<br>";
        print_r($_SESSION);
        header("Location: Register.php"); // Redireccionar para evitar reenvío en el primer formulario
        exit();
    }

    // Procesar ambos formularios e insertar datos en la base de datos cuando se envía `form2`
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form2'])) {
        // Recuperar datos de la sesión y del segundo formulario
        $nombre = $_SESSION['nombre'] ?? null;
        $primerApe = $_SESSION['primerApe'] ?? null;
        $segundoApe = $_SESSION['segundoApe'] ?? null;
        $empresa = $_POST['empresa'] ?? null;
        $telefono = $_POST['telefono'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        echo "Datos recibidos en el Formulario 2:<br>";
        print_r($_POST); // Verificar datos de POST de form2

        // Verificar si todos los datos requeridos están presentes
        if ($nombre && $primerApe && $empresa && $telefono && $email && $password) {
            try {
                // Preparar la sentencia SQL para insertar datos de forma segura
                $stmt = $conn->prepare("INSERT INTO CLIENTE (nombreEmpresa, nombrePila, primerApellido, segundoApellido, numTelefono, email, password)
                                        VALUES (:empresa, :nombre, :primerApe, :segundoApe, :telefono, :email, :password)");
                
                // Ejecutar la consulta con parámetros vinculados
                if ($stmt->execute([
                    ':empresa' => $empresa,
                    ':nombre' => $nombre,
                    ':primerApe' => $primerApe,
                    ':segundoApe' => $segundoApe,
                    ':telefono' => $telefono,
                    ':email' => $email,
                    ':password' => $password
                ])) {
                    echo "Registro exitoso";
                    session_unset(); // Limpiar los datos de la sesión después de la inserción
                } else {
                    echo "Error al registrar en la base de datos.";
                }
            } catch (PDOException $e) {
                echo "Error en el insert: " . $e->getMessage();
            }
        } else {
            echo "Por favor, completa todos los campos requeridos.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/Register.css">
    <title>Register</title>
</head>
<body>

    <div class="background-image"></div>

    <section>
        <div class="stacked-paper-container">
            <div class="paper-layer" id="paper1"></div>
            <div class="paper-layer" id="paper2"></div>
            <div class="paper-layer" id="paper3"></div>
    
            <!-- Formulario 1 -->
            <div class="form-box form-page" id="form1">
                <form id="form1-content" method="POST">
                    <h2>Registro</h2>
                    <input type="hidden" name="form1" value="1">
                    <label for="nombre">Ingrese Su Nombre</label>
                    <input type="text" id="nombre" name="nombre" required>
                    <br>
                    <label for="primerApe">Ingrese Su Primer Apellido</label>
                    <input type="text" id="primerApe" name="primerApe" required>
                    <br>
                    <label for="segundoApe">Ingrese Su Segundo Apellido</label>
                    <input type="text" id="segundoApe" name="segundoApe" required>
                    <br>
                    <button type="submit" id="nextBtn" onclick="mostrarFormulario2()">Siguiente</button>
                </form>
            </div>
    
            <!-- Formulario 2 (visible pero detrás del formulario 1) -->
            <div class="form-box form-page" id="form2" style="display: none;">
                <form id="form2-content" method="POST" action="Register.php">
                    <h2>Datos de la Empresa</h2>
                    <input type="hidden" name="form2" value="1">
                    <label for="empresa">Nombre De La Empresa</label>
                    <input type="text" id="empresa" name="empresa" required>
                    <br>
                    <label for="telefono">Ingrese el Teléfono de la Empresa</label>
                    <input type="tel" id="telefono" name="telefono" required>
                    <br>
                    <label for="email">Ingrese el Email de la Empresa</label>
                    <input type="email" id="email" name="email" required>
                    <br>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <button type="submit">Finalizar</button>
                </form>
            </div>
        </div>
    </section>

    <script>
        function mostrarFormulario2() {
            document.getElementById('form1').style.display = 'none';
            document.getElementById('form2').style.display = 'block';
        }
    </script>

    <script src="./js/register.js"></script>
    
</body>    
</html>
