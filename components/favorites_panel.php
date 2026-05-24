<!-- Panel lateral de Favoritas -->
<?php if ($_SESSION['rol'] != 3): ?>
<div class="notifications-overlay" id="favoritesOverlay" onclick="closeFavoritesPanel()"></div>
<div class="notifications-panel" id="favoritesPanel">
    <div class="notifications-header" style="border-bottom: 1px solid #f0f0f0;">
        <h3>Rutas favoritas</h3>
        <button class="modal-close" onclick="closeFavoritesPanel()">&times;</button>
    </div>
    <div id="favoritesPanelBody" class="notifications-body" style="padding: 16px;">
        <div style="text-align:center; padding:40px 20px; color:#94a3b8;">
            <i class="fas fa-heart" style="font-size:36px; margin-bottom:12px; display:block;"></i>
            <p>Cargando favoritas...</p>
        </div>
    </div>
</div>

<script>
    // ── Panel de Favoritas JS ───────────────────────────────────────
    function openFavoritesPanel() {
        // Cerrar notificaciones si están abiertas
        const notifPanel = document.getElementById('notificationsPanel');
        const notifOverlay = document.getElementById('notificationsOverlay');
        if (notifPanel) notifPanel.classList.remove('active');
        if (notifOverlay) notifOverlay.classList.remove('active');

        document.getElementById('favoritesPanel').classList.add('active');
        document.getElementById('favoritesOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
        loadFavoritesPanel();
    }

    function closeFavoritesPanel() {
        document.getElementById('favoritesPanel').classList.remove('active');
        document.getElementById('favoritesOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Mapa de datos de rutas favoritas (cacheado al abrir panel)
    window.favoritesDataMap = {};

    async function loadFavoritesPanel() {
        const body = document.getElementById('favoritesPanelBody');
        body.innerHTML = `
            <div style="text-align:center;padding:40px 20px;color:#94a3b8;">
                <i class="fas fa-spinner fa-spin" style="font-size:32px;margin-bottom:12px;display:block;color:#6366f1;"></i>
                <p>Cargando favoritas...</p>
            </div>`;

        try {
            const res = await fetch(`${typeof FAVORITES_URL !== 'undefined' ? FAVORITES_URL : '../../api/favorites_routes_api.php'}?action=get_favorites&id_usuario=${typeof ID_USUARIO !== 'undefined' ? ID_USUARIO : <?php echo $_SESSION['id'] ?? 0; ?>}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (!Array.isArray(data) || data.length === 0) {
                body.innerHTML = `
                    <div style="text-align:center;padding:40px 20px;color:#94a3b8;">
                        <i class="fas fa-heart-broken" style="font-size:36px;margin-bottom:12px;display:block;"></i>
                        <p style="font-weight:600;margin-bottom:4px;">Sin favoritas aún</p>
                        <p style="font-size:13px;">Marca rutas con ❤ para verlas aquí</p>
                    </div>`;
                window.favoritesDataMap = {};
                return;
            }

            // Cachear datos completos para acceso offline
            window.favoritesDataMap = {};
            data.forEach(route => {
                // Normalizar paradas igual que processRoutes()
                if (typeof route.paradas === 'string') {
                    route.paradas = route.paradas.split(', ').filter(p => p !== '');
                }
                if (!route.paradas_ruta) route.paradas_ruta = [];
                if (!route.horarios)    route.horarios    = [];
                route.es_tramo = 0;
                window.favoritesDataMap[route.id_ruta] = route;
            });

            body.innerHTML = data.map(route => {
                const scheduleCount = Array.isArray(route.horarios) ? route.horarios.length : 0;
                return `
                <div class="fav-panel-card route-card" data-route-id="${route.id_ruta}" onclick="selectFavoriteRoute(${route.id_ruta})">
                    <div class="route-card-header">
                        <div class="route-card-title">
                            <i class="fas fa-bus"></i>
                            <span class="route-company">${route.empresa_nombre || 'Transporte'}</span>
                        </div>
                    </div>
                    <div class="route-path">
                        <div class="route-origin">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${route.origen || '—'}</span>
                        </div>
                        <i class="fas fa-arrow-right arrow"></i>
                        <div class="route-destination">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${route.destino || '—'}</span>
                        </div>
                    </div>
                    <div class="route-card-divider"></div>
                    <div class="route-schedule-block">
                        <div class="route-schedule">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Horarios disponibles</span>
                            <span class="route-schedule-count">${scheduleCount}</span>
                        </div>
                    </div>
                    <div class="route-card-footer">
                        <button class="favorite-btn active" data-route-id="${route.id_ruta}" title="Eliminar de favoritas"
                            onclick="event.stopPropagation(); removeFavoriteFromPanel(${route.id_ruta}, this)">
                            <i class="fas fa-heart"></i>
                        </button>
                        <button class="btn-details" onclick="event.stopPropagation();selectFavoriteRoute(${route.id_ruta})">Ver detalles</button>
                    </div>
                </div>`;
            }).join('');


        } catch (err) {
            console.error('Error cargando panel favoritas:', err);
            body.innerHTML = `
                <div style="text-align:center;padding:40px 20px;color:#ef4444;">
                    <i class="fas fa-exclamation-circle" style="font-size:32px;margin-bottom:12px;display:block;"></i>
                    <p>Error al cargar favoritas</p>
                </div>`;
        }
    }

    async function selectFavoriteRoute(routeId) {
        closeFavoritesPanel();

        // 1. Buscar en rutas ya buscadas por el usuario
        let route = typeof routes !== 'undefined' ? routes.find(r => r.id_ruta === routeId) : null;

        // 2. Buscar en el mapa cacheado del panel de favoritas
        if (!route && window.favoritesDataMap && window.favoritesDataMap[routeId]) {
            route = window.favoritesDataMap[routeId];
        }

        // 3. Como último recurso, pedir a la API (ahora existe route_detail)
        if (!route) {
            try {
                const res = await fetch(`${typeof API_URL !== 'undefined' ? API_URL : '../../api/routes_api.php'}?action=route_detail&id_ruta=${routeId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (Array.isArray(data) && data.length > 0) {
                        route = typeof processRoutes !== 'undefined' ? processRoutes(data)[0] : data[0];
                    }
                }
            } catch (e) { console.error('Error fetching route detail:', e); }
        }

        if (route) {
            if (typeof selectedRouteId !== 'undefined') selectedRouteId = route.id_ruta;
            if (typeof showRouteDetails === 'function') showRouteDetails(route);
            document.getElementById('routeDetailsContainer')?.scrollIntoView({ behavior: 'smooth' });
        } else {
            if (typeof showToast === 'function') showToast('No se pudo cargar el detalle de la ruta');
        }
    }

    // Elimina una ruta de favoritas directamente desde el panel
    async function removeFavoriteFromPanel(routeId, btnElement) {
        try {
            const response = await fetch(typeof FAVORITES_URL !== 'undefined' ? FAVORITES_URL : '../../api/favorites_routes_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ action: 'remove_favorite', id_usuario: typeof ID_USUARIO !== 'undefined' ? ID_USUARIO : <?php echo $_SESSION['id'] ?? 0; ?>, id_ruta: routeId })
            });
            const data = await response.json();
            if (data.success) {
                if (typeof favorites !== 'undefined') favorites.delete(routeId);
                if (window.favoritesDataMap) delete window.favoritesDataMap[routeId];
                // Quitar la tarjeta del panel con animación
                const card = btnElement.closest('.fav-panel-card');
                if (card) {
                    card.style.transition = 'opacity 0.25s, transform 0.25s';
                    card.style.opacity = '0';
                    card.style.transform = 'translateX(30px)';
                    setTimeout(() => {
                        card.remove();
                        // Si no quedan tarjetas, mostrar estado vacío
                        const body = document.getElementById('favoritesPanelBody');
                        if (body && !body.querySelector('.fav-panel-card')) {
                            body.innerHTML = `
                                <div style="text-align:center;padding:40px 20px;color:#94a3b8;">
                                    <i class="fas fa-heart-broken" style="font-size:36px;margin-bottom:12px;display:block;"></i>
                                    <p style="font-weight:600;margin-bottom:4px;">Sin favoritas aún</p>
                                    <p style="font-size:13px;">Marca rutas con ❤ para verlas aquí</p>
                                </div>`;
                        }
                    }, 260);
                }
                // Sincronizar con tarjetas de la lista principal
                const mainBtn = document.querySelector(`.route-card:not(.fav-panel-card) .favorite-btn[data-route-id="${routeId}"]`);
                if (mainBtn) {
                    if (typeof favorites !== 'undefined') favorites.delete(routeId);
                    mainBtn.classList.remove('active');
                    mainBtn.innerHTML = '<i class="far fa-heart"></i>';
                    mainBtn.title = 'Agregar a favoritas';
                }
                if (typeof showToast === 'function') showToast('Eliminado de favoritas');
            } else {
                if (typeof showToast === 'function') showToast('Error al eliminar favorita');
            }
        } catch(e) {
            if (typeof showToast === 'function') showToast('Error de conexión');
        }
    }
</script>
<?php endif; ?>
