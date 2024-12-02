<?php
    $filtros = [];
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
                ORDER BY FIELD(v.categoriaVehiculo, 
                    'FURGG', 
                    'FURGR', 
                    'CARTO', 
                    'CARCG', 
                    'CARCR', 
                    'CAMRP', 
                    'CAMAP'
                )";

    $resultVehiculos = mysqli_query($db, $queryVehiculos);

    // Obtener Categorías, Marcas y Modelos para los Filtros
    $queryCategorias = "SELECT DISTINCT codigo, descripcion FROM cat_vehi";
    $resultCategorias = mysqli_query($db, $queryCategorias);

    $queryMarcas = "SELECT DISTINCT ma.nombre FROM vehiculo v INNER JOIN marca ma ON v.marca = ma.codigo WHERE v.disponibilidad = 'DISPO'";
    $resultMarcas = mysqli_query($db, $queryMarcas);

    $queryModelos = "SELECT DISTINCT mo.nombre FROM vehiculo v INNER JOIN modelo mo ON v.modelo = mo.codigo WHERE v.disponibilidad = 'DISPO'";
    $resultModelos = mysqli_query($db, $queryModelos);

    echo '<button onclick="removeParam()" class="btn-back">Regresar</button>';
    echo '<h1 style="margin:0rem 0rem 1rem 0rem;">Vehicles Available for Maintenance</h1>';

    // Filtros
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