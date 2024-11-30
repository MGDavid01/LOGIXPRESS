<?php
function vistaProductos($cliente) {
    global $db;

    $productos = [];
    $queryProductos = "SELECT p.num, p.nombre FROM producto p WHERE p.cliente = $cliente";

    $result = mysqli_query($db, $queryProductos);
    if (!$result) {
        die("Error en la consulta: " . mysqli_error($db));
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $productos[] = $row;
    }

    // Mostrar los datos en una tabla HTML
    ?>
    <section class="content-tools">
        <div class="tools">
            <div>
                <a class="tool-text" href="?section=products&tool=addProduct">Add Product</a>
            </div>
            <div>
                <a class="tool-text" href="?section=products&tool=editProduct">Edit Product</a>
            </div>
        </div>
        <div class="information">
            <div class="general-info">
                <h2 style="text-align: center;">Product List</h2>
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Product Name</th>
                        <?php if (isset($_GET['tool']) && $_GET['tool'] == 'editProduct'): ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                    <?php foreach ($productos as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['num']) ?></td>
                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                            <?php if (isset($_GET['tool']) && $_GET['tool'] == 'editProduct'): ?>
                                <td><a class="btn-green" href="?section=products&tool=editProduct&product=<?= $row['num'] ?>">Edit</a></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="info-products">
                <?php if (isset($_GET['section'], $_GET['tool']) && $_GET['section'] === "products" && $_GET['tool'] === 'editProduct' && isset($_GET['product'])): ?>
                    <?php vistaEditProducto($cliente, $_GET['product']); ?>
                <?php elseif (isset($_GET['section'], $_GET['tool']) && $_GET['section'] === "products" && $_GET['tool'] === 'addProduct'): ?>
                    <?php vistaAddProduct($cliente); ?>
                <?php else: ?>
                    <p style="font-size:2rem;">Select a product to edit or add a new product.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
}

function vistaEditProducto($cliente, $producto_id) {
    global $db;

    // Consulta los detalles del producto
    $queryProducto = "SELECT p.num, p.nombre, p.descripcion, p.alto, p.ancho, p.largo, p.peso, p.etiquetado, p.categoria
    FROM producto p WHERE p.cliente = '$cliente' AND p.num = '$producto_id'";
    $resultProducto = mysqli_query($db, $queryProducto);

    $producto = mysqli_fetch_assoc($resultProducto);

    if (!$producto) {
        echo '<p>Error: No se encontró el producto.</p>';
        return;
    }

    // Consultar etiquetados y categorías
    $queryEtiquetado = "SELECT e.codigo, e.descripcion FROM etiquetado e";
    $resultEtiquetado = mysqli_query($db, $queryEtiquetado);

    $queryCategoria = "SELECT c.codigo, c.descripcion FROM cat_prod c";
    $resultCategoria = mysqli_query($db, $queryCategoria);

    // Mostrar el formulario para editar el producto
    ?>
    <div class="form">
        <?php if (isset($_GET['status']) && $_GET['status'] === 'productUpdated'): ?>
            <p style="font-size:2rem; text-align: end; color: #57cf8b;">Product Updated</p>
        <?php endif; ?>
        <h2>Edit Product</h2>
        <form action="" method="POST">
            <!-- Campo oculto para el ID del producto -->
            <input type="hidden" name="producto_id" value="<?= htmlspecialchars($producto['num']) ?>">

            <!-- Campo: Nombre del Producto -->
            <div class="form-group">
                <label for="nombre">Product:</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
            </div>

            <!-- Campo: Descripción -->
            <div class="form-group">
                <label for="descripcion">Description:</label>
                <input type="text" id="descripcion" name="descripcion" value="<?= htmlspecialchars($producto['descripcion']) ?>" required>
            </div>

            <!-- Campo: Etiquetado -->
            <div class="form-group">
                <label for="etiquetado">Product Tag:</label>
                <select id="etiquetado" name="etiquetado" required>
                    <?php if ($resultEtiquetado): ?>
                        <?php $resultEtiquetado->data_seek(0); ?>
                        <?php while ($rowEti = mysqli_fetch_assoc($resultEtiquetado)): ?>
                            <?php $selected = ($rowEti['codigo'] == $producto['etiquetado']) ? 'selected' : ''; ?>
                            <option value="<?= htmlspecialchars($rowEti['codigo']) ?>" <?= $selected ?>>
                                <?= htmlspecialchars($rowEti['descripcion']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Campo: Categoría -->
            <div class="form-group">
                <label for="categoria">Category Product:</label>
                <select id="categoria" name="categoria" required>
                    <?php if ($resultCategoria): ?>
                        <?php $resultCategoria->data_seek(0); ?>
                        <?php while ($rowCat = mysqli_fetch_assoc($resultCategoria)): ?>
                            <?php $selected = ($rowCat['codigo'] == $producto['categoria']) ? 'selected' : ''; ?>
                            <option value="<?= htmlspecialchars($rowCat['codigo']) ?>" <?= $selected ?>>
                                <?= htmlspecialchars($rowCat['descripcion']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Campo: Dimensiones y Peso -->
            <div class="form-group">
                <label for="alto">Height: (Meters)</label>
                <input type="number" step="any" id="alto" name="alto" value="<?= htmlspecialchars($producto['alto']) ?>" required>
            </div>
            <div class="form-group">
                <label for="ancho">Width: (Meters)</label>
                <input type="number" step="any" id="ancho" name="ancho" value="<?= htmlspecialchars($producto['ancho']) ?>" required>
            </div>
            <div class="form-group">
                <label for="largo">Length: (Meters)</label>
                <input type="number" step="any" id="largo" name="largo" value="<?= htmlspecialchars($producto['largo']) ?>" required>
            </div>
            <div class="form-group">
                <label for="peso">Weight: (Kilograms)</label>
                <input type="number" step="any" id="peso" name="peso" value="<?= htmlspecialchars($producto['peso']) ?>" required>
            </div>

            <!-- Botón: Guardar Cambios -->
            <button type="submit" name="accion" value="updateProduct" class="btn-guardar">Update</button>
        </form>
    </div>
    <?php
}

function vistaAddProduct($cliente) {
    global $db;

    // Consultar etiquetados y categorías para llenar el formulario
    $queryEtiquetado = "SELECT e.codigo, e.descripcion FROM etiquetado e";
    $resultEtiquetado = mysqli_query($db, $queryEtiquetado);

    $queryCategoria = "SELECT c.codigo, c.descripcion FROM cat_prod c";
    $resultCategoria = mysqli_query($db, $queryCategoria);

    // Mostrar el formulario para agregar un nuevo producto
    ?>
    <div class="form">
        <?php
        if (isset($_GET['status']) && $_GET['status'] === 'addedProduct'): ?>
        <p style="font-size:2rem; text-align: end; color: #57cf8b;">Product Added</p>
        <?php endif; ?>
        <h2>Add Product</h2>
        <form action="" method="POST">
            <!-- Campo: Nombre del Producto -->
            <div class="form-group">
                <label for="nombre">Product Name:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <!-- Campo: Descripción -->
            <div class="form-group">
                <label for="descripcion">Description:</label>
                <input type="text" id="descripcion" name="descripcion" required>
            </div>

            <!-- Campo: Etiquetado -->
            <div class="form-group">
                <label for="etiquetado">Product Tag:</label>
                <select id="etiquetado" name="etiquetado" required>
                    <?php if ($resultEtiquetado): ?>
                        <?php while ($rowEti = mysqli_fetch_assoc($resultEtiquetado)): ?>
                            <option value="<?= htmlspecialchars($rowEti['codigo']) ?>">
                                <?= htmlspecialchars($rowEti['descripcion']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Campo: Categoría -->
            <div class="form-group">
                <label for="categoria">Category Product:</label>
                <select id="categoria" name="categoria" required>
                    <?php if ($resultCategoria): ?>
                        <?php while ($rowCat = mysqli_fetch_assoc($resultCategoria)): ?>
                            <option value="<?= htmlspecialchars($rowCat['codigo']) ?>">
                                <?= htmlspecialchars($rowCat['descripcion']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Campo: Dimensiones y Peso -->
            <div class="form-group">
                <label for="alto">Height: (Meters)</label>
                <input type="number" step="any" id="alto" name="alto" required>
            </div>
            <div class="form-group">
                <label for="ancho">Width: (Meters)</label>
                <input type="number" step="any" id="ancho" name="ancho" required>
            </div>
            <div class="form-group">
                <label for="largo">Length: (Meters)</label>
                <input type="number" step="any" id="largo" name="largo" required>
            </div>
            <div class="form-group">
                <label for="peso">Weight: (Kilograms)</label>
                <input type="number" step="any" id="peso" name="peso" required>
            </div>

            <!-- Botón: Guardar Nuevo Producto -->
            <button type="submit" name="accion" value="addProduct" class="btn-guardar">Add Product</button>
        </form>
    </div>
    <?php
}
?>
