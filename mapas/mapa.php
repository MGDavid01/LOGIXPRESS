<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../includes/config/conection.php');

// Verificar conexión
$db = connectTo2DB();
if (!$db) {
    die("Error en la conexión a la base de datos.");
}

// Validar parámetros
if (!isset($_SESSION['user_id']) || !isset($_GET['entrega'])) {
    die("No se ha definido el ID de entrega o el usuario no está autenticado.");
}

$empleado_id = intval($_SESSION['user_id']);
$entrega_id = intval($_GET['entrega']);

// Validar que la entrega pertenece al empleado logueado
$queryValidar = "
    SELECT entrega 
    FROM entre_empleado
    WHERE entrega = ? AND empleado = ?
    LIMIT 1
";
$stmt = $db->prepare($queryValidar);
$stmt->bind_param('ii', $entrega_id, $empleado_id);
$stmt->execute();
$resultValidar = $stmt->get_result();

if ($resultValidar->num_rows === 0) {
    die("No tiene permisos para ver esta entrega.");
}

// Función para completar direcciones
function completarDireccion($ubicacion) {
    $ciudad = "Tijuana";
    $estado = "Baja California";
    $pais = "México";

    return "{$ubicacion['nombreCalle']} {$ubicacion['numCalle']}, {$ubicacion['colonia']}, {$ubicacion['codigoPostal']}, $ciudad, $estado, $pais";
}

// Función para obtener coordenadas desde OpenCage Geocoder
function obtenerCoordenadas($direccion) {
    $apiKey = 'a57ab80f0d8d456396660e4cb8856ec7';
    $url = "https://api.opencagedata.com/geocode/v1/json?q=" . urlencode($direccion) . "&key=$apiKey";

    $response = @file_get_contents($url);

    if ($response === FALSE) {
        echo "Error al contactar la API para la dirección: $direccion<br>";
        return null;
    }

    $data = json_decode($response, true);

    if (isset($data['results'][0]['geometry'])) {
        return [
            'lat' => $data['results'][0]['geometry']['lat'],
            'lng' => $data['results'][0]['geometry']['lng']
        ];
    }

    return null;
}

// Función para obtener puntos de entrega
function obtenerPuntosEntrega($entrega_id) {
    global $db;

    // Consultar punto de salida
    $querySalida = "
        SELECT u.num, u.nombreCalle, u.numCalle, u.colonia, u.codigoPostal
        FROM ubi_entrega_salida us
        INNER JOIN ubicacion u ON us.ubicacion = u.num
        WHERE us.entrega = ?
    ";
    $stmt = $db->prepare($querySalida);
    $stmt->bind_param('i', $entrega_id);
    $stmt->execute();
    $resultSalida = $stmt->get_result();

    if ($resultSalida->num_rows == 0) {
        die("No se encontró un punto de salida.");
    }
    $salida = $resultSalida->fetch_assoc();
    $salida['coordenadas'] = obtenerCoordenadas(completarDireccion($salida));

    // Consultar puntos de llegada
    $queryLlegadas = "
        SELECT u.num, u.nombreCalle, u.numCalle, u.colonia, u.codigoPostal
        FROM ubi_entrega_llegada ul
        INNER JOIN ubicacion u ON ul.ubicacion = u.num
        WHERE ul.entrega = ?
    ";
    $stmt = $db->prepare($queryLlegadas);
    $stmt->bind_param('i', $entrega_id);
    $stmt->execute();
    $resultLlegadas = $stmt->get_result();

    $llegadas = [];
    while ($row = $resultLlegadas->fetch_assoc()) {
        $row['coordenadas'] = obtenerCoordenadas(completarDireccion($row));
        if ($row['coordenadas']) {
            $llegadas[] = $row;
        }
    }

    return [
        'salida' => $salida,
        'llegadas' => $llegadas
    ];
}

// Función para verificar si ya existe una ruta
function verificarRutaExistente($descripcion) {
    global $db;

    $query = "
        SELECT num, distanciaTotal, tiempoEstimado, geoJson
        FROM ruta
        WHERE descripcion = ?
        LIMIT 1
    ";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $descripcion);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc(); // Ruta encontrada
    }

    return null; // No existe la ruta
}

// Función para calcular ruta desde la API de GraphHopper
function obtenerRutaDesdeAPI($waypoints) {
    $apiKey = '482c7aa9-7008-4be2-ab73-f76ee2d2b5fa'; // Reemplaza con tu clave API de GraphHopper
    $points = implode('&point=', array_map(fn($wp) => "{$wp['lat']},{$wp['lng']}", $waypoints));
    $url = "https://graphhopper.com/api/1/route?key=$apiKey&point=$points&vehicle=car&type=json&points_encoded=false";

    $response = @file_get_contents($url);

    if ($response === FALSE) {
        echo "Error al contactar la API para la ruta<br>";
        return null;
    }

    $data = json_decode($response, true);

    if (isset($data['paths'][0])) {
        return [
            'distancia' => round($data['paths'][0]['distance'] / 1000, 2), // Convertir metros a kilómetros
            'tiempo' => round($data['paths'][0]['time'] / 60000, 2), // Convertir milisegundos a minutos
            'geoJson' => $data['paths'][0]['points'] // GeoJSON para el mapa
        ];
    }

    return null;
}

// Función para guardar una nueva ruta en la base de datos
function guardarRuta($descripcion, $distancia, $tiempo, $geoJson) {
    global $db;

    $query = "
        INSERT INTO ruta (descripcion, distanciaTotal, tiempoEstimado, geoJson)
        VALUES (?, ?, ?, ?)
    ";
    $stmt = $db->prepare($query);
    $stmt->bind_param('sdss', $descripcion, $distancia, $tiempo, $geoJson);
    $stmt->execute();

    return $db->insert_id;
}

// Obtener puntos de entrega
$puntos = obtenerPuntosEntrega($entrega_id);

// Generar descripción con los códigos de las ubicaciones
$descripcionRuta = implode(' -> ', array_merge(
    [$puntos['salida']['num']],
    array_map(fn($llegada) => $llegada['num'], $puntos['llegadas'])
));

// Verificar si ya existe la ruta
$rutaExistente = verificarRutaExistente($descripcionRuta);

if ($rutaExistente) {
    $distanciaTotal = $rutaExistente['distanciaTotal'];
    $tiempoFormato = $rutaExistente['tiempoEstimado'];
    $geoJson = $rutaExistente['geoJson'];
} else {
    $waypoints = array_merge(
        [['lat' => $puntos['salida']['coordenadas']['lat'], 'lng' => $puntos['salida']['coordenadas']['lng']]],
        array_map(fn($llegada) => $llegada['coordenadas'], $puntos['llegadas'])
    );

    $rutaInfo = obtenerRutaDesdeAPI($waypoints);
    $distanciaTotal = $rutaInfo['distancia'];

    // Solución al problema del float
    $tiempoTotal = round($rutaInfo['tiempo']); // Redondear a minutos

    $tiempoFormato = sprintf(
        '%02d:%02d:%02d',
        floor($tiempoTotal / 60), // Horas completas
        $tiempoTotal % 60,        // Minutos restantes
        0                         // Segundos siempre 0
    );

    $geoJson = json_encode($rutaInfo['geoJson']);
    guardarRuta($descripcionRuta, $distanciaTotal, $tiempoFormato, $geoJson);
}
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <title>Mapa de Rutas</title>
    <div class="info-mapa-mapa">
        <!-- Información -->
        <div class="info-container">
            <div class="info-header">Detalles Ruta De La Entrega</div>
        <table class="info-table">
            <thead>
                <tr>
                    <th>Información General</th>
                    <th>Punto de Origen</th>
                    <th>Puntos de Destino</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <p class="info-highlight">Distancia Total: <br><?= $distanciaTotal ?> km</p>
                        <p class="info-highlight">Tiempo Estimado: <br><?= $tiempoFormato ?></p>
                    </td>
                    <td>
                        <p class="info-highlight"><?= completarDireccion($puntos['salida']) ?></p>
                    </td>
                    <td>
                        <?php foreach ($puntos['llegadas'] as $llegada): ?>
                            <p class="info-highlight"> - <?= completarDireccion($llegada) ?></p>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
        <!-- Mapa -->
        <div id="map">

        </div>
    </div>

    <script>
        // Inicializar el mapa
        const map = L.map('map').setView([<?= $puntos['salida']['coordenadas']['lat'] ?>, <?= $puntos['salida']['coordenadas']['lng'] ?>], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        // Ícono personalizado para el punto de origen
        const origenIcon = L.icon({
            iconUrl: 'imagenes/OrigenIcon.png', // URL del ícono
            iconSize: [32, 32], // Tamaño del ícono
            iconAnchor: [16, 32], // Punto de anclaje del ícono
            popupAnchor: [0, -32] // Punto de anclaje del popup
        });

        // Ícono personalizado para los puntos de destino
        const destinoIcon = L.icon({
            iconUrl: 'imagenes/DestinoIcon.png', // URL del ícono
            iconSize: [32, 32], 
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        // Agregar marcador para el punto de salida
        L.marker([<?= $puntos['salida']['coordenadas']['lat'] ?>, <?= $puntos['salida']['coordenadas']['lng'] ?>], { icon: origenIcon })
            .addTo(map)
            .bindPopup('Punto de Salida')
            .openPopup();

        // Agregar marcadores para los puntos de llegada
        <?php foreach ($puntos['llegadas'] as $llegada): ?>
        L.marker([<?= $llegada['coordenadas']['lat'] ?>, <?= $llegada['coordenadas']['lng'] ?>], { icon: destinoIcon })
            .addTo(map)
            .bindPopup('Punto de Llegada: <?= $llegada['num'] ?>');
        <?php endforeach; ?>

        // Dibujar la ruta si hay GeoJSON
        <?php if (!empty($geoJson)): ?>
        const routeGeoJson = <?= $geoJson ?>;
        L.geoJSON(routeGeoJson, {
            style: {
                color: '#007bff', // Color de la línea de la ruta
                weight: 4
            }
        }).addTo(map);
        <?php endif; ?>
    </script>