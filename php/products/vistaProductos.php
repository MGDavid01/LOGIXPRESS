<?php
function vistaProductos($cliente) {
    global $db;
    
    $ubicacion = [];
    $queryUbicacion = "SELECT p.codigo, p.nombre
            FROM producto p
            WHERE p.cliente = $cliente";
    
    $result = mysqli_query($db, $queryUbicacion);
    if (!$result) {
        die("Error en la consulta: " . mysqli_error($db));
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $ubicacion[] = $row;
    }

    // Mostrar los datos en una tabla HTML
    if (!empty($ubicacion)) {
        echo '<link rel="stylesheet" href="css/menuCL/menuCLDetallesEntrega.css">';

        echo '<div class="datos-generales">';
            echo '<h2>Product List</h2>';
            echo '<table>';
            echo '<tr>
                    <th>ID</th>
                    <th>Name Product</th>
                    <th>Action</th>
                </tr>';
            
            foreach ($ubicacion as $row) {
                echo "<tr>
                    <td>{$row['codigo']}</td>
                    <td>{$row['nombre']}</td>
                    <td><a href='?section=products&product=".$row['codigo']."'>Edit</a></td>
                </tr>";
            }
            echo '</table>';
        echo '</div>';

        echo '<div class="datos-desglose">';
            // Si se ha seleccionado una ubicación, mostrar los detalles
            if (isset($_GET['section']) && $_GET['section'] == "products" && isset($_GET['product'])) {
                $producto_id = $_GET['product'];
                vistaFormularioUbicaciones($cliente, $producto_id);
            } else {
                echo '<p style="font-size:2rem;">Select a product to edit.</p>';
            }
        echo '</div>';
    }
}    

function vistaFormularioUbicaciones($cliente, $producto_id) {
    global $db;

    // Consulta los detalles de la ubicación
    $queryUbicacion = "SELECT p.codigo, p.nombre, p.descripcion, p.alto, p.ancho, p.largo, p.peso, p.etiquetado, p.categoria
    FROM producto p
    WHERE p.cliente = '$cliente' AND p.codigo = '$producto_id'";
    $resultUbicacion = mysqli_query($db, $queryUbicacion);

    $producto = mysqli_fetch_assoc($resultUbicacion);

    if (!$producto) {
        echo '<p>Error: No se encontró el producto.</p>';
        return;
    }

    $queryEtiquetado = "SELECT e.codigo, e.descripcion
        FROM etiquetado e";
    $resultEtiquetado = mysqli_query($db, $queryEtiquetado);

    $queryCategoria = "SELECT c.codigo, c.descripcion
        FROM cat_prod c";
    $resultCategoria = mysqli_query($db, $queryCategoria);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'updateProduct') {
        // Recuperar datos del formulario
        $nombreProd = $_POST['nombre'];
        $descripcionProd = $_POST['descripcion'];
        $categoriaProd = $_POST['categoria'];
        $etiquetadoProd = $_POST['etiquetado'];
        $altoProd = $_POST['alto'];
        $anchoProd = $_POST['ancho'];
        $largoProd = $_POST['largo'];
        $pesoProd = $_POST['peso'];
    
        // Validar que el ID sea válido
        if (!$producto_id) {
            echo "ID de la ubicación no válido.";
            exit;
        }
    
        // Actualizar la información de la ubicación
        $query = "UPDATE producto 
                SET nombre = '$nombreProd',
                    descripcion = '$descripcionProd',
                    categoria = '$categoriaProd',
                    etiquetado = '$etiquetadoProd',
                    alto = $altoProd,
                    ancho = $anchoProd,
                    largo = $largoProd,
                    peso = $pesoProd
                WHERE codigo = '$producto_id'";
    
        $result = mysqli_query($db, $query);
    
        if ($result) {
            echo "Ubicación actualizada con éxito.";
            header("Location: ?section=products&product=$producto_id&status=productupdated"); // Redirigir a la lista de ubicaciones
            exit;
        } else {
            echo "Error al actualizar la ubicación: " . mysqli_error($db);
        }
    }
    
    // Mostrar el formulario
    echo '<div class="form">';
    if(isset($_GET['status']) && $_GET['status'] === 'productupdated'){
        echo '<p style="font-size:2rem; text-align: end; color: #57cf8b;">Product Updated</p>';
    }
    echo '<h2>Edit Product</h2>';
    echo '<form action="" method="POST">';

    // Campo oculto para el ID de la ubicación
    echo '<input type="hidden" name="producto_id" value="' . $producto_id . '">';

    // Campo: Nombre
    echo '<div class="form-group">';
    echo '<label for="nombre">Product:</label>';
    echo '<input type="text" id="nombre" name="nombre" value="' . htmlspecialchars($producto['nombre']) . '" required>';
    echo '</div>';

    // Campo: Descripcion
    echo '<div class="form-group">';
    echo '<label for="descripcion">Description:</label>';
    echo '<input type="text" id="descripcion" name="descripcion" value="' . htmlspecialchars($producto['descripcion']) . '" required>';
    echo '</div>';

    // Campo: Etiquetado
    echo '<div class="form-group">';
    echo '<label for="etiquetado">Product Tag:</label>';
    echo '<select id="etiquetado" name="etiquetado" required>';
        if ($resultEtiquetado) {
            $resultEtiquetado->data_seek(0);
            while ($rowEti = mysqli_fetch_assoc($resultEtiquetado)) {
                $selected = ($rowEti['codigo'] == $producto['etiquetado']) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($rowEti['codigo']) . "' $selected>" . htmlspecialchars($rowEti['descripcion']) . "</option>";
            }
        }
    echo '</select>';
    echo '</div>';

    // Campo: Categoria
    echo '<div class="form-group">';
    echo '<label for="categoria">Category Product:</label>';
    echo '<select id="categoria" name="categoria" required>';
    if ($resultCategoria) {
        $resultCategoria->data_seek(0);
        while ($rowCat = mysqli_fetch_assoc($resultCategoria)) {
            $selected = ($rowCat['codigo'] == $producto['categoria']) ? 'selected' : '';
            echo "<option value='" . htmlspecialchars($rowCat['codigo']) . "' $selected>" . htmlspecialchars($rowCat['descripcion']) . "</option>";
        }
    }
    echo '</select>';
    echo '</div>';

    // Campo: Alto
    echo '<div class="form-group">';
    echo '<label for="alto">Height:</label>';
    echo '<input type="number" step="any" id="alto" name="alto" value="' . htmlspecialchars($producto['alto']) . '" required>';
    echo '</div>';

    // Campo: Ancho
    echo '<div class="form-group">';
    echo '<label for="ancho">Width:</label>'; 
    echo '<input type="number" step="any" id="ancho" name="ancho" value="' . htmlspecialchars($producto['ancho']) . '" required>';
    echo '</div>';

    // Campo: Largo
    echo '<div class="form-group">';
    echo '<label for="largo">Length:</label>';
    echo '<input type="number" step="any" id="largo" name="largo" value="' . htmlspecialchars($producto['largo']) . '" required>';
    echo '</div>';

    // Campo: Peso
    echo '<div class="form-group">';
    echo '<label for="peso">Weight:</label>';
    echo '<input type="number" step="any" id="peso" name="peso" value="' . htmlspecialchars($producto['peso']) . '" required>';
    echo '</div>';

    // Botón: Guardar Cambios
    echo '<button type="submit" name="accion" value="updateProduct" class="btn-guardar">Update</button>';

    echo '</form>';
    echo '</div>';
}
?>