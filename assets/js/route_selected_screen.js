// ── renderSeatBadge ──────────────────────────────────────────
function renderSeatBadge(disponibles, capacidad) {
    if (disponibles === null || disponibles === undefined || !capacidad) {
        return `
            <div class="driver-vehicle-row" style="align-items:flex-start;">
                <i class="fas fa-users" style="color:#FFA000;margin-top:2px;"></i>
                <div class="driver-vehicle-row-text" style="width:100%;">
                    <span class="driver-vehicle-label">Disponibilidad de asientos</span>
                    <div style="display: flex; align-items: center; gap: 14px; margin-top: 8px;">
                        <div class="circular-seats-wrap" style="
                            width: 56px;
                            height: 56px;
                            border-radius: 50%;
                            background: conic-gradient(#e0e0e0 100%, #e0e0e0 0);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-shrink: 0;
                        ">
                            <div style="
                                width: 44px;
                                height: 44px;
                                background: white;
                                border-radius: 50%;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                justify-content: center;
                            ">
                                <i class="fas fa-users" style="color:#9e9e9e; font-size:16px;"></i>
                            </div>
                        </div>
                        <div style="display:flex; flex-direction:column; align-items:flex-start;">
                            <span class="driver-vehicle-value" style="font-size:13px; font-weight:400; color:#64748b;">Sin datos de disponibilidad</span>
                            <span class="seats-status status-agotado" style="margin-top:6px;">Sin asignar</span>
                        </div>
                    </div>
                </div>
            </div>`;
    }
    const disp = parseInt(disponibles);
    const cap  = parseInt(capacidad);
    const pct  = cap > 0 ? Math.round((disp / cap) * 100) : 0;

    let barColor;
    let bgColor = '#e2e8f0';
    let conicPct = pct;
    if (disp === 0)      { barColor = '#9e9e9e'; bgColor = '#f5f5f5'; conicPct = 100; }
    else if (pct < 15)   { barColor = '#ef4444'; bgColor = '#fee2e2'; }
    else if (pct < 50)   { barColor = '#eab308'; bgColor = '#fef9c3'; }
    else                 { barColor = '#689F38'; bgColor = '#e8f5e9'; }

    let statusText, statusClass;
    if (disp === 0)      { statusText = 'Agotado';       statusClass = 'status-agotado'; }
    else if (pct < 15)   { statusText = 'Casi agotado';  statusClass = 'status-casi-agotado'; }
    else if (pct < 50)   { statusText = 'Pocos lugares'; statusClass = 'status-pocos'; }
    else                 { statusText = 'Disponible';    statusClass = 'status-disponible'; }

    return `
        <div class="driver-vehicle-row" style="align-items:flex-start;">
            <i class="fas fa-users" style="color:#FFA000;margin-top:2px;"></i>
            <div class="driver-vehicle-row-text" style="width:100%;">
                <span class="driver-vehicle-label">Disponibilidad de asientos</span>
                <div style="display: flex; align-items: center; gap: 14px; margin-top: 8px;">
                    <div class="circular-seats-wrap" style="
                        width: 56px;
                        height: 56px;
                        border-radius: 50%;
                        background: conic-gradient(${barColor} ${conicPct}%, ${bgColor} 0);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-shrink: 0;
                    ">
                        <div style="
                            width: 44px;
                            height: 44px;
                            background: white;
                            border-radius: 50%;
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            line-height: 1.1;
                        ">
                            <strong style="font-size: 15px; color: #333;">${disp}</strong>
                            <span style="font-size: 10px; color: #757575;">de ${cap}</span>
                        </div>
                    </div>
                    <div style="display:flex; flex-direction:column; align-items:flex-start;">
                        <span class="driver-vehicle-value" style="font-size:13px; font-weight:500;">${statusText === 'Agotado' ? 'Sin lugares disponibles' : '<strong>' + disp + '</strong> lugares disponibles'}</span>
                        <span class="seats-status ${statusClass}" style="margin-top:6px;">${statusText}</span>
                    </div>
                </div>
            </div>
        </div>`;
}

/** Estado operativo de la asignación activa (tabla asignaciones.estado, vía API). */
function renderEstadoAsignacionBadge(estadoRaw) {
    const raw = estadoRaw == null || estadoRaw === ''
        ? ''
        : String(estadoRaw).trim().toLowerCase().replace(/\s+/g, '_');
    const map = {
        programado: { label: 'Programado', cls: 'schedule-estado-programado' },
        en_ruta: { label: 'En Ruta', cls: 'schedule-estado-en_ruta' },
        completado: { label: 'Completado', cls: 'schedule-estado-completado' },
        cancelado: { label: 'Cancelado', cls: 'schedule-estado-cancelado' },
        retrasado: { label: 'Retrasado', cls: 'schedule-estado-retrasado' },
    };
    const entry = map[raw] || { label: 'Sin asignar', cls: 'schedule-estado-sin-asignar' };
    return `<span class="schedule-pill schedule-estado-badge ${entry.cls}" title="Estado del servicio">${entry.label}</span>`;
}

const MOCK_LOCATIONS = ["Centro", "Norte", "Sur", "Este", "Oeste"];
const MOCK_ROUTES = [{
    id_ruta: 1,
    nombre: "Ruta de Prueba",
    origen: "Centro",
    destino: "Norte",
    empresa_nombre: "Transportes Ejemplo",
    empresa_telefono: "555-1234",
    empresa_direccion: "Calle Falsa 123",
    empresa_email: "contacto@ejemplo.com",
    horarios: [{
        tipo_dia: "Lunes a Viernes",
        hora_salida: "08:00",
        hora_llegada: "09:30",
        frecuencia: "Cada 30 minutos",
        conductor_nombre: "Juan Pérez",
        conductor_licencia: "LIC-12345",
        vehiculo_modelo: "Mercedes Benz",
        vehiculo_placa: "ABC-123",
        vehiculo_capacidad: 40,
        estado: "programado"
    }],
    paradas: ["Parada A", "Parada B", "Parada C"]
}];

const originSelect = document.getElementById('origin');
const destinationSelect = document.getElementById('destination');
const searchForm = document.getElementById('searchForm');
const resultsContainer = document.getElementById('resultsContainer');
const routeDetailsContainer = document.getElementById('routeDetailsContainer');
const searchBtn = document.getElementById('searchBtn');
const toast = document.getElementById('toast');
const toastMessage = document.getElementById('toastMessage');

const filterAllBtn = document.getElementById('filterAll');
const filterFavoritesBtn = document.getElementById('filterFavorites');

let routes = [];
let selectedRouteId = null;
let favorites = new Set();
let currentFilter = 'all';
let locations = [];

document.addEventListener('DOMContentLoaded', () => {
    fetchLocations();
    loadFavorites();
    
    if(searchForm) searchForm.addEventListener('submit', handleSearch);
    if(originSelect) originSelect.addEventListener('change', updateSearchButton);
    if(destinationSelect) destinationSelect.addEventListener('change', updateSearchButton);
    
    if (filterAllBtn) {
        filterAllBtn.addEventListener('click', () => {
            currentFilter = 'all';
            updateFilterButtons();
            filterAndDisplayRoutes();
        });
    }
    
    if (filterFavoritesBtn) {
        filterFavoritesBtn.addEventListener('click', () => {
            currentFilter = 'favorites';
            updateFilterButtons();
            loadFavoritesAndDisplay();
        });
    }

    // Dropdown close click anywhere
    document.querySelector('.dropdown-content')?.addEventListener('click', (e) => {
        e.stopPropagation();
    });
});

async function loadFavorites() {
    if (typeof ID_USUARIO === 'undefined' || ID_USUARIO === 0) return;
    try {
        const response = await fetch(`${FAVORITES_URL}?action=get_favorites&id_usuario=${ID_USUARIO}`, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });
        if (response.ok) {
            const data = await response.json();
            if (Array.isArray(data)) {
                data.forEach(fav => favorites.add(fav.id_ruta));
            }
        }
    } catch (error) {
        console.error('Error al cargar favoritas:', error);
    }
}

function updateFilterButtons() {
    if (filterAllBtn) filterAllBtn.classList.toggle('active', currentFilter === 'all');
    if (filterFavoritesBtn) filterFavoritesBtn.classList.toggle('active', currentFilter === 'favorites');
}

function filterAndDisplayRoutes() {
    if (currentFilter === 'favorites') {
        if (routes.length === 0) {
            loadFavoritesAndDisplay();
        } else {
            const favoriteRoutes = routes.filter(route => favorites.has(route.id_ruta));
            if (favoriteRoutes.length === 0) {
                if(resultsContainer) resultsContainer.innerHTML = '<div class="no-routes"><p>No tienes rutas favoritas aún</p></div>';
            } else {
                displayRoutes(favoriteRoutes);
            }
        }
    } else {
        displayRoutes(routes);
    }
}

async function loadFavoritesAndDisplay() {
    try {
        if(resultsContainer) showLoading(true, resultsContainer);
        const response = await fetch(`${FAVORITES_URL}?action=get_favorites&id_usuario=${ID_USUARIO}`, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });
        if (response.ok) {
            const favoritesData = await response.json();
            if (Array.isArray(favoritesData) && favoritesData.length > 0) {
                displayRoutes(favoritesData);
            } else {
                if(resultsContainer) resultsContainer.innerHTML = '<div class="no-routes"><p>No tienes rutas favoritas aún</p></div>';
            }
        } else {
            if(resultsContainer) resultsContainer.innerHTML = '<div class="no-routes"><p>Error al cargar favoritas</p></div>';
        }
    } catch (error) {
        console.error('Error al cargar favoritas:', error);
        if(resultsContainer) resultsContainer.innerHTML = '<div class="no-routes"><p>Error al cargar favoritas</p></div>';
    } finally {
        if(resultsContainer) showLoading(false, resultsContainer);
    }
}

async function fetchLocations() {
    try {
        showLoading(true);
        const response = await fetch(`${API_URL}?action=locations`, {
            headers: { 'Accept': 'application/json' },
            cache: 'no-cache'
        });
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Error HTTP ${response.status}: ${errorText}`);
        }
        const data = await response.json();
        if (Array.isArray(data)) {
            locations = data;
        } else {
            throw new Error('Datos de ubicaciones no válidos');
        }
    } catch (error) {
        console.error('Error al cargar ubicaciones:', error);
        showToast('Error al cargar ubicaciones. Usando datos de prueba.');
        locations = MOCK_LOCATIONS;
    } finally {
        populateLocationSelects();
        showLoading(false);
    }
}

function populateLocationSelects() {
    if(!originSelect || !destinationSelect) return;
    while (originSelect.options.length > 1) originSelect.remove(1);
    while (destinationSelect.options.length > 1) destinationSelect.remove(1);
    
    locations.forEach(location => {
        const option1 = document.createElement('option');
        option1.value = location;
        option1.textContent = location;
        originSelect.appendChild(option1);
        
        const option2 = document.createElement('option');
        option2.value = location;
        option2.textContent = location;
        destinationSelect.appendChild(option2);
    });
    
    updateSearchButton();
}

function updateSearchButton() {
    if(!originSelect || !destinationSelect || !searchBtn) return;
    const hasSelection = originSelect.value && destinationSelect.value;
    searchBtn.disabled = !hasSelection;
}

async function handleSearch(e) {
    e.preventDefault();
    
    const origin = originSelect.value;
    const destination = destinationSelect.value;
    
    if (!origin || !destination) {
        showToast('Seleccione origen y destino');
        return;
    }
    
    try {
        if(resultsContainer) showLoading(true, resultsContainer);
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                action: 'search_routes',
                origin: origin,
                destination: destination
            })
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Error HTTP ${response.status}: ${errorText}`);
        }
        
        const data = await response.json();
        
        if (Array.isArray(data)) {
            routes = processRoutes(data);
        } else if (data.error) {
            throw new Error(data.error);
        } else {
            throw new Error('Formato de respuesta no válido');
        }
    } catch (error) {
        console.error('Error buscando rutas:', error);
        showToast(error.message || 'Error al buscar rutas. Mostrando datos de prueba.');
        routes = [{ ...MOCK_ROUTES[0], origen: origin, destino: destination }];
    } finally {
        displayRoutes(routes);
        if(resultsContainer) showLoading(false, resultsContainer);
        selectedRouteId = null;
        showNoSelection();
    }
}

function processRoutes(rawRoutes) {
    const uniqueRoutes = {};
    
    rawRoutes.forEach(route => {
        const routeId = route.id_ruta;
        
        if (uniqueRoutes[routeId]) {
            const scheduleMap = {};
            uniqueRoutes[routeId].horarios.forEach(schedule => {
                const key = `${schedule.tipo_dia}-${schedule.hora_salida}-${schedule.hora_llegada}`;
                scheduleMap[key] = schedule;
            });
            route.horarios.forEach(schedule => {
                const key = `${schedule.tipo_dia}-${schedule.hora_salida}-${schedule.hora_llegada}`;
                scheduleMap[key] = schedule;
            });
            uniqueRoutes[routeId].horarios = Object.values(scheduleMap);
        } else {
            uniqueRoutes[routeId] = {...route};
            if (typeof uniqueRoutes[routeId].paradas === 'string') {
                uniqueRoutes[routeId].paradas = uniqueRoutes[routeId].paradas.split(', ');
            }
        }
    });
    
    return Object.values(uniqueRoutes);
}

// ── displayRoutes — tarjetas con nuevo diseño (corazón abajo) ──
function displayRoutes(routesToDisplay) {
    if(!resultsContainer) return;

    if (!routesToDisplay || routesToDisplay.length === 0) {
        displayNoRoutes();
        return;
    }
    
    resultsContainer.innerHTML = '';
    
    routesToDisplay.forEach(route => {
        const routeCard = document.createElement('div');
        routeCard.className = 'route-card';
        routeCard.setAttribute('data-route-id', route.id_ruta);
        if (route.id_ruta === selectedRouteId) {
            routeCard.classList.add('selected');
        }
        
        const uniqueSchedules = getUniqueSchedules(route.horarios || []);
        const isFavorite = favorites.has(route.id_ruta);
        const favoriteIcon = isFavorite ? 'fas fa-heart' : 'far fa-heart';
        
        routeCard.innerHTML = `
            <div class="route-card-header">
                <div class="route-card-title">
                    <i class="fas fa-bus"></i>
                    <span class="route-company">${route.empresa_nombre || 'Transporte'}</span>
                </div>
            </div>
            <div class="route-path">
                <div class="route-origin">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${route.es_tramo ? (route.parada_embarque || route.origen) : route.origen}</span>
                </div>
                <i class="fas fa-arrow-right arrow"></i>
                <div class="route-destination">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${route.es_tramo ? (route.parada_bajada || route.destino) : route.destino}</span>
                </div>
            </div>
            ${route.es_tramo ? `
            <div style="margin-top:4px; font-size:12px; display:flex; align-items:center; flex-wrap:wrap; gap:6px;">
                <span style="background:#fef3c7; color:#92400e; border-radius:12px; padding:2px 10px; font-weight:600;">Tramo parcial</span>
                <span style="color:#92400e; font-weight:600;"><i class="fas fa-walking"></i> ${route.origen} &rarr; ${route.destino}</span>
            </div>
            ` : ''}
            <div class="route-card-divider"></div>
            <div class="route-schedule-block">
                <div class="route-schedule">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Horarios disponibles</span>
                    <span class="route-schedule-count">${uniqueSchedules.length}</span>
                </div>
            </div>
            <div class="route-card-footer">
                ${(typeof ID_USUARIO !== 'undefined' && ID_USUARIO !== 0) ? `
                <button class="favorite-btn ${isFavorite ? 'active' : ''}" data-route-id="${route.id_ruta}" title="${isFavorite ? 'Eliminar de favoritas' : 'Agregar a favoritas'}">
                    <i class="${favoriteIcon}"></i>
                </button>
                ` : ''}
                <button class="btn-details">Ver detalles</button>
            </div>
        `;
        
        routeCard.addEventListener('click', (e) => {
            if (!e.target.closest('.favorite-btn')) {
                selectedRouteId = route.id_ruta;
                updateSelectedRouteCard();
                showRouteDetails(route);
            }
        });

        const detailsBtn = routeCard.querySelector('.btn-details');
        detailsBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            selectedRouteId = route.id_ruta;
            updateSelectedRouteCard();
            showRouteDetails(route);
        });

        const favoriteBtn = routeCard.querySelector('.favorite-btn');
        if (favoriteBtn) {
            favoriteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleFavorite(route.id_ruta, favoriteBtn);
            });
        }
        
        resultsContainer.appendChild(routeCard);
    });
}

async function toggleFavorite(routeId, buttonElement) {
    const isFavorite = favorites.has(routeId);
    const action = isFavorite ? 'remove_favorite' : 'add_favorite';
    
    try {
        const payload = {
            action: action,
            id_usuario: ID_USUARIO,
            id_ruta: routeId
        };
        
        const response = await fetch(FAVORITES_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                if (isFavorite) {
                    favorites.delete(routeId);
                    buttonElement.classList.remove('active');
                    buttonElement.innerHTML = '<i class="far fa-heart"></i>';
                    buttonElement.title = 'Agregar a favoritas';
                    showToast('Eliminado de favoritas');
                } else {
                    favorites.add(routeId);
                    buttonElement.classList.add('active');
                    buttonElement.innerHTML = '<i class="fas fa-heart"></i>';
                    buttonElement.title = 'Eliminar de favoritas';
                    showToast('Agregado a favoritas');
                }
                if (currentFilter === 'favorites') {
                    filterAndDisplayRoutes();
                }
            }
        } else {
            showToast('Error al actualizar favorita');
        }
    } catch (error) {
        console.error('Error al cambiar favorita:', error);
        showToast('Error al actualizar favorita');
    }
}

function updateSelectedRouteCard() {
    document.querySelectorAll('.route-card').forEach(card => {
        card.classList.remove('selected');
        if (parseInt(card.getAttribute('data-route-id')) === selectedRouteId) {
            card.classList.add('selected');
        }
    });
}

function getUniqueSchedules(schedules) {
    const scheduleMap = {};
    schedules.forEach(schedule => {
        const key = `${schedule.tipo_dia}-${schedule.hora_salida}-${schedule.hora_llegada}`;
        scheduleMap[key] = schedule;
    });
    return Object.values(scheduleMap);
}

function displayNoRoutes() {
    if(resultsContainer) {
        resultsContainer.innerHTML = `
            <div class="no-routes">
                <p>No se encontraron rutas para esta combinación</p>
            </div>
        `;
    }
}

function showNoSelection() {
    if(!routeDetailsContainer) return;
    routeDetailsContainer.innerHTML = `
        <div class="no-selection">
            <i class="fas fa-route"></i>
            <h3>Selecciona una ruta</h3>
            <p>Elige una ruta de la lista para ver los detalles completos</p>
        </div>
    `;
}

// ── showRouteDetails — schedule cards con nuevo diseño ───────
function showRouteDetails(route) {
    if(!routeDetailsContainer) return;

    if (!route) {
        showNoSelection();
        return;
    }
    
    const uniqueSchedules = getUniqueSchedules(route.horarios || []);

    const isTramo   = route.es_tramo == 1;
    const boardStop = isTramo ? (route.parada_embarque || route.origen) : route.origen;
    const alightStop= isTramo ? (route.parada_bajada   || route.destino) : route.destino;

    const paradasRuta     = Array.isArray(route.paradas_ruta) ? route.paradas_ruta : [];
    const paradasFallback = Array.isArray(route.paradas) ? route.paradas : ['No especificadas'];
    
    let contentHTML = `
        <div class="route-details">
            <div class="route-detail-header">
                <div class="route-detail-company">${route.empresa_nombre || 'Ruta'}</div>
                <div class="route-detail-path">
                    <span>${boardStop}</span>
                    <i class="fas fa-arrow-right"></i>
                    <span>${alightStop}</span>
                </div>
                ${isTramo ? `
                <div style="margin-top:8px; font-size:12px; display:flex; align-items:flex-start; flex-direction:column; gap:6px;">
                    <span style="background:#fef3c7; color:#92400e; border-radius:12px; padding:3px 12px; font-weight:600;">Tramo parcial</span>
                    <div style="color:var(--dark-gray); font-weight:600; font-size:13px;">Ruta completa: ${route.origen} &rarr; ${route.destino}</div>
                </div>
                ` : ''}
            </div>

            <div class="detail-divider"></div>

            <div class="detail-card">
                <h4 class="info-title">Información de la empresa:</h4>
                <div class="info-rows-grid">
                    <div class="info-row">
                        <i class="fas fa-building"></i>
                        <div class="info-text">
                            <span class="info-label">Nombre</span>
                            <span class="info-value">${route.empresa_nombre || 'No especificado'}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-phone"></i>
                        <div class="info-text">
                            <span class="info-label">Teléfono</span>
                            <span class="info-value">${route.empresa_telefono || 'No especificado'}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-map-marker-alt"></i>
                        <div class="info-text">
                            <span class="info-label">Dirección</span>
                            <span class="info-value">${route.empresa_direccion || 'No especificada'}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-envelope"></i>
                        <div class="info-text">
                            <span class="info-label">Email</span>
                            <span class="info-value">${route.empresa_email || 'No especificado'}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-divider"></div>

            <h4 class="info-title" style="margin-bottom:14px;font-size:17px;">Horarios disponibles:</h4>
    `;

    // ── Iterar horarios ──────────────────────────────────────
    uniqueSchedules.forEach(schedule => {
        const salida  = schedule.hora_abordaje || schedule.hora_salida;
        const llegada = schedule.hora_bajada   || schedule.hora_llegada;

        // Construir lista de paradas
        let paradasHTML = '';
        const paradasConHora = Array.isArray(schedule.paradas_con_hora) ? schedule.paradas_con_hora : [];

        if (paradasConHora.length > 0) {
            let segmento = paradasConHora;
            if (isTramo) {
                const idxBoard  = paradasConHora.findIndex(p => p.nombre === boardStop);
                const idxAlight = paradasConHora.findIndex(p => p.nombre === alightStop);
                if (idxBoard !== -1 && idxAlight !== -1) {
                    segmento = paradasConHora.slice(idxBoard, idxAlight + 1);
                }
            }
            const boardMin = isTramo && segmento.length > 0 ? segmento[0].minutos_desde_origen : 0;
            paradasHTML = segmento.map(p => {
                const minRel = p.minutos_desde_origen - boardMin;
                return `<li><span class="stop-name">${p.nombre}</span>${minRel > 0 ? '<span class="stop-time">+' + minRel + ' min</span>' : '<span class="stop-time">&nbsp;</span>'}</li>`;
            }).join('');
        } else if (paradasRuta.length > 0) {
            paradasHTML = paradasRuta.map(p =>
                `<li><span class="stop-name">${p.nombre}</span>${p.minutos_desde_origen > 0 ? '<span class="stop-time">+' + p.minutos_desde_origen + ' min</span>' : '<span class="stop-time">&nbsp;</span>'}</li>`
            ).join('');
        } else {
            paradasHTML = paradasFallback.map(p => `<li><span class="stop-name">${p}</span></li>`).join('');
        }

        contentHTML += `
            <div class="schedule-card">

                <!-- Header: icono + empresa/ruta + fila de cápsulas (día + estado) -->
                <div class="schedule-header" style="cursor: pointer;">
                    <div class="schedule-header-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="schedule-header-body">
                        <div class="schedule-header-top-row">
                            <span class="schedule-company-name">${route.empresa_nombre || 'Transporte'}</span>
                            <div class="schedule-header-pills" aria-label="Tipo de día">
                                <span class="schedule-pill schedule-day-badge schedule-day-absolute">${schedule.tipo_dia || 'No especificado'}</span>
                            </div>
                        </div>
                        <div class="schedule-header-info">
                            <div class="schedule-route-path-sub">
                                <i class="fas fa-map-marker-alt" style="color:#2962FF;font-size:11px;"></i>
                                <span>${boardStop}</span>
                                <i class="fas fa-arrow-right" style="font-size:10px;color:#bdbdbd;"></i>
                                <i class="fas fa-map-marker-alt" style="color:#D32F2F;font-size:11px;"></i>
                                <span>${alightStop}</span>
                            </div>
                            <div class="schedule-state-wrap" style="margin-top: 6px;">
                                ${renderEstadoAsignacionBadge(schedule.estado)}
                            </div>
                        </div>
                    </div>
                    <div class="schedule-toggle-icon" style="margin-left: auto;">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>

                <!-- Tiempos siempre visibles -->
                <div class="schedule-times-container">
                    <div class="schedule-times">
                        <div class="time-group departure">
                            <i class="fas fa-bus"></i>
                            <div class="time-text">
                                <span class="time-label">${isTramo ? 'Abordaje' : 'Salida'}</span>
                                <span class="time-value">${salida || '--:--'}</span>
                            </div>
                        </div>
                        <div class="time-group arrival">
                            <i class="fas fa-bus"></i>
                            <div class="time-text">
                                <span class="time-label">${isTramo ? 'Bajada' : 'Llegada'}</span>
                                <span class="time-value">${llegada || '--:--'}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cuerpo de la tarjeta -->
                <div class="schedule-card-body schedule-card-collapsed">

                    <!-- Columna izquierda: info -->
                    <div class="schedule-left-col">

                        <!-- Frecuencia -->
                        <div class="detail-row">
                            <i class="fas fa-redo" style="color:#FFA000;"></i>
                            <div class="detail-row-text">
                                <span class="detail-row-label">Frecuencia</span>
                                <span class="detail-row-value">${schedule.frecuencia || 'No especificada'}</span>
                            </div>
                        </div>

                        <!-- Conductor -->
                        <div class="detail-row">
                            <i class="fas fa-user" style="color:#1565C0;"></i>
                            <div class="detail-row-text">
                                <span class="detail-row-label">Conductor</span>
                                <span class="detail-row-value">${schedule.conductor_nombre || 'N/A'}</span>
                            </div>
                        </div>

                        <!-- Vehículo -->
                        <div class="detail-row">
                            <i class="fas fa-bus" style="color:#1565C0;"></i>
                            <div class="detail-row-text">
                                <span class="detail-row-label">Vehículo</span>
                                <span class="detail-row-value">${schedule.vehiculo_modelo || 'N/A'}</span>
                            </div>
                        </div>

                        <!-- Placa -->
                        <div class="detail-row">
                            <i class="fas fa-ticket-alt" style="color:#E65100;"></i>
                            <div class="detail-row-text">
                                <span class="detail-row-label">Placa</span>
                                <span class="detail-row-value">${schedule.vehiculo_placa || 'N/A'}</span>
                            </div>
                        </div>

                        <!-- Disponibilidad de asientos -->
                        ${renderSeatBadge(schedule.asientos_disponibles, schedule.vehiculo_capacidad)}

                    </div><!-- /schedule-left-col -->

                    <!-- Columna derecha: paradas (label + lista juntos) -->
                    <div class="schedule-right-col">
                        <div class="detail-row">
                            <i class="fas fa-route" style="color:#FFA000;"></i>
                            <div class="detail-row-text">
                                <span class="detail-row-label">${isTramo ? 'Paradas del tramo' : 'Paradas'}</span>
                                <ul class="stops-list stops-timeline ${isTramo ? 'is-tramo' : ''}">
                                    ${paradasHTML}
                                </ul>
                            </div>
                        </div>
                    </div><!-- /schedule-right-col -->

                </div><!-- /schedule-card-body -->
            </div><!-- /schedule-card -->
        `;
    });

    contentHTML += `</div>`; // /route-details
    routeDetailsContainer.innerHTML = contentHTML;

    // ── Event listeners para expandir/contraer horarios ──────────────────
    const scheduleHeaders = routeDetailsContainer.querySelectorAll('.schedule-header');
    scheduleHeaders.forEach(header => {
        header.addEventListener('click', () => {
            const card = header.closest('.schedule-card');
            const body = card.querySelector('.schedule-card-body');
            
            if (card.classList.contains('active')) {
                card.classList.remove('active');
                body.classList.add('schedule-card-collapsed');
            } else {
                card.classList.add('active');
                body.classList.remove('schedule-card-collapsed');
            }
        });
    });
}

function showLoading(show, container = document.body) {
    if (show) {
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'loading';
        loadingDiv.innerHTML = `
            <div class="spinner"></div>
            <div class="loading-text">Cargando...</div>
        `;
        if (container === resultsContainer) {
            container.innerHTML = '';
        }
        container.appendChild(loadingDiv);
    } else {
        const loadingElements = container.querySelectorAll('.loading');
        loadingElements.forEach(element => element.remove());
    }
}

function showToast(message, duration = 3000) {
    if(!toast || !toastMessage) return;
    toastMessage.textContent = message;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, duration);
}

// ── Mobile dropdown toggle ────────────────────
const userDropdown = document.querySelector('.user-dropdown');
const userBtn = document.querySelector('.user-btn');
if (userBtn) {
    userBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if(userDropdown) userDropdown.classList.toggle('open');
    });
}
document.addEventListener('click', () => {
    if (userDropdown) userDropdown.classList.remove('open');
});
