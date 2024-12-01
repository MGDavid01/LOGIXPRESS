<?php
    $mantenimientoTool = filter_input(INPUT_GET, 'mantenimiento');
    switch ($mantenimientoTool) {
        case 'vehiculos':
            
            ?>  <link rel="stylesheet" href="css/menuCHD/vistaVehiculosMantenimiento.css">
                <script>
                function applyFilters() {
                    const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
                    const brandFilter = document.getElementById('brandFilter').value.toLowerCase();
                    const modelFilter = document.getElementById('modelFilter').value.toLowerCase();
                    const searchInput = document.getElementById('searchInput').value.toLowerCase();

                    const cards = document.querySelectorAll('#vehicleCards .card');

                    cards.forEach(card => {
                        const category = card.getAttribute('data-category').toLowerCase();
                        const brand = card.getAttribute('data-brand').toLowerCase();
                        const model = card.getAttribute('data-model').toLowerCase();
                        const serial = card.getAttribute('data-serial').toLowerCase();

                        let showCard = true;

                        if (categoryFilter && category !== categoryFilter) {
                            showCard = false;
                        }

                        if (brandFilter && brand !== brandFilter) {
                            showCard = false;
                        }

                        if (modelFilter && model !== modelFilter) {
                            showCard = false;
                        }

                        if (searchInput && !serial.includes(searchInput)) {
                            showCard = false;
                        }

                        if (showCard) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }
            </script>
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
            $queryCategorias = "SELECT DISTINCT categoriaVehiculo FROM vehiculo WHERE disponibilidad = 'DISPO'";
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
                            <option value="<?= htmlspecialchars($row['categoriaVehiculo']) ?>"><?= htmlspecialchars($row['categoriaVehiculo']) ?></option>
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
            <?php
            echo '<div id="vehicleCards" class="cards-container">';
            while ($vehiculo = mysqli_fetch_assoc($resultVehiculos)) {
                ?>
                <div class="card" data-category="<?= htmlspecialchars($vehiculo['categoriaVehiculo']) ?>" data-brand="<?= htmlspecialchars($vehiculo['Marca']) ?>" data-model="<?= htmlspecialchars($vehiculo['Modelo']) ?>" data-serial="<?= htmlspecialchars($vehiculo['numSerie']) ?>">
                        <div class="content-img">
                            <img src="imagenes/vehiculo.png" alt="Vehículo">
                        </div>
                    <div class="card-details">
                        <h3>Número de Serie: <?= htmlspecialchars($vehiculo['numSerie']); ?></h3>
                        <p>Marca: <?= htmlspecialchars($vehiculo['Marca']); ?></p>
                        <p>Modelo: <?= htmlspecialchars($vehiculo['Modelo']); ?></p>
                        <button onclick="enviarMantenimiento('<?= $vehiculo['num'] ?>')" class="btn-send-maintenance">Enviar a Mantenimiento</button>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
            break;

        case 'remolques':
            // Obtener los remolques desde la base de datos
            $queryRemolques = "SELECT * FROM remolque WHERE disponibilidad = 'DISPO'";
            $resultRemolques = mysqli_query($db, $queryRemolques);
            
            echo '<button onclick="removeParam()" class="btn-back">Regresar</button>';
            echo '<h1>Remolques Disponibles para Mantenimiento</h1>';
            echo '<div class="cards-container">';
            
            while ($remolque = mysqli_fetch_assoc($resultRemolques)) {
                ?>
                <div class="card">
                    <div class="content-img">
                        <img src="imagenes/remolque.png" alt="Remolque">
                    </div>
                    <div class="card-details">
                        <h3>Número de Serie: <?= htmlspecialchars($remolque['numSerie']); ?></h3>
                        <p>Capacidad de Carga: <?= htmlspecialchars($remolque['capacidadCarga']); ?> kg</p>
                        <button onclick="enviarMantenimiento('<?= $remolque['num'] ?>')" class="btn-send-maintenance">Enviar a Mantenimiento</button>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
            break;

        default:
            ?>
            <div class="title-mainte">
                <h1>Send to Maintenance</h1>
            </div>
            <div class="content-card">
                <button id="vehiculos" onclick="mostrarRecurso('vehiculos')">
                    <div class="card">
                        <div class="content-img">
                            <img src="imagenes/vehiculo.png" alt="Vehículo">
                        </div>
                        <h2>Vehículos</h2>
                    </div>
                </button>
                <button id="remolques" onclick="mostrarRecurso('remolques')">
                    <div class="card">
                        <div class="content-img">
                            <img src="imagenes/remolque.png" alt="Remolque">
                        </div>
                        <h2>Remolques</h2>
                    </div>
                </button>
            </div>
            <?php
            break;
    }
?>