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
    <title>Rutas - Transporte Público</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../../assets/images/logo_new.png" type="image/png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php
        $page_title  = 'Gestión de Rutas';
        $active_page = 'rutas';
        $base_url    = '../../';
        require_once __DIR__ . '/../../components/sidebar.php';
        ?>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Gestión de Rutas</h2>
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
        <span class="kpi-section-badge">Gestión de Rutas</span>
    </div>
    
    <div class="stats-grid" id="rutasStatsGrid" style="display:none; margin-bottom:20px;">
        <!-- Filled via JS -->
    </div>

    <div class="charts-grid" id="rutasChartsGrid" style="display:none; grid-template-columns: 1fr 2fr;">
        <div class="chart-card">
            <div class="chart-card-header">
                <div class="chart-card-title"><h4>Estado de rutas</h4><span>Activas vs Inactivas</span></div>
                <div class="chart-card-icon blue"><span class="material-icons">route</span></div>
            </div>
            <canvas id="chartEstadoRutas" height="160"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-card-header">
                <div class="chart-card-title"><h4>Top rutas</h4><span>Con más paradas asignadas</span></div>
                <div class="chart-card-icon green"><span class="material-icons">place</span></div>
            </div>
            <canvas id="chartTopParadas" height="160"></canvas>
        </div>
    </div>
                <div class="section-header">
                    <h3>Lista de Rutas</h3>
                    <button class="btn-add">+ Agregar nueva ruta</button>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Paradas registradas</th>
                            <th>Ruta de retorno</th>
                            <th>Activa</th>
                            <th>RFC de la Empresa</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        // Conexión a la base de datos
                        $conn = $conexion;

                        // Consulta para obtener las rutas con su par de retorno y conteo de paradas
                        $sql = "SELECT r.*,
                                       ret.nombre AS nombre_retorno,
                                       (SELECT COUNT(*) FROM paradas_ruta pr WHERE pr.id_ruta = r.id_ruta) AS total_paradas
                                FROM rutas r
                                LEFT JOIN rutas ret ON r.id_ruta_retorno = ret.id_ruta";
                        if ($_SESSION['rol'] == 4) {
                            $rfc_empresa_session = $_SESSION['rfc_empresa'];
                            $sql .= " WHERE r.rfc_empresa = '$rfc_empresa_session'";
                        }
                        $sql .= " ORDER BY r.id_ruta";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $statusClass = $row["activa"] ? 'status-active' : 'status-inactive';
                                $statusText = $row["activa"] ? 'Sí' : 'No';

                                // Badge de retorno
                                if ($row['id_ruta_retorno']) {
                                    $nombreRetorno = htmlspecialchars($row['nombre_retorno']);
                                    // Cambiar formato: "A - B" a "A ⇄ B"
                                    if (strpos($nombreRetorno, ' - ') !== false) {
                                        $nombreRetornoFormatted = str_replace(' - ', ' ⇄ ', $nombreRetorno);
                                        $retornoBadge = '<span style="display:inline-block;background:#dbeafe;color:#1d4ed8;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:600;white-space:normal;line-height:1.2;">' . $nombreRetornoFormatted . '</span>';
                                    } else {
                                        // Si no tiene el guión, lo dejamos como ⇄ Nombre
                                        $retornoBadge = '<span style="display:inline-block;background:#dbeafe;color:#1d4ed8;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:600;white-space:normal;line-height:1.2;">⇄ ' . $nombreRetorno . '</span>';
                                    }
                                } else {
                                    $retornoBadge = '<span style="color:#94a3b8;font-size:12px;">— Sin par</span>';
                                }
                                
                                echo '<tr>
                                        <td data-label="Nombre" data-id="'.$row["id_ruta"].'">'.$row["nombre"].'</td>
                                        <td data-label="Origen">' . htmlspecialchars($row["origen"]) . '</td>
                                        <td data-label="Destino">' . htmlspecialchars($row["destino"]) . '</td>
                                        <td data-label="Paradas">' . ($row['total_paradas'] > 0
                                            ? '<a href="paradas_ruta.php" style="display:inline-flex;align-items:center;gap:5px;background:#dbeafe;color:#1d4ed8;border-radius:12px;padding:3px 11px;font-size:12px;font-weight:600;text-decoration:none;">' . $row['total_paradas'] . ' paradas</a>'
                                            : '<a href="paradas_ruta.php" style="display:inline-flex;align-items:center;gap:5px;background:#fee2e2;color:#b91c1c;border-radius:12px;padding:3px 11px;font-size:12px;font-weight:600;text-decoration:none;">Sin paradas</a>') . '</td>
                                        <td data-label="Ruta de retorno" data-id-retorno="' . ($row['id_ruta_retorno'] ? $row['id_ruta_retorno'] : '') . '">' . $retornoBadge . '</td>
                                        <td data-label="Activa"><span class="'.$statusClass.'">' . $statusText . '</span></td>
                                        <td data-label="RFC de la Empresa">' . $row["rfc_empresa"] . '</td>
                                        <td>
                                            <div class="kebab-menu">
                                                <button class="kebab-btn" onclick="toggleKebabMenu(this, event)">
                                                    <span class="material-icons">more_vert</span>
                                                </button>
                                                <div class="dropdown-content">
                                                    <button class="dropdown-item btn-edit">
                                                        <span class="material-icons">edit_square</span> Editar
                                                    </button>
                                                    <button class="dropdown-item btn-delete">
                                                        <span class="material-icons">delete_outline</span> Eliminar
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="8">No hay rutas registradas</td></tr>';
                        }

                        ?>
                    </tbody>
                </table>
                <!-- Paginación -->
                <div class="pagination">
                    <button class="pagination-btn" id="prevPage" disabled>‹ Anterior</button>
                    <div class="pagination-info" id="pageInfo">Página 1 de 5</div>
                    <button class="pagination-btn" id="nextPage">Siguiente ›</button>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal para agregar nueva ruta -->
    <div class="modal-overlay" id="addRouteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Agregar nueva ruta</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <form id="routeForm" action="../../controllers/insert/insertar_ruta.php" method="POST">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>RFC de Empresa</label>
                            <?php if ($_SESSION['rol'] == 1): ?>
                            <select id="" name="rfc_empresa" required>
                                <option value="" disabled selected>Seleccione Empresa</option>
                                <?php
                                $conn = $conexion;
                                $result = $conn->query("SELECT rfc_empresa, nombre FROM empresas");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['rfc_empresa']}'>{$row['nombre']}</option>";
                                }
                                ?>
                            </select>
                            <?php else: ?>
                            <input type="text" value="<?php echo htmlspecialchars($_SESSION['nombre']); ?>" readonly style="background-color: #f3f4f6; cursor: not-allowed;">
                            <input type="hidden" name="rfc_empresa" value="<?php echo $_SESSION['rfc_empresa']; ?>">
                            <?php endif; ?>
                        </div>

                        <div class="modal-form-group">
                            <label for="nombre">Nombre de la ruta</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ej: Ruta 3" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="destino">Destino</label>
                            <input type="text" id="destino" name="destino" placeholder="Ej: Centro" required>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label for="origen">Origen</label>
                            <input type="text" id="origen" name="origen" placeholder="Ej: Av. Principal" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="id_ruta_retorno">Ruta de retorno (Opcional)</label>
                            <select id="id_ruta_retorno" name="id_ruta_retorno">
                                <option value="">-- Sin ruta de retorno --</option>
                                <?php
                                $result_rutas = $conexion->query("SELECT id_ruta, nombre FROM rutas ORDER BY nombre");
                                while ($row_ruta = $result_rutas->fetch_assoc()) {
                                    echo "<option value='{$row_ruta['id_ruta']}'>{$row_ruta['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-cancel" id="cancelModal">Cancelar</button>
                    <button type="submit" class="modal-btn modal-btn-save">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para edición -->
    <div class="modal-overlay" id="editRouteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Editar ruta</h3>
                <button class="modal-close" id="closeEditModal">&times;</button>
            </div>
            <form id="editRouteForm" action="../../controllers/update/actualizar_ruta.php" method="POST">
                <input type="hidden" id="edit_id_ruta" name="id_ruta">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>RFC de Empresa</label>
                            <?php if ($_SESSION['rol'] == 1): ?>
                            <select id="edit_rfc_empresa" name="rfc_empresa" required>
                                <?php
                                $conn = $conexion;
                                $result = $conn->query("SELECT rfc_empresa, nombre FROM empresas");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['rfc_empresa']}'>{$row['nombre']}</option>";
                                }
                                ?>
                            </select>
                            <?php else: ?>
                            <input type="text" value="<?php echo htmlspecialchars($_SESSION['nombre']); ?>" readonly style="background-color: #f3f4f6; cursor: not-allowed;">
                            <input type="hidden" id="edit_rfc_empresa" name="rfc_empresa" value="<?php echo $_SESSION['rfc_empresa']; ?>">
                            <?php endif; ?>
                        </div>

                        <div class="modal-form-group">
                            <label for="edit_nombre">Nombre de la ruta</label>
                            <input type="text" id="edit_nombre" name="nombre" placeholder="Ej: Ruta 3" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_destino">Destino</label>
                            <input type="text" id="edit_destino" name="destino" placeholder="Ej: Centro" required>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label for="edit_origen">Origen</label>
                            <input type="text" id="edit_origen" name="origen" placeholder="Ej: Av. Principal" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_id_ruta_retorno">Ruta de retorno (Opcional)</label>
                            <select id="edit_id_ruta_retorno" name="id_ruta_retorno">
                                <option value="">-- Sin ruta de retorno --</option>
                                <?php
                                $result_rutas_edit = $conexion->query("SELECT id_ruta, nombre FROM rutas ORDER BY nombre");
                                while ($row_ruta_edit = $result_rutas_edit->fetch_assoc()) {
                                    echo "<option value='{$row_ruta_edit['id_ruta']}'>{$row_ruta_edit['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label for="edit_activa">Activa</label>
                            <select id="edit_activa" name="activa" required>
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-cancel" id="cancelEditModal">Cancelar</button>
                    <button type="submit" class="modal-btn modal-btn-save">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/notifications.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/pagination.js"></script>
    <script>



            // Cerrar modal de edición
        document.getElementById('closeEditModal').addEventListener('click', () => {
            document.getElementById('editRouteModal').classList.remove('active');
        });

        document.getElementById('cancelEditModal').addEventListener('click', () => {
            document.getElementById('editRouteModal').classList.remove('active');
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('editRouteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });

        // Verificar si hay mensaje de éxito (desde PHP)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            showNotification('Ruta actualizada exitosamente', 'success');
            // Limpiar URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        // Modal de agregar
        const btnAdd = document.querySelector('.btn-add');
        if (btnAdd) {
            btnAdd.addEventListener('click', () => {
                document.getElementById('addRouteModal').classList.add('active');
            });
        }

        const closeModalBtn = document.getElementById('closeModal');
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', () => {
                document.getElementById('addRouteModal').classList.remove('active');
            });
        }

        const cancelModalBtn = document.getElementById('cancelModal');
        if (cancelModalBtn) {
            cancelModalBtn.addEventListener('click', () => {
                document.getElementById('addRouteModal').classList.remove('active');
            });
        }

        document.getElementById('addRouteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });

        // Manejo del formulario de inserción
        handleInsertForm(document.getElementById('routeForm'), 'Ruta agregada exitosamente', function(data) {
            if (data.nuevoRegistro) {
                const tbody = document.querySelector('.data-table tbody');
                const noData = tbody.querySelector('td[colspan]');
                if (noData) {
                    noData.parentElement.remove();
                }
                
                const reg = data.nuevoRegistro;
                const statusClass = reg.activa == 1 ? 'status-active' : 'status-inactive';
                const statusText = reg.activa == 1 ? 'Sí' : 'No';
                
                // Tratar retorno
                const selectRetorno = document.getElementById('id_ruta_retorno');
                let nombreRetorno = '';
                if (reg.id_ruta_retorno && selectRetorno) {
                    const option = selectRetorno.querySelector(`option[value="${reg.id_ruta_retorno}"]`);
                    if (option) {
                        nombreRetorno = option.textContent;
                    }
                }
                
                let retornoBadge = '<span style="color:#94a3b8;font-size:12px;">— Sin par</span>';
                if (nombreRetorno) {
                    if (nombreRetorno.includes(' - ')) {
                        const nombreRetornoFormatted = nombreRetorno.replace(' - ', ' ⇄ ');
                        retornoBadge = `<span style="display:inline-block;background:#dbeafe;color:#1d4ed8;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:600;white-space:normal;line-height:1.2;">${nombreRetornoFormatted}</span>`;
                    } else {
                        retornoBadge = `<span style="display:inline-block;background:#dbeafe;color:#1d4ed8;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:600;white-space:normal;line-height:1.2;">⇄ ${nombreRetorno}</span>`;
                    }
                }

                const tr = document.createElement('tr');
                tr.setAttribute('data-id', reg.id_ruta);
                
                tr.innerHTML = `
                    <td data-label="Nombre" data-id="${reg.id_ruta}">${reg.nombre}</td>
                    <td data-label="Origen">${reg.origen}</td>
                    <td data-label="Destino">${reg.destino}</td>
                    <td data-label="Paradas"><a href="paradas_ruta.php" style="display:inline-flex;align-items:center;gap:5px;background:#fee2e2;color:#b91c1c;border-radius:12px;padding:3px 11px;font-size:12px;font-weight:600;text-decoration:none;">Sin paradas</a></td>
                    <td data-label="Ruta de retorno" data-id-retorno="${reg.id_ruta_retorno || ''}">${retornoBadge}</td>
                    <td data-label="Activa"><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td data-label="RFC de la Empresa">${reg.rfc_empresa}</td>
                    <td>
                        <div class="kebab-menu">
                            <button class="kebab-btn" onclick="toggleKebabMenu(this, event)">
                                <span class="material-icons">more_vert</span>
                            </button>
                            <div class="dropdown-content">
                                <button class="dropdown-item btn-edit">
                                    <span class="material-icons">edit_square</span> Editar
                                </button>
                                <button class="dropdown-item btn-delete">
                                    <span class="material-icons">delete_outline</span> Eliminar
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

                const deleteBtn = tr.querySelector('.btn-delete');
                if (deleteBtn) {
                    handleDeleteButton(deleteBtn, '../../controllers/delete/eliminar_ruta.php', 'id_ruta', '¿Estás seguro de que deseas eliminar esta ruta?', handleDeleteSuccess);
                }
            }
        });

        // Usar event delegation para botones de edición
        const tbody = document.querySelector('tbody');
        if (tbody) {
            tbody.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-edit');
                if (btn) {
                    const row = btn.closest('tr');
                    const cells = row.querySelectorAll('td');
                    
                    document.getElementById('edit_id_ruta').value = cells[0].getAttribute('data-id') || row.getAttribute('data-id');
                    document.getElementById('edit_nombre').value = cells[0].textContent.trim();
                    document.getElementById('edit_origen').value = cells[1].textContent.trim();
                    document.getElementById('edit_destino').value = cells[2].textContent.trim();
                    
                    const idRetorno = cells[4].getAttribute('data-id-retorno') || '';
                    document.getElementById('edit_id_ruta_retorno').value = idRetorno;
                    
                    const statusText = cells[5].querySelector('span').textContent.trim();
                    document.getElementById('edit_activa').value = statusText === 'Sí' ? 1 : 0;
                    document.getElementById('edit_rfc_empresa').value = cells[6].textContent.trim();
                    
                    document.getElementById('editRouteModal').classList.add('active');
                }
            });
        }

        // Manejo del formulario de edición
        handleUpdateForm(document.getElementById('editRouteForm'), 'Ruta actualizada exitosamente', function(data) {
            if (data.registroActualizado) {
                const reg = data.registroActualizado;
                const tr = document.querySelector(`tr td[data-id="${reg.id_ruta}"]`)?.closest('tr') || document.querySelector(`tr[data-id="${reg.id_ruta}"]`);
                if (tr) {
                    const cells = tr.querySelectorAll('td');
                    
                    cells[0].textContent = reg.nombre;
                    cells[1].textContent = reg.origen;
                    cells[2].textContent = reg.destino;
                    
                    cells[4].setAttribute('data-id-retorno', reg.id_ruta_retorno || '');
                    
                    const selectRetorno = document.getElementById('edit_id_ruta_retorno');
                    let nombreRetorno = '';
                    if (reg.id_ruta_retorno && selectRetorno) {
                        const option = selectRetorno.querySelector(`option[value="${reg.id_ruta_retorno}"]`);
                        if (option) {
                            nombreRetorno = option.textContent;
                        }
                    }
                    
                    let retornoBadge = '<span style="color:#94a3b8;font-size:12px;">— Sin par</span>';
                    if (nombreRetorno) {
                        if (nombreRetorno.includes(' - ')) {
                            const nombreRetornoFormatted = nombreRetorno.replace(' - ', ' ⇄ ');
                            retornoBadge = `<span style="display:inline-block;background:#dbeafe;color:#1d4ed8;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:600;white-space:normal;line-height:1.2;">${nombreRetornoFormatted}</span>`;
                        } else {
                            retornoBadge = `<span style="display:inline-block;background:#dbeafe;color:#1d4ed8;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:600;white-space:normal;line-height:1.2;">⇄ ${nombreRetorno}</span>`;
                        }
                    }
                    cells[4].innerHTML = retornoBadge;
                    
                    const statusClass = reg.activa == 1 ? 'status-active' : 'status-inactive';
                    const statusText = reg.activa == 1 ? 'Sí' : 'No';
                    cells[5].innerHTML = `<span class="status-badge ${statusClass}">${statusText}</span>`;
                    
                    cells[6].textContent = reg.rfc_empresa;
                    
                    Array.from(tr.children).forEach(td => {
                        td.style.transition = 'background-color 0.5s';
                        td.style.backgroundColor = '#dcfce7'; // Verde
                    });
                    
                    setTimeout(() => {
                        Array.from(tr.children).forEach(td => {
                            td.style.backgroundColor = '';
                        });
                    }, 1000);
                }
            }
        });

        const handleDeleteSuccess = function(data, button) {
            const row = button.closest('tr');
            if (row) {
                row.style.transition = 'opacity 0.5s';
                row.style.opacity = '0';
                
                Array.from(row.children).forEach(td => {
                    td.style.transition = 'background-color 0.5s';
                    td.style.backgroundColor = '#fee2e2'; // Rojo
                });
                
                setTimeout(() => {
                    row.remove();
                    if (window.paginationInstance) {
                        window.paginationInstance.allRows = window.paginationInstance.allRows.filter(r => r !== row);
                        window.paginationInstance.filterRows(document.getElementById('searchInput')?.value || '');
                    } else {
                        const tbody = document.querySelector('.data-table tbody');
                        const count = tbody.querySelectorAll('tr').length;
                        if (count === 0) {
                            tbody.innerHTML = '<tr><td colspan="8">No hay rutas registradas</td></tr>';
                        }
                    }
                }, 500);
            }
        };

        // Inicializar botones de eliminación
        initializeDeleteButtons(
            '.btn-delete',
            '../../controllers/delete/eliminar_ruta.php',
            'id_ruta',
            '¿Estás seguro de que deseas eliminar esta ruta?',
            handleDeleteSuccess
        );
    </script>
    <?php require_once __DIR__ . '/../../components/notifications_panel.php'; ?>
    <?php require_once __DIR__ . '/../../components/logout_modal.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const GW = { blue:'#0660fe', green:'#10b981', orange:'#f59e0b', red:'#ef4444', text:'#1a1c23', gray:'#e2e8f0', sub:'#94a3b8' };
    const baseOpt = { plugins: { legend: { position: 'bottom', labels: {font:{family:"'Inter',sans-serif", size:12}} } } };

    fetch('../../api/kpis_api.php?seccion=rutas').then(r=>r.json()).then(data => {
        if(!data.success) return;
        
        // 1. Render Stats
        document.getElementById('rutasStatsGrid').style.display = 'grid';
        document.getElementById('rutasStatsGrid').innerHTML = `
            <div class="stat-card"><div class="stat-card-icon"><span class="material-icons" style="color:var(--primary-color);">route</span></div><div class="stat-card-content"><h3>Rutas Activas</h3><p class="stat-number">${data.kpi.activas}</p><span class="stat-label">De ${data.kpi.total} totales</span></div></div>
            <div class="stat-card"><div class="stat-card-icon"><span class="material-icons" style="color:#10b981;">place</span></div><div class="stat-card-content"><h3>Paradas Totales</h3><p class="stat-number">${data.kpi.paradas}</p><span class="stat-label">En el sistema</span></div></div>
            <div class="stat-card"><div class="stat-card-icon"><span class="material-icons" style="color:#f59e0b;">loop</span></div><div class="stat-card-content"><h3>Rutas con Retorno</h3><p class="stat-number">${data.kpi.con_retorno}</p><span class="stat-label">Configuradas</span></div></div>
        `;

        // 2. Render Charts
        document.getElementById('rutasChartsGrid').style.display = 'grid';
        if(data.estado_rutas && data.estado_rutas.data.some(v=>v>0)) {
            new Chart(document.getElementById('chartEstadoRutas'), {
                type: 'doughnut', data: { labels: data.estado_rutas.labels, datasets: [{ data: data.estado_rutas.data, backgroundColor: [GW.blue, GW.red] }] }, options: {...baseOpt, cutout: '70%'}
            });
        }

        if(data.top_paradas && data.top_paradas.data.length > 0) {
            new Chart(document.getElementById('chartTopParadas'), {
                type: 'bar', data: { labels: data.top_paradas.labels.map(l=>l.substring(0,20)), datasets: [{ label:'Paradas', data: data.top_paradas.data, backgroundColor: GW.green, borderRadius:4 }] }, options: { indexAxis: 'y', plugins:{legend:{display:false}} }
            });
        }
    });
});
</script>
</body>
</html>
