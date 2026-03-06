<?php
session_start();

// Verificar si el usuario está logueado y tiene rol=2
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header("Location: ../login.php");
    exit();
}

// Consultar foto fresca de la BD (no depender de la sesión)
require_once '../../config/conexion_bd.php';
$stmt = $conexion->prepare("SELECT foto FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$_user_foto = $stmt->get_result()->fetch_assoc()['foto'] ?? null;
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoWay - Rutas de Transporte</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../../assets/images/logo.png" type="image/png">
    <link rel="stylesheet" href="../../assets/css/route_selected_screen.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="GoWay Logo">
                <h1>GoWay</h1>
            </div>

            <!-- Nav links para desktop -->
            <nav class="header-nav">
                <a href="https://goway.netlify.app" target="_blank" class="download-btn">
                    <i class="fas fa-download"></i> Descargar App
                </a>
                <a href="../logout.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
                <button class="profile-nav-btn" onclick="openProfilePanel()">
                    <?php if (!empty($_user_foto)): ?>
                        <img src="../../assets/images/profiles/<?php echo htmlspecialchars($_user_foto); ?>" class="profile-nav-mini-avatar profile-nav-mini-img" alt="foto">
                    <?php else: ?>
                        <span class="profile-nav-mini-avatar"><?php echo htmlspecialchars(strtoupper(mb_substr($_SESSION['nombre'] ?? 'U', 0, 1))); ?></span>
                    <?php endif; ?>
                    Mi Perfil
                </button>
            </nav>

            <!-- Menú desplegable solo para móvil -->
            <div class="user-dropdown">
                <button class="user-btn">
                    <i class="fas fa-user-circle"></i>
                </button>
                <div class="dropdown-content">
                    <a href="#" onclick="openProfilePanel(); return false;">
                        <i class="fas fa-user-circle"></i> Mi Perfil
                    </a>
                    <a href="https://goway.netlify.app" target="_blank">
                        <i class="fas fa-download"></i> Descargar App
                    </a>
                    <a href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Columna izquierda - Búsqueda y resultados -->
        <div class="left-column">
            <section class="search-section">
                <p class="greeting">Hola, <span><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></span> &#128075;</p>
                <h2 class="search-title">¿A dónde quieres ir?</h2>
                <form id="searchForm" class="search-form">
                    <div class="form-group">
                        <label for="origin">Origen</label>
                        <select id="origin" required>
                            <option value="">Seleccione el origen</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="destination">Destino</label>
                        <select id="destination" required>
                            <option value="">Seleccione el destino</option>
                        </select>
                    </div>
                    <button type="submit" id="searchBtn" class="btn" disabled>
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </form>
            </section>

            <div class="divider"></div>

            <section class="results-section">
                <h2 class="section-title">Disponibles</h2>
                
                <div class="filter-section">
                    <button class="filter-btn active" id="filterAll" data-filter="all">
                        <i class="fas fa-list"></i> Todas
                    </button>
                    <button class="filter-btn" id="filterFavorites" data-filter="favorites">
                        <i class="fas fa-heart"></i> Favoritas
                    </button>
                </div>
                
                <div id="resultsContainer">
                    <div class="no-routes">
                        <p>Seleccione origen y destino para buscar rutas</p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Columna derecha - Detalles de ruta -->
        <div class="right-column">
            <div id="routeDetailsContainer">
                <div class="no-selection">
                    <i class="fas fa-route"></i>
                    <h3>Selecciona una ruta</h3>
                    <p>Elige una ruta de la lista para ver los detalles completos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <i class="fas fa-exclamation-circle"></i>
        <span id="toastMessage"></span>
    </div>

    <!-- Profile Panel Overlay -->
    <div id="profileOverlay" class="profile-overlay" onclick="closeProfilePanel()"></div>

    <!-- Profile Side Panel -->
    <div id="profilePanel" class="profile-panel">

        <div class="panel-views-wrapper" id="panelViewsWrapper">

        <!-- Vista principal del perfil -->
        <div id="panelViewMain" class="panel-view">
            <div class="profile-panel-header">
                <h2>Mi Perfil</h2>
                <button class="profile-close-btn" onclick="closeProfilePanel()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="profile-avatar-section">
                <?php if (!empty($_user_foto)): ?>
                    <img src="../../assets/images/profiles/<?php echo htmlspecialchars($_user_foto); ?>" class="profile-avatar-circle profile-avatar-photo" alt="foto">
                <?php else: ?>
                    <div class="profile-avatar-circle"><?php echo htmlspecialchars(strtoupper(mb_substr($_SESSION['nombre'] ?? 'U', 0, 1))); ?></div>
                <?php endif; ?>
                <div class="profile-user-name"><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></div>
                <div class="profile-user-email"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></div>
            </div>

            <div class="profile-menu-list">
                <div class="profile-menu-item" onclick="showTerminos()">
                    <div class="profile-menu-icon"><i class="fas fa-file-alt"></i></div>
                    <span class="profile-menu-label">Términos y condiciones</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </div>

                <div class="profile-menu-item" onclick="toggleConfiguracion()">
                    <div class="profile-menu-icon"><i class="fas fa-cog"></i></div>
                    <span class="profile-menu-label">Configuración</span>
                    <i class="fas fa-chevron-right chevron" id="configChevron"></i>
                </div>
                <div id="configExpanded" class="config-expanded">
                    <div class="config-version-row">
                        <span class="config-version-label">Versión de la aplicación</span>
                        <span class="config-version-value">1.0.0</span>
                    </div>
                    <a href="../logout.php" class="config-logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Cerrar sesión</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Vista de Términos y condiciones -->
        <div id="panelViewTerms" class="panel-view">
            <div class="panel-view-header">
                <button class="panel-back-btn" onclick="showMainView()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h2>Términos y condiciones</h2>
            </div>
            <div class="terms-content">
                <h3>1. Uso del servicio</h3>
                <p>GoWay es una plataforma de consulta de rutas de transporte público. El usuario acepta utilizar el servicio de manera responsable y conforme a las leyes aplicables.</p>
                <h3>2. Datos personales</h3>
                <p>Los datos personales proporcionados durante el registro serán utilizados exclusivamente para la prestación del servicio y no serán compartidos con terceros sin consentimiento expreso.</p>
                <h3>3. Exactitud de la información</h3>
                <p>GoWay no garantiza la exactitud absoluta de los horarios y rutas mostrados. Se recomienda verificar la información directamente con las empresas de transporte.</p>
                <h3>4. Favoritas y cuenta</h3>
                <p>Las rutas marcadas como favoritas se almacenan vinculadas a su cuenta de usuario. Al eliminar su cuenta, esta información será eliminada permanentemente.</p>
                <h3>5. Modificaciones</h3>
                <p>Nos reservamos el derecho de modificar estos términos en cualquier momento. Los cambios serán notificados a través de la plataforma.</p>
                <h3>6. Contacto</h3>
                <p>Para cualquier consulta relacionada con estos términos, puede contactarnos a través de los canales oficiales de GoWay.</p>
            </div>
        </div>

        </div><!-- /panel-views-wrapper -->
    </div><!-- /profilePanel -->

    <script>
        // Configuración de API
        const API_BASE_URL = window.location.origin; // Obtiene http://localhost
        const API_URL = `${API_BASE_URL}/GoWay/api/routes_api.php`;
        const FAVORITES_URL = `${API_BASE_URL}/GoWay/api/favorites_routes_api.php`;
        const ID_USUARIO = <?php echo isset($_SESSION['id']) ? $_SESSION['id'] : 0; ?>;
        
        // Datos mock para cuando falle la API
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
                dia_semana: "Lunes a Viernes",
                hora_salida: "08:00",
                hora_llegada: "09:30",
                frecuencia: "Cada 30 minutos",
                conductor_nombre: "Juan Pérez",
                conductor_licencia: "LIC-12345",
                vehiculo_modelo: "Mercedes Benz",
                vehiculo_placa: "ABC-123",
                vehiculo_capacidad: 40
            }],
            paradas: ["Parada A", "Parada B", "Parada C"]
        }];

        // Elementos del DOM
        const originSelect = document.getElementById('origin');
        const destinationSelect = document.getElementById('destination');
        const searchForm = document.getElementById('searchForm');
        const resultsContainer = document.getElementById('resultsContainer');
        const routeDetailsContainer = document.getElementById('routeDetailsContainer');
        const searchBtn = document.getElementById('searchBtn');
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        
        // Elementos de filtro
        const filterAllBtn = document.getElementById('filterAll');
        const filterFavoritesBtn = document.getElementById('filterFavorites');
        
        // Estado
        let routes = [];
        let selectedRouteId = null;
        let favorites = new Set();
        let currentFilter = 'all';
        let locations = [];

        // Inicializar la aplicación
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Iniciando aplicación...');
            console.log('URL base:', API_BASE_URL);
            console.log('URL API:', API_URL);
            
            fetchLocations();
            loadFavorites();
            
            // Escuchadores de eventos del formulario
            searchForm.addEventListener('submit', handleSearch);
            originSelect.addEventListener('change', updateSearchButton);
            destinationSelect.addEventListener('change', updateSearchButton);
            
            // Escuchadores de eventos de filtro
            filterAllBtn.addEventListener('click', () => {
                currentFilter = 'all';
                updateFilterButtons();
                filterAndDisplayRoutes();
            });
            
            filterFavoritesBtn.addEventListener('click', () => {
                currentFilter = 'favorites';
                updateFilterButtons();
                loadFavoritesAndDisplay();
            });
        });

        // Cargar rutas favoritas del servidor
        async function loadFavorites() {
            try {
                const response = await fetch(`${FAVORITES_URL}?action=get_favorites&id_usuario=${ID_USUARIO}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                console.log('Response favoritas:', response.status);
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('Datos favoritas recibidos:', data);
                    
                    if (Array.isArray(data)) {
                        data.forEach(fav => {
                            favorites.add(fav.id_ruta);
                        });
                        console.log('Favoritas cargadas:', favorites);
                    }
                } else {
                    console.error('Error en respuesta favoritas:', response.status);
                }
            } catch (error) {
                console.error('Error al cargar favoritas:', error);
            }
        }

        // Actualizar apariencia de botones de filtro
        function updateFilterButtons() {
            filterAllBtn.classList.toggle('active', currentFilter === 'all');
            filterFavoritesBtn.classList.toggle('active', currentFilter === 'favorites');
        }

        // Filtrar y mostrar rutas según el filtro actual
        function filterAndDisplayRoutes() {
            if (currentFilter === 'favorites') {
                if (routes.length === 0) {
                    // Si no hay búsqueda realizada, traer favoritas desde BD
                    loadFavoritesAndDisplay();
                } else {
                    const favoriteRoutes = routes.filter(route => favorites.has(route.id_ruta));
                    if (favoriteRoutes.length === 0) {
                        resultsContainer.innerHTML = '<div class="no-routes"><p>No tienes rutas favoritas aún</p></div>';
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
                showLoading(true, resultsContainer);
                
                const response = await fetch(`${FAVORITES_URL}?action=get_favorites&id_usuario=${ID_USUARIO}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const favoritesData = await response.json();
                    console.log('Favoritas cargadas:', favoritesData);
                    
                    if (Array.isArray(favoritesData) && favoritesData.length > 0) {
                        displayRoutes(favoritesData);
                    } else {
                        resultsContainer.innerHTML = '<div class="no-routes"><p>No tienes rutas favoritas aún</p></div>';
                    }
                } else {
                    resultsContainer.innerHTML = '<div class="no-routes"><p>Error al cargar favoritas</p></div>';
                }
            } catch (error) {
                console.error('Error al cargar favoritas:', error);
                resultsContainer.innerHTML = '<div class="no-routes"><p>Error al cargar favoritas</p></div>';
            } finally {
                showLoading(false, resultsContainer);
            }
        }

        // Obtener ubicaciones disponibles de la API
        async function fetchLocations() {
            try {
                showLoading(true);
                console.log('Consultando API en:', `${API_URL}?action=locations`);
                
                const response = await fetch(`${API_URL}?action=locations`, {
                    headers: {
                        'Accept': 'application/json'
                    },
                    cache: 'no-cache'
                });
                
                console.log('Respuesta recibida. Status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Error en respuesta:', errorText);
                    throw new Error(`Error HTTP ${response.status}: ${errorText}`);
                }
                
                const data = await response.json();
                console.log('Datos recibidos:', data);
                
                if (Array.isArray(data)) {
                    locations = data;
                } else {
                    console.warn('La API devolvió un array vacío o formato inválido');
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

        // Llenar selects de origen y destino con ubicaciones
        function populateLocationSelects() {
            console.log('Llenando selects con:', locations);
            
            // Limpiar opciones existentes (manteniendo la primera opción vacía)
            while (originSelect.options.length > 1) originSelect.remove(1);
            while (destinationSelect.options.length > 1) destinationSelect.remove(1);
            
            // Agregar nuevas opciones
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

        // Actualizar estado del botón de búsqueda según selecciones
        function updateSearchButton() {
            const hasSelection = originSelect.value && destinationSelect.value;
            searchBtn.disabled = !hasSelection;
        }

        // Manejar envío de formulario de búsqueda
        async function handleSearch(e) {
            e.preventDefault();
            
            const origin = originSelect.value;
            const destination = destinationSelect.value;
            
            if (!origin || !destination) {
                showToast('Seleccione origen y destino');
                return;
            }
            
            try {
                showLoading(true, resultsContainer);
                console.log(`Buscando rutas de ${origin} a ${destination}`);
                
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
                
                console.log('Respuesta de búsqueda. Status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`Error HTTP ${response.status}: ${errorText}`);
                }
                
                const data = await response.json();
                console.log('Rutas encontradas:', data);
                
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
                routes = [{
                    ...MOCK_ROUTES[0],
                    origen: origin,
                    destino: destination
                }];
            } finally {
                displayRoutes(routes);
                showLoading(false, resultsContainer);
                
                // Reset selección
                selectedRouteId = null;
                showNoSelection();
            }
        }

        // Procesar rutas para combinar duplicados con diferentes horarios
        function processRoutes(rawRoutes) {
            const uniqueRoutes = {};
            
            rawRoutes.forEach(route => {
                const routeId = route.id_ruta;
                
                if (uniqueRoutes[routeId]) {
                    // Combinar horarios
                    const scheduleMap = {};
                    
                    // Agregar horarios existentes
                    uniqueRoutes[routeId].horarios.forEach(schedule => {
                        const key = `${schedule.dia_semana}-${schedule.hora_salida}-${schedule.hora_llegada}`;
                        scheduleMap[key] = schedule;
                    });
                    
                    // Agregar nuevos horarios
                    route.horarios.forEach(schedule => {
                        const key = `${schedule.dia_semana}-${schedule.hora_salida}-${schedule.hora_llegada}`;
                        scheduleMap[key] = schedule;
                    });
                    
                    // Actualizar horarios
                    uniqueRoutes[routeId].horarios = Object.values(scheduleMap);
                } else {
                    // Agregar nueva ruta
                    uniqueRoutes[routeId] = {...route};
                    
                    // Asegurar que paradas es un array
                    if (typeof uniqueRoutes[routeId].paradas === 'string') {
                        uniqueRoutes[routeId].paradas = uniqueRoutes[routeId].paradas.split(', ');
                    }
                }
            });
            
            return Object.values(uniqueRoutes);
        }

        // Mostrar rutas en el contenedor de resultados
        function displayRoutes(routesToDisplay) {
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
                
                // Obtener conteo de horarios únicos
                const uniqueSchedules = getUniqueSchedules(route.horarios || []);
                const isFavorite = favorites.has(route.id_ruta);
                const favoriteIcon = isFavorite ? 'fas fa-heart' : 'far fa-heart';
                
                routeCard.innerHTML = `
                    <div class="route-card-header">
                        <div class="route-card-title">
                            <i class="fas fa-building"></i>
                            <span class="route-company">${route.empresa_nombre || 'Transporte'}</span>
                        </div>
                        <button class="favorite-btn ${isFavorite ? 'active' : ''}" data-route-id="${route.id_ruta}" title="${isFavorite ? 'Eliminar de favoritas' : 'Agregar a favoritas'}">
                            <i class="${favoriteIcon}"></i>
                        </button>
                    </div>
                    ${route.es_tramo ? `<div style="margin-bottom:6px;"><span style="background:#fef3c7;color:#92400e;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:600;">✂ Tramo parcial de: ${route.origen} → ${route.destino}</span></div>` : ''}
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
                    <div class="route-card-divider"></div>
                    <div class="route-card-footer">
                        <div class="route-schedule">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Horarios disponibles:</span>
                            <span class="route-schedule-count">${uniqueSchedules.length}</span>
                        </div>
                        <button class="btn-details">Ver detalles</button>
                    </div>
                `;
                
                // Agregar evento de clic a la tarjeta de ruta
                routeCard.addEventListener('click', (e) => {
                    // No seleccionar si se hace clic en el botón de favorita
                    if (!e.target.closest('.favorite-btn')) {
                        selectedRouteId = route.id_ruta;
                        updateSelectedRouteCard();
                        showRouteDetails(route);
                    }
                });

                // Botón "Ver detalles"
                const detailsBtn = routeCard.querySelector('.btn-details');
                detailsBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectedRouteId = route.id_ruta;
                    updateSelectedRouteCard();
                    showRouteDetails(route);
                });

                // Agregar escucha de evento del botón de favorita
                const favoriteBtn = routeCard.querySelector('.favorite-btn');
                favoriteBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleFavorite(route.id_ruta, favoriteBtn);
                });
                
                resultsContainer.appendChild(routeCard);
            });
        }

        // Alternar estado de favorita para una ruta
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
                        // Actualizar el estado local
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
                        
                        // Si estamos en vista de favoritas, actualizar lista
                        if (currentFilter === 'favorites') {
                            filterAndDisplayRoutes();
                        }
                    }
                } else {
                    showToast('Error al actualizar favorita');
                    console.error('Error response:', await response.text());
                }
            } catch (error) {
                console.error('Error al cambiar favorita:', error);
                showToast('Error al actualizar favorita');
            }
        }

        // Actualizar estilo de tarjeta de ruta seleccionada
        function updateSelectedRouteCard() {
            document.querySelectorAll('.route-card').forEach(card => {
                card.classList.remove('selected');
                if (parseInt(card.getAttribute('data-route-id')) === selectedRouteId) {
                    card.classList.add('selected');
                }
            });
        }

        // Obtener horarios únicos (evitando duplicados)
        function getUniqueSchedules(schedules) {
            const scheduleMap = {};
            
            schedules.forEach(schedule => {
                const key = `${schedule.dia_semana}-${schedule.hora_salida}-${schedule.hora_llegada}`;
                scheduleMap[key] = schedule;
            });
            
            return Object.values(scheduleMap);
        }

        // Mostrar mensaje "sin rutas"
        function displayNoRoutes() {
            resultsContainer.innerHTML = `
                <div class="no-routes">
                    <p>No se encontraron rutas para esta combinación</p>
                </div>
            `;
        }

        // Mostrar vista sin selección
        function showNoSelection() {
            routeDetailsContainer.innerHTML = `
                <div class="no-selection">
                    <i class="fas fa-route"></i>
                    <h3>Selecciona una ruta</h3>
                    <p>Elige una ruta de la lista para ver los detalles completos</p>
                </div>
            `;
        }

        // Mostrar detalles de ruta en columna derecha
        function showRouteDetails(route) {
            if (!route) {
                showNoSelection();
                return;
            }
            
            const uniqueSchedules = getUniqueSchedules(route.horarios || []);
            const isFavorite = favorites.has(route.id_ruta);
            const favoriteIcon = isFavorite ? 'fas fa-heart' : 'far fa-heart';

            // Para tramos parciales: helper para sumar minutos a "HH:MM[:SS]"
            function addMinutes(timeStr, minutes) {
                if (!timeStr || !minutes) return timeStr;
                const parts = timeStr.split(':');
                const totalMin = parseInt(parts[0]) * 60 + parseInt(parts[1]) + parseInt(minutes);
                const h = String(Math.floor(totalMin / 60) % 24).padStart(2, '0');
                const m = String(totalMin % 60).padStart(2, '0');
                return `${h}:${m}`;
            }

            const isTramo      = route.es_tramo == 1;
            const boardStop    = isTramo ? (route.parada_embarque || route.origen) : route.origen;
            const alightStop   = isTramo ? (route.parada_bajada   || route.destino) : route.destino;
            const embedMinutes = parseInt(route.embarque_minutos) || 0;
            const alightMinutes= parseInt(route.bajada_minutos)   || 0;

            // Paradas estructuradas (si existen)
            const paradasRuta = Array.isArray(route.paradas_ruta) ? route.paradas_ruta : [];
            const paradasFallback = Array.isArray(route.paradas) ? route.paradas : ['No especificadas'];
            
            // Construir contenido de detalles
            let contentHTML = `
                <div class="route-details">
                    <div class="route-detail-header">
                        <div class="route-detail-title">${route.empresa_nombre || 'Ruta'}</div>
                        ${isTramo ? `
                        <div style="margin:4px 0 2px 0;">
                            <span style="background:#fef3c7;color:#92400e;border-radius:12px;padding:3px 12px;font-size:12px;font-weight:600;">✂ Tramo parcial</span>
                        </div>
                        <div style="font-size:12px;color:#64748b;margin-bottom:4px;">Ruta completa: ${route.origen} → ${route.destino}</div>
                        ` : ''}
                        <div class="route-full-path">
                            <span>${boardStop}</span>
                            <i class="fas fa-arrow-right"></i>
                            <span>${alightStop}</span>
                        </div>
                    </div>

                    <div class="detail-divider"></div>

                    <div class="detail-card">
                        <h4 class="info-title">Información de la empresa:</h4>
                        <div class="info-rows-grid">
                        <div class="info-row">
                            <i class="fas fa-building"></i>
                            <div class="info-text">
                                <span class="info-label">Nombre:</span>
                                <span class="info-value">${route.empresa_nombre || 'No especificado'}</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-phone"></i>
                            <div class="info-text">
                                <span class="info-label">Teléfono:</span>
                                <span class="info-value">${route.empresa_telefono || 'No especificado'}</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="info-text">
                                <span class="info-label">Dirección:</span>
                                <span class="info-value">${route.empresa_direccion || 'No especificada'}</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-envelope"></i>
                            <div class="info-text">
                                <span class="info-label">Email:</span>
                                <span class="info-value">${route.empresa_email || 'No especificado'}</span>
                            </div>
                        </div>
                        </div>
                    </div>

                    <div class="detail-divider"></div>

                    <h4 class="info-title" style="margin-bottom:14px;font-size:17px;">Horarios disponibles:</h4>
            `;

            // Agregar horarios
            uniqueSchedules.forEach(schedule => {
                // Tiempos: si es tramo y el servidor ya calculó los ajustados, usarlos;
                // si no, calcularlos en el cliente con los minutos del tramo.
                const salida  = schedule.hora_abordaje
                    || (isTramo ? addMinutes(schedule.hora_salida,  embedMinutes)  : schedule.hora_salida);
                const llegada = schedule.hora_bajada
                    || (isTramo ? addMinutes(schedule.hora_salida,  alightMinutes) : schedule.hora_llegada);

                // Construir lista de paradas a mostrar en la tarjeta
                let paradasHTML = '';
                if (paradasRuta.length > 0) {
                    // Paradas estructuradas: si es tramo, mostrar solo el segmento relevante
                    let segmento = paradasRuta;
                    if (isTramo) {
                        const idxBoard  = paradasRuta.findIndex(p => p.nombre === boardStop);
                        const idxAlight = paradasRuta.findIndex(p => p.nombre === alightStop);
                        if (idxBoard !== -1 && idxAlight !== -1) {
                            segmento = paradasRuta.slice(idxBoard, idxAlight + 1);
                        }
                    }
                    paradasHTML = segmento.map(p =>
                        `<li>${p.nombre} <span style="color:#64748b;font-size:11px;">(+${p.minutos_desde_origen} min)</span></li>`
                    ).join('');
                } else {
                    paradasHTML = paradasFallback.map(p => `<li>${p}</li>`).join('');
                }

                contentHTML += `
                    <div class="schedule-card">
                        <div class="schedule-header">
                            <span class="schedule-company-name">${route.empresa_nombre || 'Transporte'}</span>
                            <div class="schedule-day">
                                <i class="fas fa-calendar-alt"></i>
                                ${schedule.dia_semana || 'No especificado'}
                            </div>
                        </div>

                        <div class="schedule-route-path">
                            <span>${boardStop}</span>
                            <i class="fas fa-arrow-right"></i>
                            <span>${alightStop}</span>
                        </div>

                        <div class="schedule-times">
                            <div class="time-group departure">
                                <i class="fas fa-map-marker-alt"></i>
                                <div class="time-text">
                                    <span class="time-label">${isTramo ? 'Abordaje' : 'Hora de salida'}</span>
                                    <span class="time-value">${salida || '--:--'}</span>
                                </div>
                            </div>
                            <div class="time-group arrival">
                                <i class="fas fa-map-marker-alt"></i>
                                <div class="time-text">
                                    <span class="time-label">${isTramo ? 'Bajada' : 'Tiempo de llegada'}</span>
                                    <span class="time-value">${llegada || '--:--'}</span>
                                </div>
                            </div>
                        </div>

                        <div class="schedule-card-body">
                            <div class="schedule-details">
                                <div class="detail-row">
                                    <i class="fas fa-redo" style="color:#FFA000;"></i>
                                    <span>Frecuencia: ${schedule.frecuencia || 'No especificada'}</span>
                                </div>
                                <div class="detail-row">
                                    <i class="fas fa-traffic-light" style="color:#e67e00;"></i>
                                    <span>${isTramo ? 'Paradas del tramo:' : 'Paradas:'}</span>
                                </div>
                                <ul class="stops-list">
                                    ${paradasHTML}
                                </ul>
                            </div>

                            <div class="driver-vehicle-info">
                                <div class="driver-vehicle-row">
                                    <i class="fas fa-user" style="color:#1565C0;"></i>
                                    <span><strong>Conductor: ${schedule.conductor_nombre || 'N/A'}</strong></span>
                                </div>
                                <div class="driver-vehicle-row">
                                    <i class="fas fa-bus" style="color:#1565C0;"></i>
                                    <span><strong>Vehículo: ${schedule.vehiculo_modelo || 'N/A'}</strong></span>
                                </div>
                                <div class="driver-vehicle-row">
                                    <i class="fas fa-ticket-alt" style="color:#E65100;"></i>
                                    <span><strong>Placa: ${schedule.vehiculo_placa || 'N/A'}</strong></span>
                                </div>
                                <div class="driver-vehicle-row">
                                    <i class="fas fa-users" style="color:#6A1B9A;"></i>
                                    <span><strong>Capacidad: ${schedule.vehiculo_capacidad || 'N/A'} pasajeros</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            contentHTML += `</div>`; // Close route-details div
            routeDetailsContainer.innerHTML = contentHTML;
        }

        // Mostrar indicador de carga
        function showLoading(show, container = document.body) {
            if (show) {
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'loading';
                loadingDiv.innerHTML = `
                    <div class="spinner"></div>
                    <div class="loading-text">Cargando...</div>
                `;
                
                // Limpiar solo si es el contenedor de resultados
                if (container === resultsContainer) {
                    container.innerHTML = '';
                }
                container.appendChild(loadingDiv);
            } else {
                const loadingElements = container.querySelectorAll('.loading');
                loadingElements.forEach(element => element.remove());
            }
        }

        // Mostrar notificación toast
        function showToast(message, duration = 3000) {
            toastMessage.textContent = message;
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, duration);
        }

        // ── Profile Panel ────────────────────────────────
        function openProfilePanel() {
            document.getElementById('profilePanel').classList.add('open');
            document.getElementById('profileOverlay').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeProfilePanel() {
            document.getElementById('profilePanel').classList.remove('open');
            document.getElementById('profileOverlay').style.display = 'none';
            document.body.style.overflow = '';
            // Reset to main view
            document.getElementById('panelViewsWrapper').classList.remove('show-terms');
        }

        function toggleConfiguracion() {
            const expanded = document.getElementById('configExpanded');
            const chevron = document.getElementById('configChevron');
            const isOpen = expanded.style.display === 'block';
            expanded.style.display = isOpen ? 'none' : 'block';
            chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(90deg)';
        }

        function showTerminos() {
            document.getElementById('panelViewTerms').querySelector('.terms-content').scrollTop = 0;
            document.getElementById('panelViewsWrapper').classList.add('show-terms');
        }

        function showMainView() {
            document.getElementById('panelViewsWrapper').classList.remove('show-terms');
        }

        // Cerrar con Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeProfilePanel();
        });

        // ── Mobile dropdown toggle ────────────────────
        const userDropdown = document.querySelector('.user-dropdown');
        const userBtn = document.querySelector('.user-btn');
        if (userBtn) {
            userBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('open');
            });
        }
        document.addEventListener('click', () => {
            if (userDropdown) userDropdown.classList.remove('open');
        });
        document.querySelector('.dropdown-content')?.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    </script>
</body>
</html>
