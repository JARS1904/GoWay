// Configuración base de Firebase (Extraída de tu configuración de entorno)
const firebaseConfig = {
  databaseURL: "https://goway-e4a7c-default-rtdb.firebaseio.com",
  projectId: "goway-e4a7c",
};

// Inicializar Firebase (Solo si no se ha inicializado)
if (!firebase.apps.length) {
    firebase.initializeApp(firebaseConfig);
}
const database = firebase.database();

let map;
let markers = {}; // Almacena los marcadores de los conductores por su RFC
let routeStopsLayer;
let filteredDrivers = null; // Set de RFCs permitidos, o null para mostrarlos todos

document.addEventListener("DOMContentLoaded", () => {
    const mapElement = document.getElementById('liveMap');
    if (!mapElement) return;

    // Inicializar mapa de Leaflet centrado temporalmente en México
    map = L.map('liveMap').setView([19.4326, -99.1332], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    routeStopsLayer = L.featureGroup().addTo(map);

    const iconOrigin = L.icon({
        iconUrl: '../../assets/images/icons/maps/icons8-geo-cerca-100 (2).png',
        iconSize: [40, 40], iconAnchor: [20, 40], popupAnchor: [0, -40]
    });
    const iconDest = L.icon({
        iconUrl: '../../assets/images/icons/maps/icons8-geo-cerca-100.png',
        iconSize: [40, 40], iconAnchor: [20, 40], popupAnchor: [0, -40]
    });
    const iconStop = L.icon({
        iconUrl: '../../assets/images/icons/maps/icons8-bus-stop-100.png',
        iconSize: [40, 40], iconAnchor: [20, 40], popupAnchor: [0, -40]
    });

    const routeSelect = document.getElementById('liveMapRouteSelect');
    if (routeSelect) {
        routeSelect.addEventListener('change', async function() {
            const idRuta = this.value;
            routeStopsLayer.clearLayers();
            
            if (!idRuta) {
                // Volver a mostrar todos
                filteredDrivers = null;
                applyDriverFilter();
                if (Object.keys(markers).length > 0) centerMapOnMarkers();
                return;
            }

            try {
                // 1. Obtener paradas
                const resStops = await fetch(`../../api/usuario/routes_api.php?action=paradas&id_ruta=${idRuta}`);
                const stopsData = await resStops.json();
                
                if (Array.isArray(stopsData)) {
                    stopsData.sort((a, b) => a.orden - b.orden);
                    const maxOrden = Math.max(...stopsData.map(p => p.orden));
                    stopsData.forEach(p => {
                        const isFirst = parseInt(p.orden) === 0;
                        const isLast  = parseInt(p.orden) === parseInt(maxOrden) && stopsData.length > 1;
                        let currentIcon = iconStop;
                        if (isFirst) currentIcon = iconOrigin;
                        else if (isLast) currentIcon = iconDest;

                        if (p.latitud && p.longitud) {
                            L.marker([p.latitud, p.longitud], { icon: currentIcon })
                             .bindTooltip(`<b>${p.nombre}</b>`, { permanent: false, direction: 'top' })
                             .addTo(routeStopsLayer);
                        }
                    });
                }

                // 2. Obtener conductores asignados a esta ruta
                const resDrivers = await fetch(`../../api/usuario/drivers_by_route_api.php?id_ruta=${idRuta}`);
                const driversData = await resDrivers.json();
                filteredDrivers = new Set(driversData);

                // Aplicar el filtro a los marcadores actuales
                applyDriverFilter();

                // Centrar mapa en la ruta o en los conductores si hay
                if (routeStopsLayer.getLayers().length > 0) {
                    map.fitBounds(routeStopsLayer.getBounds(), { padding: [50, 50], maxZoom: 15 });
                } else {
                    centerMapOnMarkers();
                }

            } catch (err) {
                console.error("Error al cargar datos de la ruta:", err);
            }
        });
    }

    // Icono personalizado para los vehículos
    const carIcon = L.icon({
        // Ícono oficial de Icons8
        iconUrl: '../../assets/images/icons/icons8-bus-100.png', 
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, -16]
    });

    if (typeof CURRENT_EMPRESA_RFC !== 'undefined') {
        if (CURRENT_EMPRESA_RFC !== 'ALL') {
            // Empresa normal: Escucha solo a sus conductores
            listenToDrivers(CURRENT_EMPRESA_RFC, carIcon);
        } else {
            // Admin Global: Escucha a todas las empresas
            listenToAllDrivers(carIcon);
        }
    }
});

function listenToDrivers(rfcEmpresa, icon) {
    const driversRef = database.ref('ubicaciones_en_vivo/' + rfcEmpresa);
    
    // Escuchar cuando se conecta un nuevo conductor o manda su primera ubicación
    driversRef.on('child_added', (snapshot) => {
        updateMarker(snapshot, icon);
        centerMapOnMarkers();
    });
    
    // Escuchar cada vez que un conductor cambia de ubicación (Cada 10 metros)
    driversRef.on('child_changed', (snapshot) => {
        updateMarker(snapshot, icon);
    });
    
    // Escuchar si el conductor se desconecta o deja la ruta (Opcional)
    driversRef.on('child_removed', (snapshot) => {
        const rfcConductor = snapshot.key;
        if (markers[rfcConductor]) {
            map.removeLayer(markers[rfcConductor]);
            delete markers[rfcConductor];
        }
    });
}

function listenToAllDrivers(icon) {
    // Para el admin global, escuchamos la raíz
    const allDriversRef = database.ref('ubicaciones_en_vivo');
    
    allDriversRef.on('value', (snapshot) => {
        const empresas = snapshot.val();
        
        // Si no hay ninguna empresa, eliminamos todos los marcadores
        if (!empresas) {
            Object.keys(markers).forEach(id => {
                map.removeLayer(markers[id]);
            });
            markers = {};
            return;
        }
        
        // Rastrear qué conductores siguen activos en este snapshot
        const activeDrivers = new Set();

        Object.keys(empresas).forEach(rfcEmpresa => {
            const conductores = empresas[rfcEmpresa];
            Object.keys(conductores).forEach(rfcConductor => {
                const data = conductores[rfcConductor];
                activeDrivers.add(rfcConductor);
                updateMarkerManual(rfcConductor, rfcEmpresa, data, icon);
            });
        });

        // Eliminar del mapa los conductores que ya no están en la base de datos
        Object.keys(markers).forEach(rfcConductor => {
            if (!activeDrivers.has(rfcConductor)) {
                map.removeLayer(markers[rfcConductor]);
                delete markers[rfcConductor];
            }
        });

        centerMapOnMarkers();
    });
}

function updateMarker(snapshot, icon) {
    const rfcConductor = snapshot.key;
    const data = snapshot.val();
    updateMarkerManual(rfcConductor, '', data, icon);
}

function updateMarkerManual(rfcConductor, rfcEmpresa, data, icon) {
    if (!data.lat || !data.lng) return;
    
    const latLng = [data.lat, data.lng];

    if (markers[rfcConductor]) {
        // Mover marcador existente de forma suave
        markers[rfcConductor].setLatLng(latLng);
        // Actualizar el tooltip si cambia la hora
        markers[rfcConductor].getPopup().setContent(`<b>Conductor:</b> ${rfcConductor}<br><b>Empresa:</b> ${rfcEmpresa}<br><b>Última actualización:</b> ${new Date(data.timestamp).toLocaleTimeString()}`);
    } else {
        // Crear nuevo marcador
        const popupContent = `<b>Conductor:</b> ${rfcConductor}<br><b>Empresa:</b> ${rfcEmpresa}<br><b>Última actualización:</b> ${new Date(data.timestamp).toLocaleTimeString()}`;
        markers[rfcConductor] = L.marker(latLng, {icon: icon})
            .bindPopup(popupContent);
    }
    
    // Asegurarse de que el marcador esté o no en el mapa según el filtro
    if (filteredDrivers === null || filteredDrivers.has(rfcConductor)) {
        if (!map.hasLayer(markers[rfcConductor])) {
            markers[rfcConductor].addTo(map);
        }
    } else {
        if (map.hasLayer(markers[rfcConductor])) {
            map.removeLayer(markers[rfcConductor]);
        }
    }
}

function applyDriverFilter() {
    Object.keys(markers).forEach(rfcConductor => {
        const marker = markers[rfcConductor];
        if (filteredDrivers === null || filteredDrivers.has(rfcConductor)) {
            if (!map.hasLayer(marker)) map.addLayer(marker);
        } else {
            if (map.hasLayer(marker)) map.removeLayer(marker);
        }
    });
}

// Función auxiliar para centrar el mapa en todos los conductores activos
function centerMapOnMarkers() {
    const visibleMarkers = Object.values(markers).filter(m => map.hasLayer(m));
    if (visibleMarkers.length > 0) {
        const group = new L.featureGroup(visibleMarkers);
        map.fitBounds(group.getBounds(), { padding: [50, 50], maxZoom: 15 });
    }
}
