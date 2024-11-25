<?php
function vistaEditarInfoCuenta($cliente) {
    global $db;

    // Recuperar los datos actuales del cliente
    $query = "SELECT nomPila, primerApe, segundoApe, numTelefono, email, password FROM cliente WHERE num = '$cliente'";
    $result = mysqli_query($db, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Almacenar los datos actuales en variables
        $nomPila = htmlspecialchars($row['nomPila']);
        $primerApe = htmlspecialchars($row['primerApe']);
        $segundoApe = htmlspecialchars($row['segundoApe']);
        $numTelefono = htmlspecialchars($row['numTelefono']);
        $email = htmlspecialchars($row['email']);
        $password = htmlspecialchars($row['password']);
        
    } else {
        echo "<p>Error: No se pudo recuperar la información del cliente.</p>";
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'toInfoUpdate') {
        $numTelefono = $_POST['numTelefono'];
        $email = $_POST['email'];
        $nomPila = $_POST['nomPila'];
        $primerApe = $_POST['primerApe'];
        $segundoApe = $_POST['segundoApe'];

        if (!empty($_POST['password'])) {
            $hashed_password = hash('sha256', $_POST['password']); 
        } else {
            $hashed_password = $password;
        }

        // Falta Validar
        $queryUpdateInfoCliente = "UPDATE cliente SET 
                    nomPila = '$nomPila', 
                    primerApe = '$primerApe', 
                    segundoApe = '$segundoApe',
                    numTelefono = '$numTelefono',
                    email = '$email',
                    password = '$hashed_password'
                    WHERE num = '$cliente'";

        $result = mysqli_query($db, $queryUpdateInfoCliente);
        if ($result) {
            header("Location: ?section=editAccount&status=infoUpdated"); // Redirigir a la lista de ubicaciones
        }else {
            header("Location: ?section=editAccount&status=errorInfoUpdate"); // Redirigir a la lista de ubicaciones
        }
    }
?>
            
    <form action="" method="POST">
        <div class="form">
            <?php 
            if(isset($_GET['status']) && $_GET['status'] === 'infoUpdated'){
                echo '<p style="font-size:2rem; text-align: end; color: #57cf8b;">Information Updated</p>';
        
            } else if(isset($_GET['status']) && $_GET['status'] === 'errorInfoUpdate') {
                echo '<p style="font-size:2rem; text-align: end; color: #57cf8b;">Error Information Updated</p>';
            }
            ?>
            <div class="formulario">
            <h2>Edit Contact Information</h2>
                <div class="form-group">
                    <label for="nomPila">First Name:</label>
                    <input type="text" id="nomPila" name="nomPila" value="<?php echo $nomPila; ?>" required>
                </div>
                <div class="form-group">
                    <label for="primerApe">Last Name:</label>
                    <input type="text" id="primerApe" name="primerApe" value="<?php echo $primerApe; ?>" required>
                </div>
                <div class="form-group">
                    <label for="segundoApe">Second Last Name:</label>
                    <input type="text" id="segundoApe" name="segundoApe" value="<?php echo $segundoApe; ?>">
                </div>
            </div>
            <div class="formulario">
                <h2>Edit Account Information</h2>
                <div class="form-group">
                    <label for="numTelefono">Phone Number:</label>
                    <input type="text" id="numTelefono" name="numTelefono" value="<?php echo $numTelefono; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Leave empty to keep current password">
                </div>
            </div>
            <button type="submit" name="accion" value="toInfoUpdate" class="btn-guardar">Update Information</button>
        </div>
    </form>
<?php

}
?>