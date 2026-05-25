<!-- Panel de Reportes (convertido a lateral) -->
<div class="modal-overlay" id="reportModalOverlay" onclick="closeReportModal()">
    <div class="modal-container" onclick="event.stopPropagation()">
        <div class="modal-header" style="padding: 20px 24px; border-bottom: 1px solid #eef2f6; background: #fff; margin-bottom: 0;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="background:#e8efff; color:#2962FF; width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <h3 style="margin:0; font-size:1.25rem;">Nuevo reporte</h3>
            </div>
            <button class="modal-close" onclick="closeReportModal()">&times;</button>
        </div>
        
        <div class="modal-body" style="padding: 24px; overflow-y: auto; flex: 1;">
            <!-- Busqueda de Vehiculo -->
            <div style="display:flex; gap:10px; align-items:flex-end; margin-bottom:20px;">
                <div class="modal-form-group" style="flex:1; margin-bottom:0;">
                    <label>Placa del vehículo</label>
                    <div style="position:relative; width: 100%;">
                        <i class="fas fa-car" style="position:absolute; left:12px; top:15px; color:#757575;"></i>
                        <input type="text" id="reportSearchPlaca" placeholder="Placa del vehículo" style="width: 100%; padding-left:36px;" oninput="this.value = this.value.toUpperCase()" onkeypress="if(event.key === 'Enter') searchReportAssignment()">
                    </div>
                </div>
                <button type="button" id="btnSearchReport" class="btn" style="width:auto; flex-shrink:0; border-radius:18px; background:#2962FF; padding:12px 24px; font-weight:500; font-size:16px;" onclick="searchReportAssignment()">Buscar</button>
            </div>

            <!-- Tarjeta Verde de Asignacion Encontrada -->
            <div id="reportAssignmentCard" style="display:none; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:12px 16px; margin-bottom:20px;">
                <div style="color:#16a34a; font-weight:600; font-size:13px; margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-check-circle"></i> Asignación encontrada
                </div>
                <div style="color:#333; font-size:13px; line-height:1.8; display:flex; flex-direction:column; gap:4px;">
                    <div style="display:flex; align-items:center; gap:10px;"><i class="fas fa-car" style="color:#64748b; width:16px; text-align:center;"></i> <span id="reportCardVehiculo"></span></div>
                    <div style="display:flex; align-items:center; gap:10px;"><i class="fas fa-user" style="color:#64748b; width:16px; text-align:center;"></i> <span id="reportCardConductor"></span></div>
                    <div style="display:flex; align-items:flex-start; gap:10px;">
                        <i class="fas fa-route" style="color:#64748b; width:16px; text-align:center; margin-top:4px;"></i>
                        <span id="reportCardRuta" style="flex:1; line-height:1.5;"></span>
                    </div>
                </div>
                <input type="hidden" id="reportIdAsignacion" value="">
            </div>

            <!-- Toggle Es trayecto de regreso (solo visible al encontrar asignación) -->
            <div id="reportRetornoSection" style="display:none; justify-content:space-between; align-items:center; margin-bottom:20px; font-weight:600; font-size:14px; color:#333;">
                <span>Es trayecto de regreso</span>
                <label class="switch-ios">
                    <input type="checkbox" id="reportEsRetorno">
                    <span class="slider-ios"></span>
                </label>
            </div>

            <!-- Tipo de incidente -->
            <div class="modal-form-group">
                <label>Tipo de incidente</label>
                <div style="position:relative;">
                    <i class="fas fa-exclamation-triangle" style="position:absolute; left:12px; top:15px; color:#757575;"></i>
                    <select id="reportTipoIncidente" style="padding-left:36px;" required>
                        <option value="" disabled selected>Tipo de incidente</option>
                    </select>
                </div>
            </div>

            <!-- Gravedad -->
            <div class="modal-form-group">
                <label>Gravedad</label>
                <div style="position:relative;">
                    <i class="fas fa-chart-bar" style="position:absolute; left:12px; top:15px; color:#757575;"></i>
                    <select id="reportGravedad" style="padding-left:36px;" required>
                        <option value="" disabled selected>Gravedad</option>
                    </select>
                </div>
            </div>

            <!-- Fecha y Hora -->
            <div class="modal-form-group">
                <label>Fecha y hora del incidente</label>
                <div style="position:relative;">
                    <i class="far fa-calendar-alt" style="position:absolute; left:12px; top:15px; color:#757575;"></i>
                    <input type="datetime-local" id="reportFechaHora" placeholder="Fecha y hora del incidente" style="padding-left:36px; font-family:inherit;" required>
                </div>
            </div>

            <!-- Descripción -->
            <div class="modal-form-group" style="margin-bottom:10px;">
                <label>Descripción del incidente</label>
                <div style="position:relative;">
                    <i class="fas fa-align-left" style="position:absolute; left:12px; top:14px; color:#757575;"></i>
                    <textarea id="reportDescripcion" rows="4" style="padding-left:36px; padding-top:12px; font-family:inherit;" placeholder="Descripción del incidente"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="padding: 16px 24px; background: #fff; border-top: 1px solid #eef2f6; display: flex; justify-content: space-between; align-items: center; border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
            <button class="btn" style="background:transparent; color:#64748b; font-weight:600; font-size:14px; border:none; box-shadow:none; padding:0 10px;" onclick="closeReportModal()">Cancelar</button>
            <button class="btn" style="border-radius:18px; padding:12px 32px; background:#2962FF; font-weight:500; font-size:16px;" onclick="submitReport()">Enviar reporte</button>
        </div>
    </div>
</div>

<script>
    // ── Modal Reportes JS ────────────────────────────────
    function openReportModal() {
        const userDropdown = document.querySelector('.user-dropdown');
        if (userDropdown) userDropdown.classList.remove('open');
        
        document.getElementById('reportModalOverlay').classList.add('active');
        
        document.getElementById('reportFechaHora').value = '';
        
        document.getElementById('reportSearchPlaca').value = '';
        document.getElementById('reportAssignmentCard').style.display = 'none';
        document.getElementById('reportIdAsignacion').value = '';
        document.getElementById('reportDescripcion').value = '';
        document.getElementById('reportEsRetorno').checked = false;
        document.getElementById('reportRetornoSection').style.display = 'none';
        window.currentAsignacionData = null;
        
        const tipoSelect = document.getElementById('reportTipoIncidente');
        if (tipoSelect.options.length <= 1) {
            fetch('../../api/reportes_api.php?action=get_options')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        tipoSelect.innerHTML = '<option value="" disabled selected>Tipo de incidente</option>';
                        data.tipos_incidencia.forEach(t => {
                            tipoSelect.add(new Option(t.nombre, t.id));
                        });
                        const gravedadesSelect = document.getElementById('reportGravedad');
                        gravedadesSelect.innerHTML = '<option value="" disabled selected>Gravedad</option>';
                        data.niveles_gravedad.forEach(g => {
                            gravedadesSelect.add(new Option(g.nombre, g.id));
                        });
                    }
                }).catch(e => console.error("Error loading options", e));
        }
    }

    function closeReportModal() {
        document.getElementById('reportModalOverlay').classList.remove('active');
    }

    function searchReportAssignment() {
        const placa = document.getElementById('reportSearchPlaca').value.trim();
        if (!placa) {
            if (typeof showToast === 'function') showToast('Ingresa una placa para buscar');
            return;
        }
        
        const btn = document.getElementById('btnSearchReport');
        const originalText = btn.innerText;
        btn.innerText = '...';
        
        fetch(`../../api/reportes_api.php?action=get_assignment_data&placa=${encodeURIComponent(placa)}`)
            .then(res => res.json())
            .then(data => {
                btn.innerText = originalText;
                if (data.success && data.data) {
                    const asig = data.data;
                    window.currentAsignacionData = asig;
                    
                    document.getElementById('reportCardVehiculo').textContent = `${asig.vehiculo_placa} - ${asig.vehiculo_modelo}`;
                    document.getElementById('reportCardConductor').textContent = asig.conductor_nombre;
                    
                    const isRetorno = document.getElementById('reportEsRetorno').checked;
                    if (isRetorno) {
                       const texto = asig.ruta_retorno_nombre
                            ? asig.ruta_retorno_nombre
                            : asig.ruta_nombre;
                        document.getElementById('reportCardRuta').textContent = texto;
                    } else {
                        document.getElementById('reportCardRuta').textContent = asig.ruta_nombre;
                    }
                    
                    document.getElementById('reportRetornoSection').style.display = 'flex';
                    
                    document.getElementById('reportIdAsignacion').value = asig.id_asignacion;
                    
                    document.getElementById('reportAssignmentCard').style.display = 'block';
                    if (typeof showToast === 'function') showToast('Asignación encontrada');
                } else {
                    document.getElementById('reportAssignmentCard').style.display = 'none';
                    document.getElementById('reportIdAsignacion').value = '';
                    if (typeof showToast === 'function') showToast(data.error || 'No se encontró asignación');
                }
            })
            .catch(err => {
                btn.innerText = originalText;
                if (typeof showToast === 'function') showToast('Error al buscar asignación');
            });
    }

    function submitReport() {
        const idAsignacion = document.getElementById('reportIdAsignacion').value;
        if (!idAsignacion) {
            if (typeof showToast === 'function') showToast('Debes buscar y seleccionar un vehículo primero');
            return;
        }
        
        const payload = {
            id_asignacion: idAsignacion,
            id_usuario: <?php echo $_SESSION['id'] ?? 0; ?>,
            es_retorno: document.getElementById('reportEsRetorno').checked,
            tipo_incidente: document.getElementById('reportTipoIncidente').value,
            gravedad: document.getElementById('reportGravedad').value,
            fecha_hora: document.getElementById('reportFechaHora').value.replace('T', ' ') + ':00',
            descripcion: document.getElementById('reportDescripcion').value.trim()
        };
        
        if (!payload.descripcion || !payload.fecha_hora) {
            if (typeof showToast === 'function') showToast('Completa todos los campos');
            return;
        }
        
        fetch('../../api/reportes_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success || data.id_reporte) {
                if (typeof showToast === 'function') showToast('Reporte enviado con éxito');
                closeReportModal();
            } else {
                if (typeof showToast === 'function') showToast(data.error || data.message || 'Error al enviar reporte');
            }
        })
        .catch(err => {
            if (typeof showToast === 'function') showToast('Error de conexión');
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const toggleRetorno = document.getElementById('reportEsRetorno');
        if (toggleRetorno) {
            toggleRetorno.addEventListener('change', function() {
                if (!window.currentAsignacionData) {
                    if (this.checked) {
                        if (typeof showToast === 'function') showToast('Busca un vehículo primero');
                        this.checked = false;
                    }
                    return;
                }
                const asig = window.currentAsignacionData;
                if (this.checked) {
                   const texto = asig.ruta_retorno_nombre
                    ? asig.ruta_retorno_nombre
                    : asig.ruta_nombre;
                document.getElementById('reportCardRuta').textContent = texto;
                } else {
                    document.getElementById('reportCardRuta').textContent = asig.ruta_nombre;
                }
            });
        }
    });
</script>
