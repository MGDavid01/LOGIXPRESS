<?php
    if($mantenimientoTool == 'vehiculos'){
        if($herramienta == 'mandar'){
            // Obtener los vehículos desde la base de datos
            $queryVehiculos = "SELECT v.num,
                        v.numSerie,
                        v.kilometraje,
                        v.costoAcumulado,
                        v.categoriaVehiculo,
                        ma.nombre as Marca,
                        mo.nombre as Modelo
                        FROM vehiculo v
                        INNER JOIN marca ma ON v.marca = ma.codigo
                        INNER JOIN modelo mo ON v.modelo = mo.codigo
                        WHERE v.disponibilidad = 'DISPO'
                        ";
    
            $resultVehiculos = mysqli_query($db, $queryVehiculos);
        } else {
            // Obtener los vehículos desde la base de datos
            $queryVehiculos = "SELECT v.num,
                        v.numSerie,
                        v.kilometraje,
                        v.costoAcumulado,
                        v.categoriaVehiculo,
                        ma.nombre as Marca,
                        mo.nombre as Modelo
                        FROM vehiculo v
                        INNER JOIN marca ma ON v.marca = ma.codigo
                        INNER JOIN modelo mo ON v.modelo = mo.codigo
                        WHERE v.disponibilidad = 'MANTE'
                        ";
            $resultVehiculos = mysqli_query($db, $queryVehiculos);
        }
        // Obtener Categorías, Marcas y Modelos para los Filtros
        $queryCategorias = "SELECT DISTINCT codigo, descripcion FROM cat_vehi";
        $resultCategorias = mysqli_query($db, $queryCategorias);

        $queryMarcas = "SELECT DISTINCT ma.nombre FROM vehiculo v INNER JOIN marca ma ON v.marca = ma.codigo WHERE v.disponibilidad = 'DISPO'";
        $resultMarcas = mysqli_query($db, $queryMarcas);

        $queryModelos = "SELECT DISTINCT mo.nombre FROM vehiculo v INNER JOIN modelo mo ON v.modelo = mo.codigo WHERE v.disponibilidad = 'DISPO'";
        $resultModelos = mysqli_query($db, $queryModelos);
    } else {
        if ($herramienta == 'mandar') {
            // Obtener los remolques desde la base de datos (disponibles)
            $queryRemolque = "SELECT r.num,
                              r.numSerie,
                              r.costoAcumulado,
                              r.tipoRemolque,
                              ma.nombre as Marca,
                              mo.nombre as Modelo
                              FROM remolque r
                              INNER JOIN marca ma ON r.marca = ma.codigo
                              INNER JOIN modelo mo ON r.modelo = mo.codigo
                              WHERE r.disponibilidad = 'DISPO'
                              ";
        
            $resultoRemolques = mysqli_query($db, $queryRemolque);
        } else {
            // Obtener los remolques desde la base de datos (en mantenimiento)
            $queryRemolque = "SELECT r.num,
                              r.numSerie,
                              r.costoAcumulado,
                              r.tipoRemolque,
                              ma.nombre as Marca,
                              mo.nombre as Modelo
                              FROM remolque r
                              INNER JOIN marca ma ON r.marca = ma.codigo
                              INNER JOIN modelo mo ON r.modelo = mo.codigo
                              WHERE r.disponibilidad = 'MANTE'
                              ";
        
            $resultoRemolques = mysqli_query($db, $queryRemolque);
        }
        
        // Obtener Categorías, Marcas y Modelos para los Filtros
        
        // Suponiendo que tienes una tabla de categorías específica para remolques
        $queryCategorias = "SELECT DISTINCT codigo, descripcion FROM tipo_remolque";
        $resultCategorias = mysqli_query($db, $queryCategorias);
        
        // Obtener las marcas de los remolques disponibles
        $queryMarcas = "SELECT DISTINCT ma.nombre 
                        FROM remolque r 
                        INNER JOIN marca ma ON r.marca = ma.codigo 
                        WHERE r.disponibilidad = 'DISPO'";
        $resultMarcas = mysqli_query($db, $queryMarcas);
        
        // Obtener los modelos de los remolques disponibles
        $queryModelos = "SELECT DISTINCT mo.nombre 
                         FROM remolque r 
                         INNER JOIN modelo mo ON r.modelo = mo.codigo 
                         WHERE r.disponibilidad = 'DISPO'";
        $resultModelos = mysqli_query($db, $queryModelos);        
    }
    
    $filtros = [];
    
?>
<div class="filters-title-back">
    <div class="status">
        <?php
        $status = filter_input(INPUT_GET, 'status');
        if ($status == 'success') {
            ?>
            <script>
                // Recargar la página para actualizar el listado (vehículo/remolque eliminado)
                window.addEventListener('load', () => {
                    // Redirigir eliminando el parámetro `status` de la URL
                    setTimeout(() => {
                        const url = new URL(window.location.href);
                        url.searchParams.delete('status');
                        window.history.replaceState({}, document.title, url.toString());
                        // Mostrar el mensaje después de que la URL esté limpia
                        document.getElementById('successMessage').innerText = 'Registration Successful';
                    }, 500); // Esperar 500ms para recargar y eliminar el parámetro
                });
            </script>
            <?php
        
        ?>
        <!-- Mensaje HTML que se mostrará después de la actualización -->
        <h2 id="successMessage"></h2>
        <?php
        }else{
            ?><h2></h2><?php
        }
        ?>
        <button onclick="removerHerramienta()" class="btn-back">Go Back</button>
    </div>
    <?php
        if($mantenimientoTool == 'vehiculos'){
            if ($herramienta == 'mandar') {
                echo '<h1 style="margin:0rem 0rem 1rem 0rem;">Vehicles Available for Maintenance</h1>';
            }else{
                echo '<h1 style="margin:0rem 0rem 1rem 0rem;">Vehicles Under Maintenance</h1>';
            }
        } else {
            if ($herramienta == 'mandar') {
                echo '<h1 style="margin:0rem 0rem 1rem 0rem;">Trailers Available for Maintenance</h1>';
            }else{
                echo '<h1 style="margin:0rem 0rem 1rem 0rem;">Trailers Under Maintenance</h1>';
            }
        }
    ?>

    <div class="filters-container">
        <div class="filter">
            <label for="categoryFilter">Category:</label>
            <select id="categoryFilter" onchange="applyFilters()">
                <option value="">All Categories</option>
                <?php while ($row = mysqli_fetch_assoc($resultCategorias)) { ?>
                    <option value="<?= htmlspecialchars($row['codigo']) ?>"><?= htmlspecialchars($row['descripcion']) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="filter">
            <label for="brandFilter">Brand:</label>
            <select id="brandFilter" onchange="applyFilters()">
                <option value="">All Brands</option>
                <?php while ($row = mysqli_fetch_assoc($resultMarcas)) { ?>
                    <option value="<?= htmlspecialchars($row['nombre']) ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="filter">
            <label for="modelFilter">Model:</label>
            <select id="modelFilter" onchange="applyFilters()">
                <option value="">All Models</option>
                <?php while ($row = mysqli_fetch_assoc($resultModelos)) { ?>
                    <option value="<?= htmlspecialchars($row['nombre']) ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="filter">
            <label for="searchInput">Search:</label>
            <input type="text" id="searchInput" placeholder="Search by Serial Number..." onkeyup="applyFilters()">
        </div>
    </div>
</div>
