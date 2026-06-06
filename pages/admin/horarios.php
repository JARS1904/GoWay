<!--Se agreo para el manejo de sesión-->
<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../../config/conexion_bd.php';
require_once '../../config/sync_session_foto.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios - Transporte Público</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" href="../../assets/images/logo_new.png" type="image/png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php
        $page_title  = 'Gestión de Horarios';
        $active_page = 'horarios';
        $base_url    = '../../';
        require_once __DIR__ . '/../../components/sidebar.php';
        ?>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Gestión de Horarios</h2>
                                <div class="header-notif-wrap">
                    <button class="notification-bell" id="desktopNotifBtn" onclick="toggleNotifications()">
                        <span class="material-icons">notifications_none</span>
                    </button>
                </div>
            </header>

            <section class="content">
    <!-- KPI Dashboard Section -->
    <div class="kpi-section-title" style="margin-top:0;">
        <h2>Indicadores Operativos</h2>
        <span class="kpi-section-badge">Horarios y Frecuencias</span>
    </div>
    
    <div class="stats-grid" id="horariosStatsGrid" style="display:none; margin-bottom:20px;"></div>

    <div class="charts-grid" id="horariosChartsGrid" style="display:none; grid-template-columns: 1fr 1fr;">
        <div class="chart-card">
            <div class="chart-card-header">
                <div class="chart-card-title"><h4>Bandas horarias</h4><span>Distribución por franja del día</span></div>
                <div class="chart-card-icon purple"><span class="material-icons">schedule</span></div>
            </div>
            <canvas id="chartFranjas" height="160"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-card-header">
                <div class="chart-card-title"><h4>Tipos de día</h4><span>Configuraciones activas</span></div>
                <div class="chart-card-icon blue"><span class="material-icons">event</span></div>
            </div>
            <canvas id="chartTipoDia" height="160"></canvas>
        </div>
    </div>
                <div class="section-header">
                    <h3>Horarios Disponibles</h3>
                    <button class="btn-add">+ Agregar nuevo horario</button>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Ruta</th>
                            <th>Jornada (Día)</th>
                            <th>Hora salida</th>
                            <th>Hora llegada</th>
                            <th>Frecuencia</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $conn = $conexion;
                        $sql  = "SELECT h.*, r.nombre AS nombre_ruta FROM horarios h LEFT JOIN rutas r ON h.id_ruta = r.id_ruta";
                        if ($_SESSION['rol'] == 4) {
                            $rfc_empresa_session = $_SESSION['rfc_empresa'];
                            $sql .= " WHERE r.rfc_empresa = '$rfc_empresa_session'";
                        }
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $ruta      = htmlspecialchars($row['nombre_ruta'] ?? 'Sin ruta');
                                $dia       = htmlspecialchars($row['tipo_dia']);
                                $salida    = htmlspecialchars($row['hora_salida']);
                                $llegada   = htmlspecialchars($row['hora_llegada']);
                                $frecuencia = htmlspecialchars($row['frecuencia'] ?? '—');
                                echo "
                                <tr data-id=\"{$row['id_horario']}\"
                                    data-id-ruta=\"{$row['id_ruta']}\"
                                    data-dia=\"{$dia}\"
                                    data-salida=\"{$salida}\"
                                    data-llegada=\"{$llegada}\"
                                    data-frecuencia=\"{$frecuencia}\">
                                    <td data-label=\"Ruta\">{$ruta}</td>
                                    <td data-label=\"Día\">{$dia}</td>
                                    <td data-label=\"Hora salida\">{$salida}</td>
                                    <td data-label=\"Hora llegada\">{$llegada}</td>
                                    <td data-label=\"Frecuencia\">{$frecuencia}</td>
                                    <td>
                                        <div class=\"kebab-menu\">
                                            <button class=\"kebab-btn\" onclick=\"toggleKebabMenu(this, event)\">
                                                <span class=\"material-icons\">more_vert</span>
                                            </button>
                                            <div class=\"dropdown-content\">
                                                <button class=\"dropdown-item btn-edit\" data-id=\"{$row['id_horario']}\">
                                                    <span class=\"material-icons\">edit</span> Editar
                                                </button>
                                                <button class=\"dropdown-item btn-delete\" data-id=\"{$row['id_horario']}\">
                                                    <span class=\"material-icons\">delete</span> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo '<tr><td colspan="6">No hay horarios registrados</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Paginación -->
                <div class="pagination">
                    <button class="pagination-btn" id="prevPage" disabled>‹ Anterior</button>
                    <div class="pagination-info" id="pageInfo">Página 1 de 1</div>
                    <button class="pagination-btn" id="nextPage">Siguiente ›</button>
                </div>
            </section>
        </main>
    </div>

        <!-- Modal para agregar nuevo horarios -->
<div class="modal-overlay" id="addRouteModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Agregar nuevo horarios</h3>
            <button class="modal-close" id="closeModal">&times;</button>
        </div>
        <form id="routeForm" action="../../controllers/insert/insert_horarios.php" method="POST">
            <div class="modal-body">
                <!-- Columna izquierda -->
                <div>
                    <div class="modal-form-group">
                        <label >Ruta</label>
                        <select name="id_ruta" id="">
                            
                            <?php
                            $conn = $conexion;
                            $where_emp = ($_SESSION['rol'] == 4) ? " WHERE rfc_empresa = '".$_SESSION['rfc_empresa']."'" : "";
                            $result = $conn->query("SELECT id_ruta, nombre FROM rutas" . $where_emp);
                            while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id_ruta']}'>{$row['nombre']}</option>";
                            }
                            ?>

                        </select>

                    </div>
                    <div class="modal-form-group">
                        <label >Tipo de Día (Jornada)</label>
                        <select name="tipo_dia" required>
                            <option value="Lunes a Viernes">Lunes a Viernes</option>
                            <option value="Sábado">Sábado</option>
                            <option value="Domingo">Domingo</option>
                            <option value="Festivo">Festivo</option>
                        </select>
                    </div>
                    <div class="modal-form-group">
                        <label >Hora de salida</label>
                        <input type="time" id="" name="hora_salida" placeholder="" required>
                    </div>
                </div>
                
                <!-- Columna derecha -->
                <div>
                    <div class="modal-form-group">
                        <label >Hora de llegada</label>
                        <input type="time" id="" name="hora_llegada" placeholder="" required>
                    </div>
                    <div class="modal-form-group">
                        <label >Frecuencia</label>
                        <input type="text" id="" name="frecuencia" placeholder="Ej.Cada 15 minutos"></input>
                    </div>
                    
                </div>
            </div>


            <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-cancel" id="cancelModal">Cancelar</button>
                <button type="submit" class="modal-btn modal-btn-save" >Guardar</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal para editar horario -->
<div class="modal-overlay" id="editRouteModal">
  <div class="modal-container">
    <div class="modal-header">
      <h3>Editar horario</h3>
      <button class="modal-close" id="closeEditModal">×</button>
    </div>
    <form id="editRouteForm" action="../../controllers/update/update_horario.php" method="POST">
      <input type="hidden" name="id_horario" id="edit_id_horario">
      <div class="modal-body">
        <div>
          <div class="modal-form-group">
            <label>Ruta</label>
            <select name="id_ruta" id="edit_id_ruta">
              <?php
              $conn = $conexion;
              $where_emp = ($_SESSION['rol'] == 4) ? " WHERE rfc_empresa = '".$_SESSION['rfc_empresa']."'" : "";
              $result = $conn->query("SELECT id_ruta, nombre FROM rutas" . $where_emp);
              while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id_ruta']}'>{$row['nombre']}</option>";
              }
              ?>
            </select>
          </div>
          <div class="modal-form-group">
            <label>Tipo de Día (Jornada)</label>
            <select name="tipo_dia" id="edit_tipo_dia" required>
                <option value="Lunes a Viernes">Lunes a Viernes</option>
                <option value="Sábado">Sábado</option>
                <option value="Domingo">Domingo</option>
                <option value="Festivo">Festivo</option>
            </select>
          </div>
          <div class="modal-form-group">
            <label>Hora de salida</label>
            <input type="time" name="hora_salida" id="edit_hora_salida" required>
          </div>
        </div>
        <div>
          <div class="modal-form-group">
            <label>Hora de llegada</label>
            <input type="time" name="hora_llegada" id="edit_hora_llegada" required>
          </div>
          <div class="modal-form-group">
            <label>Frecuencia</label>
            <input type="text" name="frecuencia" id="edit_frecuencia">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="modal-btn modal-btn-cancel" id="cancelEditModal">Cancelar</button>
        <button type="submit" class="modal-btn modal-btn-save">Actualizar</button>
      </div>
    </form>
  </div>
</div>




<script src="../../assets/js/notifications.js"></script>
<script src="../../assets/js/main.js"></script>
<script src="../../assets/js/pagination.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inserción
    handleInsertForm(
        document.getElementById('routeForm'),
        'Horario agregado exitosamente',
        function(data) {
            if (data.nuevoRegistro) {
                const tbody = document.querySelector('.data-table tbody');
                const noData = tbody.querySelector('td[colspan]');
                if (noData) {
                    noData.parentElement.remove();
                }
                
                const reg = data.nuevoRegistro;
                const tr = document.createElement('tr');
                tr.setAttribute('data-id', reg.id_horario);
                tr.setAttribute('data-id-ruta', reg.id_ruta);
                tr.setAttribute('data-dia', reg.tipo_dia);
                tr.setAttribute('data-salida', reg.hora_salida);
                tr.setAttribute('data-llegada', reg.hora_llegada);
                tr.setAttribute('data-frecuencia', reg.frecuencia || '—');
                
                tr.innerHTML = `
                    <td data-label="Ruta">${reg.ruta}</td>
                    <td data-label="Día">${reg.tipo_dia}</td>
                    <td data-label="Hora salida">${reg.hora_salida}</td>
                    <td data-label="Hora llegada">${reg.hora_llegada}</td>
                    <td data-label="Frecuencia">${reg.frecuencia || '—'}</td>
                    <td>
                        <div class="kebab-menu">
                            <button class="kebab-btn" onclick="toggleKebabMenu(this, event)">
                                <span class="material-icons">more_vert</span>
                            </button>
                            <div class="dropdown-content">
                                <button class="dropdown-item btn-edit" data-id="${reg.id_horario}">
                                    <span class="material-icons">edit</span> Editar
                                </button>
                                <button class="dropdown-item btn-delete" data-id="${reg.id_horario}">
                                    <span class="material-icons">delete</span> Eliminar
                                </button>
                            </div>
                        </div>
                    </td>
                `;
                
                tr.style.transition = 'opacity 0.5s';
                tr.style.opacity = '0';
                
                Array.from(tr.children).forEach(td => {
                    td.style.transition = 'background-color 0.5s';
                    td.style.backgroundColor = '#dbeafe'; // Azul
                });
                
                tbody.prepend(tr);
                
                setTimeout(() => { tr.style.opacity = '1'; }, 10);
                setTimeout(() => {
                    Array.from(tr.children).forEach(td => {
                        td.style.backgroundColor = '';
                    });
                }, 1000);

                if (window.paginationInstance) {
                    window.paginationInstance.allRows.unshift(tr);
                    window.paginationInstance.filterRows(document.getElementById('searchInput')?.value || '');
                }
                
                // Re-bind events to new buttons
                const btnEdit = tr.querySelector('.btn-edit');
                if (btnEdit) {
                    btnEdit.addEventListener('click', function() {
                        const row = this.closest('tr');
                        document.getElementById('edit_id_horario').value  = row.dataset.id;
                        document.getElementById('edit_id_ruta').value     = row.dataset.idRuta;
                        document.getElementById('edit_tipo_dia').value  = row.dataset.dia;
                        document.getElementById('edit_hora_salida').value = row.dataset.salida;
                        document.getElementById('edit_hora_llegada').value = row.dataset.llegada;
                        document.getElementById('edit_frecuencia').value  = row.dataset.frecuencia;
                        document.getElementById('editRouteModal').classList.add('active');
                    });
                }
                
                const btnDelete = tr.querySelector('.btn-delete');
                if (btnDelete) {
                    handleDeleteButton(
                        btnDelete,
                        '../../controllers/delete/delete_horarios.php',
                        'id_horario',
                        '¿Estás seguro de que deseas eliminar este horario?',
                        function(data, button) {
                            const rowToRemove = button.closest('tr');
                            if (rowToRemove) {
                                rowToRemove.style.transition = 'opacity 0.5s';
                                rowToRemove.style.opacity = '0';
                                Array.from(rowToRemove.children).forEach(td => {
                                    td.style.transition = 'background-color 0.5s';
                                    td.style.backgroundColor = '#fee2e2';
                                });
                                setTimeout(() => {
                                    rowToRemove.remove();
                                    if (window.paginationInstance) {
                                        window.paginationInstance.allRows = window.paginationInstance.allRows.filter(r => r !== rowToRemove);
                                        window.paginationInstance.filterRows(document.getElementById('searchInput')?.value || '');
                                    } else {
                                        const tb = document.querySelector('.data-table tbody');
                                        if (tb && tb.children.length === 0) {
                                            tb.innerHTML = '<tr><td colspan="6">No hay horarios registrados</td></tr>';
                                        }
                                    }
                                }, 500);
                            }
                        }
                    );
                }
                
                document.getElementById('addRouteModal').classList.remove('active');
            }
        }
    );

    // Actualización
    handleUpdateForm(
        document.getElementById('editRouteForm'),
        'Horario actualizado exitosamente',
        function(data) {
            if (data.registroActualizado) {
                const reg = data.registroActualizado;
                const tr = document.querySelector(`tr td[data-id="${reg.id_horario}"]`)?.closest('tr') || document.querySelector(`tr[data-id="${reg.id_horario}"]`);
                
                if (tr) {
                    const cells = tr.querySelectorAll('td');
                    cells[0].textContent = reg.ruta;
                    cells[1].textContent = reg.tipo_dia;
                    cells[2].textContent = reg.hora_salida;
                    cells[3].textContent = reg.hora_llegada;
                    cells[4].textContent = reg.frecuencia || '—';
                    
                    // Update dataset
                    tr.setAttribute('data-id-ruta', reg.id_ruta);
                    tr.setAttribute('data-dia', reg.tipo_dia);
                    tr.setAttribute('data-salida', reg.hora_salida);
                    tr.setAttribute('data-llegada', reg.hora_llegada);
                    tr.setAttribute('data-frecuencia', reg.frecuencia || '—');
                    
                    Array.from(tr.children).forEach(td => {
                        td.style.transition = 'background-color 0.5s';
                        td.style.backgroundColor = '#dcfce7'; // Verde
                    });
                    
                    setTimeout(() => {
                        Array.from(tr.children).forEach(td => {
                            td.style.backgroundColor = '';
                        });
                    }, 1000);
                    
                    document.getElementById('editRouteModal').classList.remove('active');
                }
            }
        }
    );

    // Eliminación
    initializeDeleteButtons(
        '.btn-delete',
        '../../controllers/delete/delete_horarios.php',
        'id_horario',
        '¿Estás seguro de que deseas eliminar este horario?',
        function(data, button) {
            const tr = button.closest('tr');
            if (tr) {
                tr.style.transition = 'opacity 0.5s';
                tr.style.opacity = '0';
                
                Array.from(tr.children).forEach(td => {
                    td.style.transition = 'background-color 0.5s';
                    td.style.backgroundColor = '#fee2e2'; // Rojo
                });
                
                setTimeout(() => {
                    tr.remove();
                    if (window.paginationInstance) {
                        window.paginationInstance.allRows = window.paginationInstance.allRows.filter(r => r !== tr);
                        window.paginationInstance.filterRows(document.getElementById('searchInput')?.value || '');
                    } else {
                        const tbody = document.querySelector('.data-table tbody');
                        if (tbody && tbody.children.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="6">No hay horarios registrados</td></tr>';
                        }
                    }
                }, 500);
            }
        }
    );

    // Editar: leer datos del data-* del tr
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            document.getElementById('edit_id_horario').value  = row.dataset.id;
            document.getElementById('edit_id_ruta').value     = row.dataset.idRuta;
            document.getElementById('edit_tipo_dia').value  = row.dataset.dia;
            document.getElementById('edit_hora_salida').value = row.dataset.salida;
            document.getElementById('edit_hora_llegada').value = row.dataset.llegada;
            document.getElementById('edit_frecuencia').value  = row.dataset.frecuencia;
            document.getElementById('editRouteModal').classList.add('active');
        });
    });

    document.getElementById('closeEditModal')?.addEventListener('click', () =>
        document.getElementById('editRouteModal').classList.remove('active'));
    document.getElementById('cancelEditModal')?.addEventListener('click', () =>
        document.getElementById('editRouteModal').classList.remove('active'));
    document.getElementById('editRouteModal')?.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active');
    });
});
</script>
    <?php require_once __DIR__ . '/../../components/notifications_panel.php'; ?>
    <?php require_once __DIR__ . '/../../components/logout_modal.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const GW = { blue:'#0660fe', green:'#10b981', orange:'#f59e0b', purple:'#8b5cf6', red:'#ef4444' };
    fetch('../../api/kpis_api.php?seccion=horarios').then(r=>r.json()).then(data => {
        if(!data.success) return;
        document.getElementById('horariosStatsGrid').style.display = 'grid';
        document.getElementById('horariosStatsGrid').innerHTML = `
            <div class="stat-card"><div class="stat-card-icon"><span class="material-icons" style="color:var(--primary-color);">schedule</span></div><div class="stat-card-content"><h3>Servicios Diarios</h3><p class="stat-number">${data.kpi.total}</p><span class="stat-label">Registrados</span></div></div>
            <div class="stat-card"><div class="stat-card-icon"><span class="material-icons" style="color:#10b981;">route</span></div><div class="stat-card-content"><h3>Rutas Cubiertas</h3><p class="stat-number">${data.kpi.rutas_cubiertas}</p><span class="stat-label">Con horario asignado</span></div></div>
            <div class="stat-card"><div class="stat-card-icon"><span class="material-icons" style="color:#8b5cf6;">av_timer</span></div><div class="stat-card-content"><h3>Con Frecuencia</h3><p class="stat-number">${data.kpi.con_frecuencia}</p><span class="stat-label">Definida</span></div></div>
        `;
        document.getElementById('horariosChartsGrid').style.display = 'grid';
        if(data.franjas && data.franjas.data.some(v=>v>0)) {
            new Chart(document.getElementById('chartFranjas'), {
                type: 'pie', data: { labels: data.franjas.labels, datasets: [{ data: data.franjas.data, backgroundColor: [GW.blue, GW.green, GW.orange, GW.purple] }] }, options: {plugins: {legend: {position: 'bottom'}}}
            });
        }
        if(data.tipo_dia && data.tipo_dia.data.length > 0) {
            new Chart(document.getElementById('chartTipoDia'), {
                type: 'bar', data: { labels: data.tipo_dia.labels, datasets: [{ label:'Horarios', data: data.tipo_dia.data, backgroundColor: GW.blue, borderRadius:4 }] }, options: { plugins:{legend:{display:false}} }
            });
        }
    });
});
</script>
</body>
</html>
