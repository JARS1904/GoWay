<!-- ── Modal Resumen Ejecutivo ── -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<div id="summaryModal">
    <div class="summary-container" id="summaryPrintArea">
        <!-- Header -->
        <div class="summary-header">
            <div style="display:flex;align-items:center;gap:14px">
                <img src="../../assets/images/logo_new.png" alt="GoWay" style="width:42px;height:42px;object-fit:contain;flex-shrink:0;">
                <div>
                    <h2 style="margin:0 0 4px;font-size:1.35rem;">Resumen ejecutivo de reportes</h2>
                    <p class="summary-meta" id="summaryPeriodo" style="display:none"></p>
                    <p class="summary-meta" id="summaryGenerado">Generado el: —</p>
                </div>
            </div>
            <button class="summary-close-btn" onclick="closeSummaryModal()">✕</button>
        </div>

        <!-- Body -->
        <div class="summary-body" id="summaryBody">
            <div class="summary-loading">
                <span class="material-icons">sync</span>
                Generando resumen...
            </div>
        </div>

        <!-- Footer -->
        <div class="summary-footer">
            <small>GoWay - Sistema de Transporte Público</small>
            <button class="btn-download-pdf" id="btnDownloadPdf" onclick="downloadSummaryPDF()">
                <span class="material-icons">download</span>
                Descargar PDF
            </button>
        </div>
    </div>
</div>

<script>
    const TIPO_LABELS = {
        accidente: 'Accidente',
        averia:    'Avería Mecánica',
        retraso:   'Retraso Significativo',
        cliente:   'Incidente con Cliente',
        otro:      'Otro'
    };

    const GW_COLORS = {
        blue: '#0660fe', green: '#10b981', orange: '#f59e0b', red: '#ef4444', gray: '#e2e8f0', purple: '#8b5cf6', text: '#1a1c23'
    };

    let sumChartFlotaInstance = null;
    let sumChartAsigInstance = null;
    let sumChartEstRutasInstance = null;
    let sumChartTopRutasInstance = null;
    let sumChartBandasInstance = null;
    let sumChartTipoDiaInstance = null;
    let sumChartConductoresInstance = null;

    // Abrir modal y cargar datos
    function openSummaryModal() {
        document.getElementById('summaryModal').classList.add('active');
        
        document.getElementById('summaryBody').innerHTML = `
            <div class="summary-loading">
                <span class="material-icons">sync</span>
                Generando resumen...
            </div>`;
        document.getElementById('summaryPeriodo').textContent  = 'Cargando período...';
        document.getElementById('summaryGenerado').textContent = 'Generado el: —';

        // Deshabilitar botón de PDF mientras carga
        document.getElementById('btnDownloadPdf').disabled = true;

        const fetchSummary = fetch('../../api/reportes_api.php?action=get_summary').then(r => r.json());
        const fetchKPIsDashboard = fetch('../../api/kpis_api.php?seccion=dashboard').then(r => r.json());
        const fetchKPIsRutas = fetch('../../api/kpis_api.php?seccion=rutas').then(r => r.json());
        const fetchKPIsHorarios = fetch('../../api/kpis_api.php?seccion=horarios').then(r => r.json());
        const fetchKPIsAsig = fetch('../../api/kpis_api.php?seccion=asignaciones').then(r => r.json());

        Promise.all([fetchSummary, fetchKPIsDashboard, fetchKPIsRutas, fetchKPIsHorarios, fetchKPIsAsig])
            .then(([summaryData, dashData, rutasData, horData, asigData]) => {
                if (!summaryData.success) throw new Error(summaryData.error || 'Error al obtener datos del resumen');
                
                renderSummaryHTML(summaryData);
                renderKPICharts(dashData, rutasData, horData, asigData);
                document.getElementById('btnDownloadPdf').disabled = false;
            })
            .catch(err => {
                document.getElementById('summaryBody').innerHTML =
                    `<p style="color:#ef4444;padding:20px;">Error: ${err.message}</p>`;
            });
    }

    function closeSummaryModal() {
        document.getElementById('summaryModal').classList.remove('active');
    }

    // Cerrar al hacer click fuera del contenedor
    document.getElementById('summaryModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSummaryModal();
        }
    });

    function renderSummaryHTML(data) {
        const t      = data.totales;
        const total  = parseInt(t.total)      || 0;
        const pend   = parseInt(t.pendientes) || 0;
        const proc   = parseInt(t.en_proceso) || 0;
        const resu   = parseInt(t.resueltos)  || 0;

        // Período
        const desde = data.rango_fechas?.desde ? new Date(data.rango_fechas.desde).toLocaleDateString('es-MX',{day:'2-digit',month:'short',year:'numeric'}) : '—';
        const hasta = data.rango_fechas?.hasta ? new Date(data.rango_fechas.hasta).toLocaleDateString('es-MX',{day:'2-digit',month:'short',year:'numeric'}) : '—';
        document.getElementById('summaryPeriodo').textContent  = `Período: ${desde} — ${hasta}`;

        const genTs = new Date(data.generado_en);
        document.getElementById('summaryGenerado').textContent =
            `Generado el: ${genTs.toLocaleDateString('es-MX',{day:'2-digit',month:'long',year:'numeric'})} a las ${genTs.toLocaleTimeString('es-MX',{hour:'2-digit',minute:'2-digit'})}`;

        // ── Barras de gravedad ──
        const gravedadOrder = ['critica','alta','media','baja'];
        const gMap = {};
        (data.por_gravedad || []).forEach(g => gMap[g.gravedad] = parseInt(g.total));
        const maxG = Math.max(...Object.values(gMap), 1);
        const gravedadHTML = gravedadOrder.map(g => {
            const cnt  = gMap[g] || 0;
            const pct  = Math.round((cnt / maxG) * 100);
            const label = g === 'critica' ? 'Crítica' : g.charAt(0).toUpperCase() + g.slice(1);
            return `<div class="gravedad-row">
                <span class="gravedad-label">${label}</span>
                <div class="gravedad-bar-wrap">
                    <div class="gravedad-bar bar-${g}" style="width:${pct}%"></div>
                </div>
                <span class="gravedad-count">${cnt}</span>
            </div>`;
        }).join('');

        // ── Tabla tipos ──
        const tipoRows = (data.por_tipo || []).map((row, i) => `
            <tr>
                <td><span class="badge-rank">#${i+1}</span></td>
                <td>${TIPO_LABELS[row.tipo_incidente] || row.tipo_incidente}</td>
                <td><strong>${row.total}</strong></td>
                <td>${total > 0 ? Math.round((row.total/total)*100) : 0}%</td>
            </tr>`).join('') || '<tr><td colspan="4" style="text-align:center;color:#94a3b8">Sin datos</td></tr>';

        // ── Top conductores ──
        const condRows = (data.top_conductores || []).map((row, i) => `
            <tr>
                <td><span class="badge-rank">#${i+1}</span></td>
                <td>${row.nombre}</td>
                <td><strong>${row.total}</strong></td>
            </tr>`).join('') || '<tr><td colspan="3" style="text-align:center;color:#94a3b8">Sin datos</td></tr>';

        // ── Top rutas ──
        const rutaRows = (data.top_rutas || []).map((row, i) => `
            <tr>
                <td><span class="badge-rank">#${i+1}</span></td>
                <td>${row.nombre}</td>
                <td><strong>${row.total}</strong></td>
            </tr>`).join('') || '<tr><td colspan="3" style="text-align:center;color:#94a3b8">Sin datos</td></tr>';

        // ── Top vehículos ──
        const vehicRows = (data.top_vehiculos || []).map((row, i) => `
            <tr>
                <td><span class="badge-rank">#${i+1}</span></td>
                <td>${row.vehiculo}</td>
                <td><strong>${row.total}</strong></td>
            </tr>`).join('') || '<tr><td colspan="3" style="text-align:center;color:#94a3b8">Sin datos</td></tr>';

        // ── Días de la semana ──
        const maxDia = Math.max(...(data.por_dia_semana || []).map(d => d.total), 1);
        const diaColors = ['#6366f1','#3b82f6','#06b6d4','#10b981','#f59e0b','#ef4444','#8b5cf6'];
        const diasHTML = (data.por_dia_semana || []).map((d, i) => {
            const pct = Math.round((d.total / maxDia) * 100);
            return `<div class="gravedad-row">
                <span class="gravedad-label" style="width:36px;font-size:12px">${d.dia}</span>
                <div class="gravedad-bar-wrap">
                    <div class="gravedad-bar" style="width:${pct}%;background:${diaColors[i % diaColors.length]}"></div>
                </div>
                <span class="gravedad-count">${d.total}</span>
            </div>`;
        }).join('');

        document.getElementById('summaryBody').innerHTML = `
            <!-- KPIs -->
            <div class="summary-kpis">
                <div class="kpi-card kpi-total">
                    <span class="kpi-val">${total}</span>
                    <span class="kpi-label">Total</span>
                </div>
                <div class="kpi-card kpi-pending">
                    <span class="kpi-val">${pend}</span>
                    <span class="kpi-label">Pendientes</span>
                </div>
                <div class="kpi-card kpi-process">
                    <span class="kpi-val">${proc}</span>
                    <span class="kpi-label">En Proceso</span>
                </div>
                <div class="kpi-card kpi-resolved">
                    <span class="kpi-val">${resu}</span>
                    <span class="kpi-label">Resueltos</span>
                </div>
            </div>

            <!-- Gravedad -->
            <div class="summary-section">
                <h4>Por Nivel de Gravedad</h4>
                ${gravedadHTML}
            </div>

            <!-- Grid estructurado en filas para asegurar alineación horizontal -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px 24px; align-items:start;">
                <!-- Fila 1 -->
                <div class="summary-section" style="margin:0;">
                    <h4>Por Tipo de Incidente</h4>
                    <table class="summary-table">
                        <thead><tr><th>#</th><th>Tipo</th><th>Total</th><th>%</th></tr></thead>
                        <tbody>${tipoRows}</tbody>
                    </table>
                </div>
                <div class="summary-section" style="margin:0;">
                    <h4>Top Conductores</h4>
                    <table class="summary-table">
                        <thead><tr><th>#</th><th>Conductor</th><th>Reportes</th></tr></thead>
                        <tbody>${condRows}</tbody>
                    </table>
                </div>

                <!-- Fila 2 -->
                <div class="summary-section" style="margin:0;">
                    <h4>Top Vehículos</h4>
                    <table class="summary-table">
                        <thead><tr><th>#</th><th>Vehículo</th><th>Reportes</th></tr></thead>
                        <tbody>${vehicRows}</tbody>
                    </table>
                </div>
                <div class="summary-section" style="margin:0;">
                    <h4>Top Rutas</h4>
                    <table class="summary-table">
                        <thead><tr><th>#</th><th>Ruta</th><th>Reportes</th></tr></thead>
                        <tbody>${rutaRows}</tbody>
                    </table>
                </div>

                <!-- Fila 3 -->
                <div></div>
                <div class="summary-section" style="margin:0;">
                    <h4>Incidentes por Día</h4>
                    ${diasHTML}
                </div>
            </div>

            <!-- Gráficos KPI Dashboard -->
            <div class="summary-section" style="margin-top: 30px;">
                <h4>Indicadores Clave (KPIs) Generales</h4>
                <div style="display:grid; grid-template-columns:repeat(2, minmax(220px, 1fr)); gap:30px 20px; justify-items:center; max-width:600px; margin:0 auto;">
                    <div style="width:220px; text-align:center;">
                        <h5 style="margin:0 0 10px; font-size:12px; color:#64748b;">Estado de la flota</h5>
                        <div style="height:220px; position:relative;"><canvas id="sumChartFlota"></canvas></div>
                    </div>
                    <div style="width:220px; text-align:center;">
                        <h5 style="margin:0 0 10px; font-size:12px; color:#64748b;">Asignaciones</h5>
                        <div style="height:220px; position:relative;"><canvas id="sumChartAsig"></canvas></div>
                    </div>
                    <div style="width:220px; text-align:center;">
                        <h5 style="margin:0 0 10px; font-size:12px; color:#64748b;">Estado de rutas</h5>
                        <div style="height:220px; position:relative;"><canvas id="sumChartEstRutas"></canvas></div>
                    </div>
                    <div style="width:220px; text-align:center;">
                        <h5 style="margin:0 0 10px; font-size:12px; color:#64748b;">Top Rutas</h5>
                        <div style="height:220px; position:relative;"><canvas id="sumChartTopRutas"></canvas></div>
                    </div>
                    <div style="width:220px; text-align:center;">
                        <h5 style="margin:0 0 10px; font-size:12px; color:#64748b;">Bandas Horarias</h5>
                        <div style="height:220px; position:relative;"><canvas id="sumChartBandas"></canvas></div>
                    </div>
                    <div style="width:220px; text-align:center;">
                        <h5 style="margin:0 0 10px; font-size:12px; color:#64748b;">Tipos de día</h5>
                        <div style="height:220px; position:relative;"><canvas id="sumChartTipoDia"></canvas></div>
                    </div>
                    <div style="width:220px; text-align:center;">
                        <h5 style="margin:0 0 10px; font-size:12px; color:#64748b;">Carga de conductores</h5>
                        <div style="height:220px; position:relative;"><canvas id="sumChartConductores"></canvas></div>
                    </div>
                </div>
            </div>`;
    }

    function renderKPICharts(dashData, rutasData, horData, asigData) {
        if (sumChartFlotaInstance) sumChartFlotaInstance.destroy();
        if (sumChartAsigInstance) sumChartAsigInstance.destroy();
        if (sumChartEstRutasInstance) sumChartEstRutasInstance.destroy();
        if (sumChartTopRutasInstance) sumChartTopRutasInstance.destroy();
        if (sumChartBandasInstance) sumChartBandasInstance.destroy();
        if (sumChartTipoDiaInstance) sumChartTipoDiaInstance.destroy();
        if (sumChartConductoresInstance) sumChartConductoresInstance.destroy();

        const baseOptions = {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 10, boxWidth: 10, font: { size: 10 }, color: '#334155' } },
                tooltip: { backgroundColor: 'rgba(255,255,255,0.95)', titleColor: '#1e293b', bodyColor: '#475569', borderColor: '#e2e8f0', borderWidth: 1, padding: 10, boxPadding: 6, usePointStyle: true }
            }
        };

        const barOptionsH = {
            responsive: true, maintainAspectRatio: false, indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { display: false }, y: { ticks: { font: { size: 10 } } } }
        };
        const barOptionsV = {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { ticks: { font: { size: 10 } } }, y: { display: false } }
        };

        // Estado de la flota (Dashboard)
        if (dashData.flota_dona && dashData.flota_dona.data.some(v => v > 0)) {
            const labelsWithData = dashData.flota_dona.labels.map((l, i) => `${l}: ${dashData.flota_dona.data[i]}`);
            sumChartFlotaInstance = new Chart(document.getElementById('sumChartFlota'), {
                type: 'doughnut',
                data: { labels: labelsWithData, datasets: [{ data: dashData.flota_dona.data, backgroundColor: [GW_COLORS.blue, GW_COLORS.red], borderColor: '#fff', borderWidth: 2 }] },
                options: baseOptions
            });
        }

        // Asignaciones (Dashboard)
        if (dashData.asig_dias && dashData.asig_dias.data.some(v => v > 0)) {
            const estadoColorsMap = { 'Programado': '#64748b', 'Completado': GW_COLORS.green, 'En Ruta': GW_COLORS.blue, 'Cancelado': GW_COLORS.red, 'Retrasado': GW_COLORS.orange, 'Inactiva/Cancelada': GW_COLORS.red };
            const bgColors = dashData.asig_dias.labels.map(l => estadoColorsMap[l] || GW_COLORS.gray);
            const labelsWithData = dashData.asig_dias.labels.map((l, i) => `${l}: ${dashData.asig_dias.data[i]}`);
            sumChartAsigInstance = new Chart(document.getElementById('sumChartAsig'), {
                type: 'doughnut',
                data: { labels: labelsWithData, datasets: [{ data: dashData.asig_dias.data, backgroundColor: bgColors, borderColor: '#fff', borderWidth: 2 }] },
                options: baseOptions
            });
        }

        // Estado de Rutas (Rutas)
        if (rutasData.estado_rutas && rutasData.estado_rutas.data.some(v => v > 0)) {
            const labelsWithData = rutasData.estado_rutas.labels.map((l, i) => `${l}: ${rutasData.estado_rutas.data[i]}`);
            sumChartEstRutasInstance = new Chart(document.getElementById('sumChartEstRutas'), {
                type: 'doughnut',
                data: { labels: labelsWithData, datasets: [{ data: rutasData.estado_rutas.data, backgroundColor: [GW_COLORS.blue, GW_COLORS.red], borderColor: '#fff', borderWidth: 2 }] },
                options: baseOptions
            });
        }

        // Top Rutas (Rutas)
        if (rutasData.top_paradas && rutasData.top_paradas.data.some(v => v > 0)) {
            const labelsWithData = rutasData.top_paradas.labels.map((l, i) => `${l.substring(0,15)}: ${rutasData.top_paradas.data[i]}`);
            sumChartTopRutasInstance = new Chart(document.getElementById('sumChartTopRutas'), {
                type: 'bar',
                data: { labels: labelsWithData, datasets: [{ label:'Paradas', data: rutasData.top_paradas.data, backgroundColor: GW_COLORS.green, borderRadius:4 }] },
                options: barOptionsH
            });
        }

        // Bandas Horarias (Horarios)
        if (horData.franjas && horData.franjas.data.some(v => v > 0)) {
            const labelsWithData = horData.franjas.labels.map((l, i) => `${l}: ${horData.franjas.data[i]}`);
            sumChartBandasInstance = new Chart(document.getElementById('sumChartBandas'), {
                type: 'pie',
                data: { labels: labelsWithData, datasets: [{ data: horData.franjas.data, backgroundColor: [GW_COLORS.blue, GW_COLORS.green, GW_COLORS.orange, GW_COLORS.purple], borderColor: '#fff', borderWidth: 2 }] },
                options: { ...baseOptions, cutout: 0 }
            });
        }

        // Tipos de día (Horarios)
        if (horData.tipo_dia && horData.tipo_dia.data.some(v => v > 0)) {
            const labelsWithData = horData.tipo_dia.labels.map((l, i) => `${l}: ${horData.tipo_dia.data[i]}`);
            sumChartTipoDiaInstance = new Chart(document.getElementById('sumChartTipoDia'), {
                type: 'bar',
                data: { labels: labelsWithData, datasets: [{ label:'Horarios', data: horData.tipo_dia.data, backgroundColor: GW_COLORS.blue, borderRadius:4 }] },
                options: barOptionsV
            });
        }

        // Carga de conductores (Asignaciones)
        if (asigData.top_conductores && asigData.top_conductores.data.some(v => v > 0)) {
            const labelsWithData = asigData.top_conductores.labels.map((l, i) => `${l}: ${asigData.top_conductores.data[i]}`);
            sumChartConductoresInstance = new Chart(document.getElementById('sumChartConductores'), {
                type: 'bar',
                data: { labels: labelsWithData, datasets: [{ label:'Asignaciones', data: asigData.top_conductores.data, backgroundColor: GW_COLORS.blue, borderRadius:4 }] },
                options: barOptionsH
            });
        }
    }

    function downloadSummaryPDF() {
        const generadoText = document.getElementById('summaryGenerado').textContent;
        const imgFlota = sumChartFlotaInstance ? sumChartFlotaInstance.toBase64Image() : '';
        const imgAsig = sumChartAsigInstance ? sumChartAsigInstance.toBase64Image() : '';
        const imgEstRutas = sumChartEstRutasInstance ? sumChartEstRutasInstance.toBase64Image() : '';
        const imgTopRutas = sumChartTopRutasInstance ? sumChartTopRutasInstance.toBase64Image() : '';
        const imgBandas = sumChartBandasInstance ? sumChartBandasInstance.toBase64Image() : '';
        const imgTipoDia = sumChartTipoDiaInstance ? sumChartTipoDiaInstance.toBase64Image() : '';
        const imgConductores = sumChartConductoresInstance ? sumChartConductoresInstance.toBase64Image() : '';

        const bodyClone = document.createElement('div');
        bodyClone.innerHTML = document.getElementById('summaryBody').innerHTML;

        const replaceCanvas = (id, img) => {
            if (img) {
                const canvas = bodyClone.querySelector('#' + id);
                if(canvas) canvas.outerHTML = `<img src="${img}" style="width:100%; max-width:220px; height:auto; margin:0 auto; display:block;">`;
            }
        };

        replaceCanvas('sumChartFlota', imgFlota);
        replaceCanvas('sumChartAsig', imgAsig);
        replaceCanvas('sumChartEstRutas', imgEstRutas);
        replaceCanvas('sumChartTopRutas', imgTopRutas);
        replaceCanvas('sumChartBandas', imgBandas);
        replaceCanvas('sumChartTipoDia', imgTipoDia);
        replaceCanvas('sumChartConductores', imgConductores);

        const bodyHTML = bodyClone.innerHTML;

        const printCSS = `
            body { font-family: 'Inter', Arial, sans-serif; margin: 0; padding: 0; background:#fff; color:#333; }
            .print-header { background: linear-gradient(135deg, #1e3a8a, #3b82f6); color:#fff; padding:28px 36px 22px; display:flex; align-items:center; gap:16px; print-color-adjust:exact; -webkit-print-color-adjust:exact; }
            .print-header img { width:44px; height:44px; object-fit:contain; }
            .print-header h2 { margin:0 0 4px; font-size:1.35rem; }
            .print-header p { margin:3px 0; font-size:12px; opacity:.85; }
            .print-body { padding:28px 36px; }
            .summary-kpis { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:28px; }
            .kpi-card { border-radius:12px; padding:18px 14px; text-align:center; }
            .kpi-card .kpi-val { font-size:2rem; font-weight:800; display:block; line-height:1; margin-bottom:6px; }
            .kpi-card .kpi-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; opacity:.75; }
            .kpi-total{background:#eff6ff;color:#1e40af;}.kpi-pending{background:#fffbeb;color:#b45309;}
            .kpi-process{background:#f0f9ff;color:#0369a1;}.kpi-resolved{background:#f0fdf4;color:#166534;}
            .summary-section { margin-bottom:24px; }
            .summary-section h4 { font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#64748b; margin:0 0 12px; padding-bottom:8px; border-bottom:2px solid #e2e8f0; }
            .gravedad-row { display:flex; align-items:center; gap:12px; margin-bottom:10px; }
            .gravedad-label { width:68px; font-size:13px; font-weight:600; text-transform:capitalize; }
            .gravedad-bar-wrap { flex:1; background:#f1f5f9; border-radius:99px; height:10px; overflow:hidden; }
            .gravedad-bar { height:10px; border-radius:99px; }
            .bar-baja{background:#22c55e;}.bar-media{background:#f59e0b;}.bar-alta{background:#ef4444;}.bar-critica{background:#7c3aed;}
            .gravedad-count { font-size:13px; font-weight:700; color:#334155; min-width:24px; text-align:right; }
            .summary-table { width:100%; border-collapse:collapse; font-size:13px; }
            .summary-table th { text-align:left; padding:8px 12px; background:#f8fafc; color:#64748b; font-weight:700; font-size:11px; text-transform:uppercase; }
            .summary-table td { padding:9px 12px; border-top:1px solid #f1f5f9; color:#334155; }
            .badge-rank { display:inline-block; background:#e0e7ff; color:#4338ca; border-radius:99px; padding:2px 8px; font-size:11px; font-weight:700; }
            .print-footer { border-top:1px solid #e2e8f0; padding:14px 36px; background:#f8fafc; font-size:12px; color:#94a3b8; }
            @media print { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        `;

        const win = window.open('', '_blank', 'width=900,height=700');
        win.document.write(`<!DOCTYPE html><html lang="es"><head>
            <meta charset="UTF-8">
            <title>Resumen ejecutivo de reportes - GoWay</title>
            <style>${printCSS}</style>
        </head><body>
            <div class="print-header">
                <img src="${window.location.origin}/GoWay/assets/images/logo_new.png" alt="GoWay">
                <div>
                    <h2>Resumen ejecutivo de reportes</h2>
                    <p>${generadoText}</p>
                </div>
            </div>
            <div class="print-body">${bodyHTML}</div>
            <div class="print-footer">GoWay - Sistema de Transporte Público</div>
            
            <script>
                // Pequeño script para esperar a que las imágenes carguen antes de imprimir
                window.onload = function() {
                    setTimeout(function() {
                        window.focus();
                        window.print();
                    }, 500);
                };
            <\/script>
        </body></html>`);
        win.document.close();
    }
</script>
