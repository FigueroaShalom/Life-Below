<?php
// MOCK API de datos marinos actualizada con Coordenadas Geográficas (Latitud, Longitud)
$mock_marine_data = [
    [
        'ocean' => 'pacifico', 'name' => 'Tortuga Verde', 'scientific_name' => 'Chelonia mydas',
        'place' => 'Gran Barrera de Coral', 'image' => 'uploads/pacific_turtle.png',
        'desc' => 'Majestuosa tortuga marina nadando pacíficamente sobre arrecifes de coral bañados en luz.',
        'lat' => -18.28, 'lng' => 147.69
    ],
    [
        'ocean' => 'pacifico', 'name' => 'Pez Payaso Neón', 'scientific_name' => 'Amphiprioninae',
        'place' => 'Arrecifes de la Polinesia Francesa', 'image' => 'uploads/pacific_clownfish.png',
        'desc' => 'Pequeño pez tropical de colores vibrantes escondido entre los tentáculos de una anémona verde.',
        'lat' => -17.53, 'lng' => -149.56
    ],
    [
        'ocean' => 'pacifico', 'name' => 'Gran Tiburón Blanco', 'scientific_name' => 'Carcharodon carcharias',
        'place' => 'Costas de Baja California', 'image' => 'uploads/atlantic_shark.png',
        'desc' => 'Imponente depredador apex patrullando el inmenso Océano Pacífico.',
        'lat' => 29.50, 'lng' => -123.00
    ],
    [
        'ocean' => 'atlantico', 'name' => 'Tiburón Blanco Atlántico', 'scientific_name' => 'Carcharodon carcharias',
        'place' => 'Gansbaai, Sudáfrica', 'image' => 'uploads/atlantic_shark.png',
        'desc' => 'Poderoso y majestuoso tiburón blanco experto en emboscadas verticales.',
        'lat' => -34.58, 'lng' => 19.35
    ],
    [
        'ocean' => 'atlantico', 'name' => 'Medusa Bioluminiscente', 'scientific_name' => 'Aurelia aurita',
        'place' => 'Afloramiento del Atlántico Norte', 'image' => 'uploads/atlantic_jellyfish.png',
        'desc' => 'Criatura etérea que emite luz azul neón en la oscuridad extrema del océano profundo.',
        'lat' => 45.00, 'lng' => -35.00
    ],
    [
        'ocean' => 'indico', 'name' => 'Mantarraya Gigante', 'scientific_name' => 'Mobula birostris',
        'place' => 'Archipiélago de las Maldivas', 'image' => 'uploads/indian_manta.png',
        'desc' => 'Un gigante gentil que planea elegantemente cruzando los rayos de sol tropicales.',
        'lat' => 3.20, 'lng' => 73.22
    ],
    [
        'ocean' => 'indico', 'name' => 'Pez León', 'scientific_name' => 'Pterois volitans',
        'place' => 'Costas de Madagascar', 'image' => 'uploads/pacific_clownfish.png', 
        'desc' => 'Hermoso pez de aletas largas multicolores que parece una obra de arte viviente.',
        'lat' => -18.76, 'lng' => 50.10
    ],
    [
        'ocean' => 'artico', 'name' => 'Delfín Beluga', 'scientific_name' => 'Delphinapterus leucas',
        'place' => 'Mar de Bahía de Baffin', 'image' => 'uploads/atlantic_jellyfish.png', 
        'desc' => 'Curiosa y juguetona ballena blanca asomándose bajo el espeso hielo polar.',
        'lat' => 73.50, 'lng' => -68.00
    ],
    [
        'ocean' => 'artico', 'name' => 'Tiburón de Groenlandia', 'scientific_name' => 'Somniosus microcephalus',
        'place' => 'Fosas heladas de Groenlandia', 'image' => 'uploads/atlantic_shark.png',
        'desc' => 'Anciano gigante que puede vivir siglos y viaja por las oscuras aguas bajo cero.',
        'lat' => 68.00, 'lng' => -30.00
    ],
    [
        'ocean' => 'antartico', 'name' => 'Pingüino Emperador', 'scientific_name' => 'Aptenodytes forsteri',
        'place' => 'Plataforma de Hielo Ross', 'image' => 'uploads/pacific_turtle.png', 
        'desc' => 'Aves adaptadas a entornos letales que en el agua se transforman en rápidos torpedos.',
        'lat' => -73.00, 'lng' => 170.00
    ],
    [
        'ocean' => 'antartico', 'name' => 'Mantarraya Austral', 'scientific_name' => 'Mobula birostris sub',
        'place' => 'Pasaje de Drake', 'image' => 'uploads/indian_manta.png', 
        'desc' => 'Viajera incansable que tolera los mares más agitados en los confines del sur mundial.',
        'lat' => -60.00, 'lng' => -65.00
    ]
];

$oceans_nav = [
    'pacifico'  => ['label' => 'Pacífico',  'lat' => 0.0, 'lng' => -160.0, 'zoom' => 3],
    'atlantico' => ['label' => 'Atlántico', 'lat' => 15.0, 'lng' => -40.0, 'zoom' => 3],
    'indico'    => ['label' => 'Índico',    'lat' => -10.0, 'lng' => 70.0, 'zoom' => 4],
    'artico'    => ['label' => 'Ártico',    'lat' => 75.0, 'lng' => -20.0, 'zoom' => 3],
    'antartico' => ['label' => 'Antártico', 'lat' => -65.0, 'lng' => 45.0, 'zoom' => 3],
];

?>

<!-- Incluimos Leaflet.js para mapas interactivos realistas de alto rendimiento -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<style>
/* Contenedor principal que abarca toda la pantalla sin barras de scroll */
.hy-gallery-wrap { 
    width: 100%; max-width: 100%; margin: 0; padding: 0;
    height: calc(100vh - 65px); 
    position: relative; overflow: hidden;
    display: flex; flex-direction: column;
}

/* El elemento del mapa satelital */
#leaflet-map {
    width: 100%;
    flex: 1; /* Ocupa todo el espacio dinámico disponible */
    background: #000;
    z-index: 1;
}

/* Interfaz Flotante Superior (Controles de Navegación) */
.map-ui-overlay {
    position: absolute; top: 20px; left: 50%; transform: translateX(-50%);
    background: rgba(255, 255, 255, 0.95);
    padding: 12px 20px; border-radius: 50px;
    box-shadow: 0 10px 30px rgba(0,40,80,0.3);
    z-index: 1000; display: flex; gap: 8px; align-items: center;
    backdrop-filter: blur(8px);
}
@media (max-width: 768px) {
    .map-ui-overlay { flex-wrap: wrap; width: 90%; justify-content: center; border-radius: 16px; padding: 15px;}
}

.map-ui-title {
    font-family: 'Nunito', sans-serif; font-weight: 900;
    font-size: 1.1rem; color: #001828; margin-right: 15px;
}

.ocean-btn {
    background: transparent; border: 2px solid rgba(0,119,190,0.2);
    color: #0077be; padding: 6px 14px; border-radius: 50px;
    font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 0.85rem;
    cursor: pointer; transition: all 0.3s;
}
.ocean-btn:hover, .ocean-btn.active {
    background: #0077be; color: #fff; border-color: #0077be;
    box-shadow: 0 4px 15px rgba(0,119,190,0.4);
}

.btn-volver {
    background: #ff9800; border: none; color: #fff;
    padding: 6px 14px; border-radius: 50px;
    font-family: 'Nunito', sans-serif; font-weight: 900; font-size: 0.85rem;
    cursor: pointer; transition: opacity 0.3s, transform 0.3s;
    opacity: 0; pointer-events: none; transform: scale(0.8);
    margin-left: 10px;
}
.btn-volver.show {
    opacity: 1; pointer-events: auto; transform: scale(1);
    box-shadow: 0 4px 15px rgba(255,152,0,0.4);
}

/* Personalización de los "Marcadores" en el mapa (burbujitas de avatares) */
.custom-leaflet-marker {
    background: transparent;
    border: none;
}
.creature-avatar {
    width: 48px; height: 48px;
    border-radius: 50%;
    border: 3px solid #fff;
    background-size: cover; background-position: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.4);
    transition: transform 0.3s, border-color 0.3s;
    cursor: pointer;
}
.creature-avatar:hover {
    transform: scale(1.2);
    border-color: #0cebeb;
    animation: none;
}

/* Estilos del Popup de Leaflet. 
   Leaflet por defecto evita que los popups se salgan de la pantalla! */
.leaflet-popup-content-wrapper {
    background: #fff; border-radius: 16px; 
    padding: 0; overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,30,60,0.3);
    border: 1px solid rgba(0,120,190,0.1);
}
.leaflet-popup-tip-container { display: none; } /* Ocultar la flechita blanca pequeña por defecto para que se vea más limpio */

.creature-popup {
    width: 260px; font-family: 'Nunito', sans-serif;
}
.creature-popup-img {
    width: 100%; height: 130px; object-fit: cover; display: block; background: #eee;
}
.creature-popup-body {
    padding: 14px;
}
.creature-popup-title {
    font-weight: 900; font-size: 1.1rem; color: #001828; margin-bottom: 2px; line-height: 1.2;
}
.creature-popup-sci {
    font-style: italic; font-size: 0.8rem; color: #5a7a9a; margin-bottom: 8px;
}
.creature-popup-desc {
    font-size: 0.85rem; color: #4a6a8a; line-height: 1.4; margin-bottom: 12px;
}
.creature-popup-loc {
    font-size: 0.8rem; font-weight: 800; color: #0077be;
    display: inline-block; padding: 4px 10px; background: rgba(0,119,190,0.1); border-radius: 50px;
}
</style>

<div class="hy-gallery-wrap">
    
    <!-- Controles Dinámicos Superiores -->
    <div class="map-ui-overlay">
        <div class="map-ui-title">🌍 Explora:</div>
        <?php foreach ($oceans_nav as $key => $o): ?>
            <button class="ocean-btn" 
                    onclick="flyToOcean(<?php echo $o['lat']; ?>, <?php echo $o['lng']; ?>, <?php echo $o['zoom']; ?>, '<?php echo $key; ?>', this)">
                <?php echo htmlspecialchars($o['label']); ?>
            </button>
        <?php endforeach; ?>
        <button class="btn-volver" id="btn-reset" onclick="resetMap()">🌎 Global</button>
    </div>

    <!-- El lienzo acelerado por hardware de Leaflet -->
    <div id="leaflet-map"></div>

</div>

<script>
    // Variables Puras
    const data = <?php echo json_encode($mock_marine_data); ?>;
    const markersLayer = L.layerGroup(); // Grupo para poder borrar marcadores si hace falta
    let map;

    // Inicializar el mapa de Leaflet
    function initMap() {
        // Configuramos la vista global inicial
        // Leaflet.js está diseñado para NO tener los bugs de salir de pantalla o rendimiento
        map = L.map('leaflet-map', {
            zoomControl: false, // Ocultar +/- por defecto para no romper estética (lo añadiremos abajo estéticamente)
            maxBoundsViscosity: 1.0, // Que rebote suave en bordes
            worldCopyJump: true // Si viajas este-oeste infinito, mueve los puntos fluidamente
        }).setView([20, 0], 2); // (Lat, Lng), Zoom global

        // Añadimos el Control de Zoom en una esquina discreta inferior derecha
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Agregamos la impresionante Capa Satelital Fotográfica de Esri World Imagery
        // (Librería PÚBLICA e irrestricta equivalente al modo satélite fotorrealista HD)
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, USDA, USGS, AEX, GeoEye, y GIS User Community',
            maxZoom: 16,
            noWrap: false
        }).addTo(map);

        markersLayer.addTo(map);

        // Pintamos a todas las criaturas del listado en sus coordenadas
        renderMarkers();
    }

    // Dibujar los nodos / criaturas interactivas
    function renderMarkers() {
        data.forEach(item => {
            // Un marcador HTML CSS custom en vez del 'Pincito azul' aburrido de mapa predeterminado
            const customIcon = L.divIcon({
                className: 'custom-leaflet-marker',
                html: `<div class="creature-avatar" style="background-image: url('${item.image}')"></div>`,
                iconSize: [48, 48],
                iconAnchor: [24, 24],     // El punto medio para la LatLng
                popupAnchor: [0, -30]     // El Popup saldrá justo arriba del círculo
            });

            const marker = L.marker([item.lat, item.lng], { icon: customIcon });

            // HTML Amigable de las tarjetas que NO choca fuera de pantalla. Leaflet lo ajusta
            // con un "slide" impecable automático
            const popupHTML = `
                <div class="creature-popup">
                    <img src="${item.image}" class="creature-popup-img" alt="${item.name}">
                    <div class="creature-popup-body">
                        <div class="creature-popup-title">${item.name}</div>
                        <div class="creature-popup-sci">${item.scientific_name}</div>
                        <div class="creature-popup-desc">${item.desc}</div>
                        <div class="creature-popup-loc">📍 ${item.place}</div>
                    </div>
                </div>
            `;

            marker.bindPopup(popupHTML, {
                maxWidth: 260,
                minWidth: 260,
                autoPanPadding: [30, 80], // Mucho colchón inteligente de auto-acomodo
                closeButton: false // Es más limpio si se cierra haciendo clic fuera
            });

            markersLayer.addLayer(marker);
        });
    }

    // Vuelo Cinematográfico a un Océano!
    function flyToOcean(lat, lng, zoom, oceanCode, btnObj) {
        // Marcamos el botón como interactivo / seleccionado
        const btns = document.querySelectorAll('.ocean-btn');
        btns.forEach(b => b.classList.remove('active'));
        if(btnObj) btnObj.classList.add('active');

        // Mostrar Botón de "Regresar" global
        document.getElementById('btn-reset').classList.add('show');

        // Cerrar cualquier popup que esté abierto
        map.closePopup();

        // El comando mágico "flyTo" de Leaflet (zoom curvo animado excelente, como volar)
        map.flyTo([lat, lng], zoom, {
            duration: 2.5,     // 2.5 Segundos de viaje suave
            easeLinearity: 0.2 // Aceleración realista parecida a Google Maps
        });
    }

    // Regresar la cámara globalmente
    function resetMap() {
        const btns = document.querySelectorAll('.ocean-btn');
        btns.forEach(b => b.classList.remove('active'));
        document.getElementById('btn-reset').classList.remove('show');
        map.closePopup();

        // Vuelo a inicio
        map.flyTo([20, 0], 2, {
            duration: 2.0
        });
    }

    // Inicializamos al cargar (esperamos tantito a las fuentes, pero es instantáneo)
    document.addEventListener('DOMContentLoaded', initMap);

</script>
