<?php
require_once('includes/config/conection.php');

// Verificar conexión
$db = connectTo2DB();
if (!$db) {
    die("Error en la conexión a la base de datos.");
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
    $apiKey = 'a57ab80f0d8d456396660e4cb8856ec7'; // Reemplaza con tu clave API de OpenCage
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
    } else {
        echo "Sin resultados para la dirección: $direccion<br>";
    }

    return null;
}

// Función para obtener puntos de origen y destino
function obtenerPuntosEntrega($entrega_id) {
    global $db;
    // Consultar el punto de origen
    $queryOrigen = "
        SELECT u.codigo, u.nombreCalle, u.numCalle, u.colonia, u.codigoPostal
        FROM ubi_entrega_salida us
        INNER JOIN ubicacion u ON us.ubicacion = u.codigo
        WHERE us.entrega = $entrega_id
    ";
    $resultOrigen = $db->query($queryOrigen);
    if (!$resultOrigen || $resultOrigen->num_rows == 0) {
        die("No se encontró un punto de origen.");
    }
    $origen = $resultOrigen->fetch_assoc();
    $origen['coordenadas'] = obtenerCoordenadas(completarDireccion($origen));

    // Consultar los puntos de destino
    $queryDestinos = "
        SELECT u.codigo, u.nombreCalle, u.numCalle, u.colonia, u.codigoPostal
        FROM ubi_entrega_llegada ul
        INNER JOIN ubicacion u ON ul.ubicacion = u.codigo
        WHERE ul.entrega = $entrega_id
    ";
    $resultDestinos = $db->query($queryDestinos);
    if (!$resultDestinos) {
        die("Error en la consulta de puntos de destino: " . $db->error);
    }
    $destinos = [];
    while ($row = $resultDestinos->fetch_assoc()) {
        $row['coordenadas'] = obtenerCoordenadas(completarDireccion($row));
        if ($row['coordenadas']) {
            $destinos[] = $row;
        }
    }

    return [
        'origen' => $origen,
        'destinos' => $destinos
    ];
}

// Función para calcular la distancia entre dos puntos
function calcularDistancia($coord1, $coord2) {
    $R = 6371; // Radio de la Tierra en km
    $lat1 = deg2rad($coord1['lat']);
    $lon1 = deg2rad($coord1['lng']);
    $lat2 = deg2rad($coord2['lat']);
    $lon2 = deg2rad($coord2['lng']);
    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;

    $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $R * $c; // Distancia en km
}

// Función para calcular la ruta óptima
function calcularRuta($origen, &$destinos) {
    $rutaDescripcion = [$origen['codigo']];
    $distanciaTotal = 0;
    $rutasCalculadas = [];

    while (!empty($destinos)) {
        $distancias = [];
        foreach ($destinos as $index => $destino) {
            $distancias[$index] = calcularDistancia($origen['coordenadas'], $destino['coordenadas']);
        }
        asort($distancias);
        $masCercanoIndex = array_key_first($distancias);
        $masCercano = $destinos[$masCercanoIndex];

        $distanciaTotal += $distancias[$masCercanoIndex];
        $rutasCalculadas[] = [
            'inicio' => $origen,
            'fin' => $masCercano,
            'distancia' => $distancias[$masCercanoIndex]
        ];

        $rutaDescripcion[] = $masCercano['codigo'];
        $origen = $masCercano;
        unset($destinos[$masCercanoIndex]);
    }

    return [
        'descripcion' => implode(' -> ', $rutaDescripcion),
        'distanciaTotal' => $distanciaTotal,
        'rutasCalculadas' => $rutasCalculadas
    ];
}



$puntos = obtenerPuntosEntrega($_SESSION['entrega']);
$ruta = calcularRuta($puntos['origen'], $puntos['destinos']);

// Insertar la ruta en la base de datos con mysqli
$queryInsertRuta = $db->prepare("
    INSERT INTO ruta (descripcion, distanciaTotal, tiempoEstimado)
    VALUES (?, ?, ?)
");
$tiempoEstimado = ($ruta['distanciaTotal'] / 60) * 60; // Estimado en minutos
$queryInsertRuta->bind_param(
    'sdd', 
    $ruta['descripcion'], 
    $ruta['distanciaTotal'], 
    $tiempoEstimado
);
$queryInsertRuta->execute();

// Obtener el ID de la ruta insertada
$rutaId = $db->insert_id;

function obtenerDistanciaYTiempo($waypoints) {
    $apiKey = '482c7aa9-7008-4be2-ab73-f76ee2d2b5fa'; // Tu clave API de GraphHopper
    $points = implode('&point=', array_map(fn($wp) => "{$wp['lat']},{$wp['lng']}", $waypoints));
    $url = "https://graphhopper.com/api/1/route?key=$apiKey&point=$points&vehicle=car&type=json&points_encoded=false";

    $response = @file_get_contents($url);

    if ($response === FALSE) {
        echo "Error al contactar la API de GraphHopper<br>";
        return null;
    }

    // Inspecciona la respuesta completa
    $data = json_decode($response, true);
    echo "<pre>";
    print_r($data);
    echo "</pre>";

    if (isset($data['paths'][0])) {
        return [
            'distancia' => round($data['paths'][0]['distance'] / 1000, 2), // Convertir metros a kilómetros
            'tiempo' => round($data['paths'][0]['time'] / 60000, 2), // Convertir milisegundos a minutos
            'geoJson' => $data['paths'][0]['points'] // GeoJSON para el mapa
        ];
    }

    echo "No se encontraron rutas válidas<br>";
    return null;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <title>Mapa de Entregas</title>
</head>
<body>
    <div id="map" style="height: 600px;"></div>
    <script>
        const map = L.map('map').setView([<?= $puntos['origen']['coordenadas']['lat'] ?>, <?= $puntos['origen']['coordenadas']['lng'] ?>], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        const waypoints = [];
        const apiKey = '482c7aa9-7008-4be2-ab73-f76ee2d2b5fa'; // Reemplaza con tu clave API de GraphHopper

        // Agregar el origen como primer punto
        waypoints.push([<?= $puntos['origen']['coordenadas']['lat'] ?>, <?= $puntos['origen']['coordenadas']['lng'] ?>]);
        L.marker([<?= $puntos['origen']['coordenadas']['lat'] ?>, <?= $puntos['origen']['coordenadas']['lng'] ?>])
            .addTo(map)
            .bindPopup('Origen: <?= completarDireccion($puntos['origen']) ?>');

        // Agregar puntos de destino
        <?php foreach ($ruta['rutasCalculadas'] as $segmento): ?>
        waypoints.push([<?= $segmento['fin']['coordenadas']['lat'] ?>, <?= $segmento['fin']['coordenadas']['lng'] ?>]);
        L.marker([<?= $segmento['fin']['coordenadas']['lat'] ?>, <?= $segmento['fin']['coordenadas']['lng'] ?>])
            .addTo(map)
            .bindPopup('Destino: <?= $segmento['fin']['codigo'] ?>');
        <?php endforeach; ?>

        // Generar la ruta con GraphHopper
        const routeUrl = `https://graphhopper.com/api/1/route?key=${apiKey}&point=${waypoints.map(p => p.join(',')).join('&point=')}&vehicle=car&type=json&points_encoded=false`;

        fetch(routeUrl)
            .then(response => response.json())
            .then(data => {
                if (data.paths && data.paths.length > 0) {
                    const geoJson = data.paths[0].points;
                    L.geoJSON(geoJson).addTo(map);
                } else {
                    console.error('No se encontraron rutas válidas:', data);
                }
            })
            .catch(error => console.error('Error al obtener la ruta:', error));
    </script>
</body>
</html>
