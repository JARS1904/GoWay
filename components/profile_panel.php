<!-- Profile Panel Overlay -->
<div id="profileOverlay" class="profile-overlay" onclick="closeProfilePanel()"></div>

<!-- Profile Side Panel -->
<div id="profilePanel" class="profile-panel">

    <div class="panel-views-wrapper" id="panelViewsWrapper">

    <!-- Vista principal del perfil -->
    <div id="panelViewMain" class="panel-view" style="background-color: #FAFAFA;">
        <div class="profile-panel-header" style="border-bottom: none; padding-top: 24px;">
            <h2 style="font-size: 24px; font-weight: 700;">Mi perfil</h2>
        </div>

        <div class="profile-avatar-section" style="background: transparent; padding-top: 10px; padding-bottom: 20px;">
            <?php if (!empty($_user_foto)): ?>
                <img src="../../assets/images/profiles/<?php echo htmlspecialchars($_user_foto); ?>" class="profile-avatar-circle profile-avatar-photo" alt="foto" style="width: 100px; height: 100px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 3px solid white;">
            <?php else: ?>
                <div class="profile-avatar-circle" style="width: 100px; height: 100px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 3px solid white; font-size: 36px;"><?php echo htmlspecialchars(strtoupper(mb_substr($_SESSION['nombre'] ?? 'U', 0, 1))); ?></div>
            <?php endif; ?>
            <div class="profile-user-name" style="font-size: 24px; margin-top: 16px; margin-bottom: 4px;"><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></div>
            <div class="profile-user-email" style="font-size: 14px; color: #888;"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></div>
        </div>

        <div class="profile-stats-container">
            <div class="profile-stat-card">
                <div class="profile-stat-number" id="profileFavCount">0</div>
                <div class="profile-stat-label">RUTAS FAVORITAS</div>
            </div>
            <div class="profile-stat-card">
                <div class="profile-stat-number" id="profileRepCount">0</div>
                <div class="profile-stat-label">REPORTES CREADOS</div>
            </div>
        </div>

        <div class="profile-menu-container">
            <div class="profile-menu-card">
                <div class="profile-menu-item ios-item">
                    <div class="ios-icon-box" style="background: #eef2ff; color: #4f46e5;">
                        <i class="fas fa-id-badge"></i>
                    </div>
                    <span class="ios-label">Tarjeta</span>
                    <i class="fas fa-chevron-right ios-chevron"></i>
                </div>
                <div class="ios-divider"></div>
                <div class="profile-menu-item ios-item" onclick="showMap()">
                    <div class="ios-icon-box" style="background: #eef2ff; color: #4f46e5;">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <span class="ios-label">Mapa</span>
                    <i class="fas fa-chevron-right ios-chevron"></i>
                </div>
                <div class="ios-divider"></div>
                <div class="profile-menu-item ios-item" onclick="showTerminos()">
                    <div class="ios-icon-box" style="background: #eef2ff; color: #4f46e5;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <span class="ios-label">Términos y condiciones</span>
                    <i class="fas fa-chevron-right ios-chevron"></i>
                </div>
                <div class="ios-divider"></div>
                <div class="profile-menu-item ios-item" onclick="showConfiguracion()">
                    <div class="ios-icon-box" style="background: #eef2ff; color: #4f46e5;">
                        <i class="fas fa-cog"></i>
                    </div>
                    <span class="ios-label">Configuración</span>
                    <i class="fas fa-chevron-right ios-chevron"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Vista de Términos y condiciones -->
    <div id="panelViewTerms" class="panel-view" style="background-color: #FAFAFA;">
        <div class="panel-view-header">
            <button class="panel-back-btn" onclick="showMainView()">
                <i class="fas fa-chevron-left"></i>
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

    <!-- Vista de Configuración -->
    <div id="panelViewConfig" class="panel-view" style="background-color: #FAFAFA;">
        <div class="panel-view-header" style="border-bottom: none; padding-top: 24px;">
            <button class="panel-back-btn" onclick="showMainView()" style="background: none; font-size: 20px;">
                <i class="fas fa-chevron-left" style="color: #000;"></i>
            </button>
            <h2 style="font-size: 20px; font-weight: 700; text-align: left; padding-left: 10px;">Configuración</h2>
        </div>
        
        <div class="config-content" style="padding: 0 20px; overflow-y: auto; flex: 1;">
            
            <h3 class="config-section-title">Apariencia</h3>
            <div class="profile-menu-card" style="margin-bottom: 24px;">
                <div class="profile-menu-item ios-item">
                    <div class="ios-icon-box" style="background: #eef2ff; color: #4f46e5;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="ios-text-box">
                        <span class="ios-label">Ocultar horarios</span>
                        <span class="ios-sublabel" id="lblOcultarHorarios">Mostrando todos los horarios</span>
                    </div>
                    <label class="ios-toggle">
                        <input type="checkbox" id="toggleOcultarHorarios" onchange="handleToggleOcultarHorarios(this)">
                        <span class="ios-toggle-slider"></span>
                    </label>
                </div>
                <div class="ios-divider"></div>
                <div class="profile-menu-item ios-item">
                    <div class="ios-icon-box" style="background: #eef2ff; color: #4f46e5;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="ios-text-box">
                        <span class="ios-label">Nombres de paradas</span>
                        <span class="ios-sublabel" id="lblNombresParadas">Mostrando nombres en el mapa</span>
                    </div>
                    <label class="ios-toggle">
                        <input type="checkbox" id="toggleNombresParadas" checked onchange="handleToggleNombresParadas(this)">
                        <span class="ios-toggle-slider"></span>
                    </label>
                </div>
            </div>

            <h3 class="config-section-title">Información</h3>
            <div class="profile-menu-card" style="margin-bottom: 24px;">
                <div class="profile-menu-item ios-item">
                    <div class="ios-icon-box" style="background: #eef2ff; color: #4f46e5;">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="ios-text-box">
                        <span class="ios-label">Versión de la Aplicación</span>
                        <span class="ios-sublabel">2.0.0</span>
                    </div>
                </div>
            </div>

            <h3 class="config-section-title">Sesión</h3>
            <div class="profile-menu-card" style="margin-bottom: 24px;">
                <a href="../logout.php" class="profile-menu-item ios-item" style="text-decoration: none;">
                    <div class="ios-icon-box" style="background: #fef2f2; color: #ef4444;">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <span class="ios-label" style="color: #ef4444; font-weight: 500;">Cerrar Sesión</span>
                </a>
            </div>

        </div>
    </div>

    <!-- Vista del Mapa -->
    <div id="panelViewMap" class="panel-view" style="background-color: #FAFAFA;">
        <div class="panel-view-header" style="border-bottom: none; padding-top: 24px;">
            <button class="panel-back-btn" onclick="showMainView()" style="background: none; font-size: 20px;">
                <i class="fas fa-chevron-left" style="color: #000;"></i>
            </button>
            <h2 style="font-size: 20px; font-weight: 700; text-align: left; padding-left: 10px;">Tu Ubicación</h2>
        </div>
        <div style="flex: 1; position: relative;">
            <div id="profileMapContainer" style="width: 100%; height: 100%;"></div>
        </div>
    </div>

    </div><!-- /panel-views-wrapper -->
</div><!-- /profilePanel -->

<script>
    // ── Profile Panel JS ────────────────────────────────
    let profileMapInstance = null;
    let profileUserMarker = null;

    function openProfilePanel() {
        if (typeof closeReportsPanel === 'function') closeReportsPanel();
        if (typeof closeFavoritesPanel === 'function') closeFavoritesPanel();
        
        const notifPanel = document.getElementById('notificationsPanel');
        const notifOverlay = document.getElementById('notificationsOverlay');
        if (notifPanel) notifPanel.classList.remove('active');
        if (notifOverlay) notifOverlay.classList.remove('active');

        document.getElementById('profilePanel').classList.add('open');
        document.getElementById('profileOverlay').style.display = 'block';
        document.body.style.overflow = 'hidden';
        updateProfileCounts();
    }

    function closeProfilePanel() {
        document.getElementById('profilePanel').classList.remove('open');
        document.getElementById('profileOverlay').style.display = 'none';
        document.body.style.overflow = '';
        setTimeout(() => {
            document.getElementById('panelViewsWrapper').classList.remove('show-terms', 'show-config', 'show-map');
            if (profileMapInstance) {
                profileMapInstance.remove();
                profileMapInstance = null;
            }
        }, 350); // wait for panel animation to finish before resetting view
    }

    function showTerminos() {
        document.getElementById('panelViewTerms').querySelector('.terms-content').scrollTop = 0;
        document.getElementById('panelViewsWrapper').classList.remove('show-config', 'show-map');
        document.getElementById('panelViewsWrapper').classList.add('show-terms');
    }

    function showConfiguracion() {
        document.getElementById('panelViewConfig').querySelector('.config-content').scrollTop = 0;
        document.getElementById('panelViewsWrapper').classList.remove('show-terms', 'show-map');
        document.getElementById('panelViewsWrapper').classList.add('show-config');
    }

    function showMap() {
        document.getElementById('panelViewsWrapper').classList.remove('show-terms', 'show-config');
        document.getElementById('panelViewsWrapper').classList.add('show-map');
        
        // Inicializar mapa de perfil después de la transición
        setTimeout(() => {
            if (!profileMapInstance) {
                profileMapInstance = L.map('profileMapContainer', {
                    zoomControl: false
                }).setView([20.659698, -103.349609], 13); // Default a Guadalajara

                L.control.zoom({ position: 'bottomright' }).addTo(profileMapInstance);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
                    subdomains: 'abcd',
                    maxZoom: 19
                }).addTo(profileMapInstance);
            }

            // Geolocation
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    if (profileUserMarker) {
                        profileMapInstance.removeLayer(profileUserMarker);
                    }
                    
                    profileUserMarker = L.circleMarker([lat, lng], {
                        radius: 8,
                        fillColor: "#2962FF",
                        color: "#fff",
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(profileMapInstance);
                    
                    profileMapInstance.setView([lat, lng], 15);
                }, err => {
                    console.log("No se pudo obtener la ubicación para el mapa de perfil:", err);
                });
            }

            profileMapInstance.invalidateSize();
        }, 350);
    }

    function showMainView() {
        document.getElementById('panelViewsWrapper').classList.remove('show-terms', 'show-config', 'show-map');
    }

    async function updateProfileCounts() {
        const favCount = (typeof favorites !== 'undefined') ? favorites.size : 0;
        document.getElementById('profileFavCount').textContent = favCount;
        
        // Cargar reportes para tener la cantidad real
        try {
            const userId = typeof ID_USUARIO !== 'undefined' ? ID_USUARIO : <?php echo $_SESSION['id'] ?? 0; ?>;
            const res = await fetch(`../../api/usuario/reportes_api.php?action=get_reports&id_usuario=${userId}&limit=0`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            const repCount = (data.success && Array.isArray(data.reportes)) ? data.reportes.length : 0;
            document.getElementById('profileRepCount').textContent = repCount;
            // Also store for future use
            if (typeof window.allReports !== 'undefined') window.allReports = data.reportes;
        } catch (e) {
            console.error('Error al cargar cantidad de reportes:', e);
            document.getElementById('profileRepCount').textContent = (typeof window.allReports !== 'undefined') ? window.allReports.length : 0;
        }
    }

    // Toggle handlers
    window.isHideSchedulesEnabled = false;
    window.isShowStopNamesEnabled = true;

    function handleToggleOcultarHorarios(checkbox) {
        window.isHideSchedulesEnabled = checkbox.checked;
        const lbl = document.getElementById('lblOcultarHorarios');
        lbl.textContent = window.isHideSchedulesEnabled ? 'Ocultando horarios sin asignación' : 'Mostrando todos los horarios';
        
        // Trigger a re-render of schedules if the function exists
        if (typeof window.updateSchedulesDisplay === 'function') {
            window.updateSchedulesDisplay();
        } else if (typeof renderSchedulesList === 'function' && typeof currentSelectedRouteData !== 'undefined' && currentSelectedRouteData) {
            renderSchedulesList(currentSelectedRouteData);
        }
    }

    function handleToggleNombresParadas(checkbox) {
        window.isShowStopNamesEnabled = checkbox.checked;
        const lbl = document.getElementById('lblNombresParadas');
        lbl.textContent = window.isShowStopNamesEnabled ? 'Mostrando nombres en el mapa' : 'Nombres ocultos en el mapa';
        
        // Refresh map tooltips if the map exists
        if (typeof window.refreshStopTooltips === 'function') {
            window.refreshStopTooltips();
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeProfilePanel();
    });
</script>
