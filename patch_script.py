import re

with open('c:/xampp/htdocs/GoWay/pages/admin/paradas_ruta.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add Leaflet CSS
css_patch = """
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map { height: 400px; width: 100%; margin-top: 16px; border-radius: 8px; border: 1px solid #e2e8f0; display: none; z-index: 1; }
        .return-stop-icon { filter: grayscale(100%) opacity(0.6); }
    </style>
"""
content = re.sub(r'<link href="https://fonts.googleapis.com/icon\?family=Material\+Icons" rel="stylesheet">', css_patch, content)

# 2. Add id_ruta_retorno to options
route_query_old = """$res = $conexion->query(
                            "SELECT id_ruta, nombre, origen, destino FROM rutas" . $where_emp . " ORDER BY nombre ASC"
                        );
                        while ($r = $res->fetch_assoc()) {
                            //$label = htmlspecialchars($r['nombre'] . ' (' . $r['origen'] . ' → ' . $r['destino'] . ')');
                            $label = htmlspecialchars($r['nombre']);
                            echo "<option value=\\"{$r['id_ruta']}\\" data-origen=\\"" . htmlspecialchars($r['origen']) . "\\" data-destino=\\"" . htmlspecialchars($r['destino']) . "\\">{$label}</option>";
                        }"""
route_query_new = """$res = $conexion->query(
                            "SELECT id_ruta, nombre, origen, destino, id_ruta_retorno FROM rutas" . $where_emp . " ORDER BY nombre ASC"
                        );
                        while ($r = $res->fetch_assoc()) {
                            $label = htmlspecialchars($r['nombre']);
                            $id_retorno = $r['id_ruta_retorno'] ? $r['id_ruta_retorno'] : '';
                            echo "<option value=\\"{$r['id_ruta']}\\" data-retorno=\\"{$id_retorno}\\" data-origen=\\"" . htmlspecialchars($r['origen']) . "\\" data-destino=\\"" . htmlspecialchars($r['destino']) . "\\">{$label}</option>";
                        }"""
content = content.replace(route_query_old, route_query_new)

# 3. Add map div
map_div = """
            <div class="route-controls" style="margin-top: 10px;">
                <label style="font-size: 13px; color: #64748b; display: flex; align-items: center; gap: 5px;">
                    <input type="checkbox" id="chkShowReturn" checked> Mostrar paradas de ruta de retorno como guía
                </label>
            </div>
            <div id="map"></div>
            <div id="loadingStops">Cargando paradas…</div>
"""
content = content.replace('<div id="loadingStops">Cargando paradas…</div>', map_div)

# 4. Modal inputs
modal_inputs_old = """<input type="hidden" id="f_id_parada"  name="id_parada">
            <input type="hidden" id="f_id_ruta"    name="id_ruta">"""
modal_inputs_new = """<input type="hidden" id="f_id_parada"  name="id_parada">
            <input type="hidden" id="f_id_ruta"    name="id_ruta">
            <input type="hidden" id="f_latitud"    name="latitud">
            <input type="hidden" id="f_longitud"   name="longitud">
            <div id="coordDisplay" style="font-size:12px; color:#64748b; text-align:center; margin-bottom:10px;">No hay coordenadas seleccionadas. Clic en el mapa para ubicar.</div>"""
content = content.replace(modal_inputs_old, modal_inputs_new)

# 5. Add Leaflet JS
js_old = """<script src="../../assets/js/notifications.js"></script>"""
js_new = """<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="../../assets/js/notifications.js"></script>"""
content = content.replace(js_old, js_new)

# 6. Add JS logic
js_logic_patch = """
let map, routeMarkers, returnMarkers;
document.addEventListener("DOMContentLoaded", () => {
    map = L.map('map').setView([18.1729, -93.1090], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
    routeMarkers = L.featureGroup().addTo(map);
    returnMarkers = L.featureGroup().addTo(map);
    
    map.on('click', function(e) {
        if(!currentRouteId) return;
        document.getElementById('f_latitud').value = e.latlng.lat.toFixed(8);
        document.getElementById('f_longitud').value = e.latlng.lng.toFixed(8);
        document.getElementById('coordDisplay').innerHTML = `Lat: ${e.latlng.lat.toFixed(5)}, Lon: ${e.latlng.lng.toFixed(5)}`;
        
        document.getElementById('stopModalTitle').textContent = 'Agregar parada (Desde Mapa)';
        document.getElementById('f_id_parada').value  = '';
        document.getElementById('f_id_ruta').value    = currentRouteId;
        document.getElementById('f_nombre').value     = '';
        document.getElementById('f_orden').value      = '';
        document.getElementById('f_minutos').value    = '';
        document.getElementById('stopModal').classList.add('active');
    });
});

document.getElementById('chkShowReturn').addEventListener('change', (e) => {
    if(e.target.checked) map.addLayer(returnMarkers);
    else map.removeLayer(returnMarkers);
});

async function updateStopCoordinates(id_parada, latlng, p) {
    const fd = new FormData();
    fd.append('id_parada', id_parada);
    fd.append('nombre', p.nombre);
    fd.append('orden', p.orden);
    fd.append('minutos_desde_origen', p.minutos_desde_origen);
    fd.append('latitud', latlng.lat.toFixed(8));
    fd.append('longitud', latlng.lng.toFixed(8));
    
    try {
        const res = await fetch(CTRL_UPDATE, { method: 'POST', body: fd });
        const data = await res.json();
        if(data.success) { showNotification('Coordenadas actualizadas', 'success'); }
        else { showNotification(data.message, 'error'); loadStops(currentRouteId); }
    } catch(err) { showNotification('Error de conexión', 'error'); loadStops(currentRouteId); }
}

async function loadReturnStops(id_retorno) {
    returnMarkers.clearLayers();
    if(!id_retorno) return;
    try {
        const res  = await fetch(`${API_PARADAS}?action=paradas&id_ruta=${id_retorno}`);
        const data = await res.json();
        if(Array.isArray(data)) {
            data.forEach(p => {
                if(p.latitud && p.longitud) {
                    let icon = L.icon({
                        iconUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png',
                        shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
                        className: 'return-stop-icon',
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34]
                    });
                    L.marker([p.latitud, p.longitud], {icon: icon}).bindPopup(`<b>Retorno:</b> ${p.nombre}`).addTo(returnMarkers);
                }
            });
        }
    } catch(err) {}
}
"""
content = content.replace("const noStopsMsg     = document.getElementById('noStopsMsg');", "const noStopsMsg     = document.getElementById('noStopsMsg');\n" + js_logic_patch)

# 7. Update hideTables / loadStops to show map
content = content.replace("stopsTable.style.display = 'none';", "stopsTable.style.display = 'none';\ndocument.getElementById('map').style.display = currentRouteId ? 'block' : 'none'; if(currentRouteId && typeof map !== 'undefined') { setTimeout(() => map.invalidateSize(), 300); }")

# 8. Inside loadStops, handle route retornos
content = content.replace("renderStops(data, highlight);", "renderStops(data, highlight);\n        const opt = routeSelect.options[routeSelect.selectedIndex];\n        const idRetorno = opt.getAttribute('data-retorno');\n        loadReturnStops(idRetorno);")

# 9. Inside renderStops, add markers
render_stops_patch = """
    routeMarkers.clearLayers();
    paradas.forEach(p => {
        if(p.latitud && p.longitud) {
            let m = L.marker([p.latitud, p.longitud], {draggable: true}).bindPopup(`<b>${p.orden}</b>: ${p.nombre}`).addTo(routeMarkers);
            m.on('dragend', function(e) {
                updateStopCoordinates(p.id_parada, m.getLatLng(), p);
            });
        }
"""
content = content.replace("paradas.forEach(p => {", render_stops_patch)

# 10. Fit bounds at end of renderStops
content = content.replace("stopsBody.appendChild(tr);", "stopsBody.appendChild(tr);\n    });\n    if(routeMarkers.getLayers().length > 0) { setTimeout(() => map.fitBounds(routeMarkers.getBounds().pad(0.1)), 100); }")
# Remove the original "});" since we replaced it
content = content.replace("    });\n    if(routeMarkers.getLayers().length > 0) { setTimeout(() => map.fitBounds(routeMarkers.getBounds().pad(0.1)), 100); }\n}", "    if(routeMarkers.getLayers().length > 0) { setTimeout(() => map.fitBounds(routeMarkers.getBounds().pad(0.1)), 100); }\n}")


# 11. openEditModal updates coordinates
open_edit_old = """function openEditModal(id_parada, nombre, orden, minutos) {
    document.getElementById('stopModalTitle').textContent = 'Editar parada';
    document.getElementById('f_id_parada').value  = id_parada;
    document.getElementById('f_id_ruta').value    = currentRouteId;
    document.getElementById('f_nombre').value     = nombre;
    document.getElementById('f_orden').value      = orden;
    document.getElementById('f_minutos').value    = minutos;
    document.getElementById('stopModal').classList.add('active');
}"""
open_edit_new = """function openEditModal(id_parada, nombre, orden, minutos, lat, lon) {
    document.getElementById('stopModalTitle').textContent = 'Editar parada';
    document.getElementById('f_id_parada').value  = id_parada;
    document.getElementById('f_id_ruta').value    = currentRouteId;
    document.getElementById('f_nombre').value     = nombre;
    document.getElementById('f_orden').value      = orden;
    document.getElementById('f_minutos').value    = minutos;
    document.getElementById('f_latitud').value    = lat || '';
    document.getElementById('f_longitud').value   = lon || '';
    document.getElementById('coordDisplay').innerHTML = (lat && lon) ? `Lat: ${lat}, Lon: ${lon}` : 'Sin coordenadas';
    document.getElementById('stopModal').classList.add('active');
}"""
content = content.replace(open_edit_old, open_edit_new)

# 12. Fix the openEditModal call in renderStops
edit_call_old = """onclick="openEditModal(${p.id_parada}, '${escAttr(p.nombre)}', ${p.orden}, ${p.minutos_desde_origen})\""""
edit_call_new = """onclick="openEditModal(${p.id_parada}, '${escAttr(p.nombre)}', ${p.orden}, ${p.minutos_desde_origen}, '${p.latitud||''}', '${p.longitud||''}')\""""
content = content.replace(edit_call_old, edit_call_new)

# 13. Fix btnAddStop click to clear coords
add_stop_old = """document.getElementById('f_minutos').value    = '';
    document.getElementById('stopModal').classList.add('active');"""
add_stop_new = """document.getElementById('f_minutos').value    = '';
    document.getElementById('f_latitud').value    = '';
    document.getElementById('f_longitud').value   = '';
    document.getElementById('coordDisplay').innerHTML = 'No hay coordenadas seleccionadas.';
    document.getElementById('stopModal').classList.add('active');"""
content = content.replace(add_stop_old, add_stop_new)

with open('c:/xampp/htdocs/GoWay/pages/admin/paradas_ruta_patched.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Patched file created.")
