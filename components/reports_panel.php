<!-- Panel lateral de Reportes (Historial) -->
<?php if ($_SESSION['rol'] != 3): ?>
<div class="notifications-overlay" id="reportsOverlay" onclick="closeReportsPanel()"></div>
<div class="notifications-panel" id="reportsPanel">
    <div class="notifications-header" style="border-bottom: 1px solid #f0f0f0;">
        <h3 class="reports-header-title" style="margin:0; font-size: 22px;">Reportes</h3>
        <button class="modal-close" style="display:flex; align-items:center; justify-content:center; border:none; background:transparent; font-size: 20px; color: #333;" onclick="closeReportsPanel()"><i class="fas fa-search"></i></button>
    </div>
    
    <div class="reports-filters" style="display:flex; gap:10px; padding: 16px 20px 8px; overflow-x: auto; white-space: nowrap; border-bottom: 1px solid #f0f0f0;">
        <button class="report-filter-btn active" onclick="filterReports('Todas', this)">Todas</button>
        <button class="report-filter-btn" onclick="filterReports('Crítica', this)">Crítica</button>
        <button class="report-filter-btn" onclick="filterReports('Alta', this)">Alta</button>
        <button class="report-filter-btn" onclick="filterReports('Media', this)">Media</button>
        <button class="report-filter-btn" onclick="filterReports('Baja', this)">Baja</button>
    </div>

    <div id="reportsPanelBody" class="notifications-body" style="padding: 16px 20px; position:relative; background: #f9fafb;">
        <div style="text-align:center; padding:40px 20px; color:#94a3b8;">
            <p>Cargando reportes...</p>
        </div>
    </div>

    <!-- Floating Action Button (+) -->
    <button class="reports-fab" onclick="openNewReportModal()">
        <i class="fas fa-plus"></i>
    </button>
</div>

<script>
    // ── Panel de Reportes JS ───────────────────────────────────────
    let allReports = [];
    let currentReportFilter = 'Todas';
    let currentReportPage = 1;
    const reportsPerPage = 10;

    function openReportsPanel() {
        if (typeof closeFavoritesPanel === 'function') closeFavoritesPanel();
        
        const notifPanel = document.getElementById('notificationsPanel');
        const notifOverlay = document.getElementById('notificationsOverlay');
        if (notifPanel) notifPanel.classList.remove('active');
        if (notifOverlay) notifOverlay.classList.remove('active');

        document.getElementById('reportsPanel').classList.add('active');
        document.getElementById('reportsOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
        loadReportsPanel();
    }

    function closeReportsPanel() {
        document.getElementById('reportsPanel').classList.remove('active');
        document.getElementById('reportsOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    async function loadReportsPanel() {
        const body = document.getElementById('reportsPanelBody');
        body.innerHTML = `
            <div style="text-align:center;padding:40px 20px;color:#94a3b8;">
                <i class="fas fa-spinner fa-spin" style="font-size:32px;margin-bottom:12px;display:block;color:#6366f1;"></i>
                <p>Cargando reportes...</p>
            </div>`;

        try {
            const res = await fetch(`../../api/usuario/reportes_api.php?action=get_reports&id_usuario=${typeof ID_USUARIO !== 'undefined' ? ID_USUARIO : <?php echo $_SESSION['id'] ?? 0; ?>}&limit=0`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (!data.success || !Array.isArray(data.reportes) || data.reportes.length === 0) {
                body.innerHTML = `
                    <div style="text-align:center;padding:40px 20px;color:#94a3b8;">
                        <i class="fas fa-clipboard-list" style="font-size:36px;margin-bottom:12px;display:block;"></i>
                        <p style="font-weight:600;margin-bottom:4px;">No has hecho reportes</p>
                        <p style="font-size:13px;">Toca el botón + para reportar un incidente</p>
                    </div>`;
                allReports = [];
                return;
            }

            allReports = data.reportes;
            renderReports('Todas');

        } catch (err) {
            console.error('Error cargando panel reportes:', err);
            body.innerHTML = `
                <div style="text-align:center;padding:40px 20px;color:#ef4444;">
                    <i class="fas fa-exclamation-circle" style="font-size:32px;margin-bottom:12px;display:block;"></i>
                    <p>Error al cargar reportes</p>
                </div>`;
        }
    }

    function filterReports(gravedadStr, btnElement) {
        document.querySelectorAll('.report-filter-btn').forEach(btn => btn.classList.remove('active'));
        if (btnElement) btnElement.classList.add('active');
        renderReports(gravedadStr);
    }

    function renderReports(filter, page = 1) {
        currentReportFilter = filter;
        currentReportPage = page;
        const body = document.getElementById('reportsPanelBody');
        let filtered = allReports;
        
        const normalize = str => (str || '').normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();

        if (filter !== 'Todas') {
            filtered = allReports.filter(r => normalize(r.gravedad) === normalize(filter));
        }

        if (filtered.length === 0) {
            body.innerHTML = `
                <div style="text-align:center; padding:40px 20px; color:#94a3b8;">
                    <i class="fas fa-folder-open" style="font-size:48px; margin-bottom:16px; opacity:0.5;"></i>
                    <p>No hay reportes para esta categoría.</p>
                </div>
            `;
            return;
        }

        const totalPages = Math.ceil(filtered.length / reportsPerPage);
        const startIndex = (page - 1) * reportsPerPage;
        const pagedReports = filtered.slice(startIndex, startIndex + reportsPerPage);

        let html = pagedReports.map((r, index) => {
            const realIndex = startIndex + index;
            const gravedadUpper = (r.gravedad || '').toUpperCase();
            let gravColor = '#94a3b8', gravBg = '#f1f5f9';
            const normalizedGravedad = normalize(r.gravedad);
            
            if (normalizedGravedad === 'baja') { gravColor = '#16a34a'; gravBg = '#dcfce7'; }
            if (normalizedGravedad === 'media') { gravColor = '#d97706'; gravBg = '#fef3c7'; }
            if (normalizedGravedad === 'alta') { gravColor = '#dc2626'; gravBg = '#fee2e2'; }
            if (normalizedGravedad === 'critica') { gravColor = '#991b1b'; gravBg = '#fecaca'; }

            const rawTipo = r.tipo_incidente || 'Incidente';
            const tipoCapitalized = rawTipo.charAt(0).toUpperCase() + rawTipo.slice(1).toLowerCase();

            return `
            <div class="report-card" onclick="toggleReportDetails(${realIndex})">
                <div class="report-card-header">
                    <div class="report-icon-box" style="background:${gravBg}; color:${gravColor};">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="report-header-info">
                        <h4 class="report-title">${tipoCapitalized}</h4>
                        <div class="report-meta">
                            <span class="report-badge" style="background:${gravBg}; color:${gravColor};">${gravedadUpper}</span>
                            <span class="report-date">${r.fecha_incidente || ''}</span>
                        </div>
                    </div>
                    <div class="report-status">Reportado</div>
                    <i class="fas fa-chevron-down report-chevron" id="reportChevron-${realIndex}"></i>
                </div>
                
                <div class="report-card-details" id="reportDetails-${realIndex}">
                    <div class="report-detail-item">
                        <i class="fas fa-car"></i>
                        <div class="report-detail-text">
                            <span class="report-detail-label">Vehiculo</span>
                            <span class="report-detail-val">${r.vehiculo_placa || ''} &middot; ${r.vehiculo_modelo || ''}</span>
                        </div>
                    </div>
                    <div class="report-detail-item">
                        <i class="fas fa-user"></i>
                        <div class="report-detail-text">
                            <span class="report-detail-label">Conductor</span>
                            <span class="report-detail-val">${r.conductor_nombre || 'N/A'}</span>
                        </div>
                    </div>
                    <div class="report-detail-item">
                        <i class="fas fa-route"></i>
                        <div class="report-detail-text">
                            <span class="report-detail-label">Ruta</span>
                            <span class="report-detail-val">${r.ruta_nombre || 'N/A'}</span>
                        </div>
                    </div>
                    <div class="report-detail-item">
                        <i class="far fa-clock"></i>
                        <div class="report-detail-text">
                            <span class="report-detail-label">Fecha y Hora</span>
                            <span class="report-detail-val">${r.fecha_incidente || ''}</span>
                        </div>
                    </div>
                    <div class="report-desc-box">
                        <i class="fas fa-align-left" style="color:#94a3b8; margin-top:2px;"></i>
                        <span>${r.descripcion || 'Sin descripción'}</span>
                    </div>
                </div>
            </div>`;
        }).join('');

        if (totalPages > 1) {
            html += `
            <div class="schedules-pagination-wrap" style="margin-top: 20px;">
                <div class="schedules-pagination">
                    <button class="pagination-btn" onclick="changeReportPage(-1)" ${page === 1 ? 'disabled' : ''}>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="pagination-text">Página ${page} de ${totalPages}</span>
                    <button class="pagination-btn" onclick="changeReportPage(1)" ${page === totalPages ? 'disabled' : ''}>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            `;
        }

        body.innerHTML = html;
    }

    function changeReportPage(direction) {
        const newPage = currentReportPage + direction;
        renderReports(currentReportFilter, newPage);
    }

    function toggleReportDetails(index) {
        const details = document.getElementById(`reportDetails-${index}`);
        const chevron = document.getElementById(`reportChevron-${index}`);
        
        if (details.classList.contains('open')) {
            details.classList.remove('open');
            chevron.style.transform = 'rotate(0deg)';
        } else {
            details.classList.add('open');
            chevron.style.transform = 'rotate(180deg)';
        }
    }
</script>
<?php endif; ?>
