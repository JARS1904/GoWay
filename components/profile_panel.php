<!-- Profile Panel Overlay -->
<div id="profileOverlay" class="profile-overlay" onclick="closeProfilePanel()"></div>

<!-- Profile Side Panel -->
<div id="profilePanel" class="profile-panel">

    <div class="panel-views-wrapper" id="panelViewsWrapper">

    <!-- Vista principal del perfil -->
    <div id="panelViewMain" class="panel-view">
        <div class="profile-panel-header">
            <h2>Mi Perfil</h2>
            <button class="modal-close" onclick="closeProfilePanel()">&times;</button>
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
    // ── Profile Panel JS ────────────────────────────────
    function openProfilePanel() {
        document.getElementById('profilePanel').classList.add('open');
        document.getElementById('profileOverlay').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeProfilePanel() {
        document.getElementById('profilePanel').classList.remove('open');
        document.getElementById('profileOverlay').style.display = 'none';
        document.body.style.overflow = '';
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

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeProfilePanel();
    });
</script>
