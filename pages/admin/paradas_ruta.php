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
    <title>Paradas - GoWay</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" href="../../assets/images/logo_new.png" type="image/png">
    <style>
        .badge-orden {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            border-radius: 12px;
            padding: 2px 10px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-minutos {
            display: inline-block;
            background: #dcfce7;
            color: #15803d;
            border-radius: 12px;
            padding: 2px 10px;
            font-size: 12px;
            font-weight: 600;
        }
        .stop-origin  { color: #b45309; font-weight: 700; }
        .stop-dest    { color: #7c3aed; font-weight: 700; }
        #loadingStops { display: none; text-align: center; padding: 24px; color: #64748b; }
        .stops-table-wrap { overflow-x: auto; }
        /* Ocultar toolbar de main.js hasta que la tabla sea visible */
        .table-toolbar { display: none; }

        /* Controles de selección de ruta */
        .route-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .route-controls select {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            color: #374151;
            background: #fff;
            cursor: pointer;
            flex: 1;
            min-width: 0;
        }
        .route-controls .btn-add {
            white-space: nowrap;
            margin: 0;
        }
        @media (max-width: 768px) {
            .route-controls {
                flex-direction: column;
                align-items: stretch;
                width: 100%;
            }
            .route-controls select,
            .route-controls .btn-add {
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<div class="container">
    <?php
    $page_title  = 'Paradas';
    $active_page = 'paradas';
    $base_url    = '../../';
    require_once __DIR__ . '/../../components/sidebar.php';
    ?>

    <!-- Contenido Principal -->
    <main class="main-content" id="mainContent">
        <header class="header">
            <h2>Paradas</h2>
                            <div class="header-notif-wrap">
                    <button class="notification-bell" id="desktopNotifBtn" onclick="toggleNotifications()">
                        <span class="material-icons">notifications_none</span>
                    </button>
                </div>
        </header>

        <section class="content">

            <div class="section-header">
                <h3>Paradas</h3>
                <div class="route-controls">
                    <select id="routeSelect">
                        <option value="">— Selecciona una ruta —</option>
                        <?php
                        $where_emp = ($_SESSION['rol'] == 4) ? " WHERE rfc_empresa = '".$_SESSION['rfc_empresa']."'" : "";
                        $res = $conexion->query(
                            "SELECT id_ruta, nombre, origen, destino FROM rutas" . $where_emp . " ORDER BY nombre ASC"
                        );
                        while ($r = $res->fetch_assoc()) {
                            $label = htmlspecialchars($r['nombre'] . ' (' . $r['origen'] . ' → ' . $r['destino'] . ')');
                            echo "<option value=\"{$r['id_ruta']}\" data-origen=\"" . htmlspecialchars($r['origen']) . "\" data-destino=\"" . htmlspecialchars($r['destino']) . "\">{$label}</option>";
                        }
                        ?>
                    </select>
                    <button class="btn-add" id="btnAddStop" disabled>+ Agregar parada</button>
                </div>
            </div>

            <div id="loadingStops">Cargando paradas…</div>

            <div class="stops-table-wrap">
                <table class="data-table" id="stopsTable" style="display:none;">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Nombre de la parada</th>
                            <th>Minutos desde origen</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="stopsBody"></tbody>
                </table>
            </div>

            <div id="noStopsMsg" style="display:none; color:#94a3b8; padding:16px;">
                Esta ruta aún no tiene paradas registradas. Usa <strong>+ Agregar parada</strong> para comenzar.<br>
                <small>Tip: agrega siempre el punto de partida (orden 0, minutos 0) y el destino final como última parada.</small>
            </div>

        </section>
    </main>
</div>

<!-- ═══ Modal Agregar / Editar Parada ═══ -->
<div class="modal-overlay" id="stopModal">
    <div class="modal-container" style="max-width:420px;">
        <div class="modal-header">
            <h3 id="stopModalTitle">Agregar parada</h3>
            <button class="modal-close" id="closeStopModal">&times;</button>
        </div>
        <form id="stopForm">
            <input type="hidden" id="f_id_parada"  name="id_parada">
            <input type="hidden" id="f_id_ruta"    name="id_ruta">
            <div class="modal-body" style="display:block;">
                <div class="modal-form-group">
                    <label for="f_nombre">Nombre de la parada</label>
                    <input type="text" id="f_nombre" name="nombre" placeholder="Ej: Cupilco" required>
                </div>
                <div class="modal-form-group">
                    <label for="f_orden">Orden en la ruta</label>
                    <input type="number" id="f_orden" name="orden" min="0" required
                           placeholder="0 = primera parada (origen)">
                    <small style="color:#64748b;">0 = punto de partida, 1 = primera parada, etc.</small>
                </div>
                <div class="modal-form-group">
                    <label for="f_minutos">Minutos desde el origen</label>
                    <input type="number" id="f_minutos" name="minutos_desde_origen" min="0" required
                           placeholder="0 si es el punto de partida">
                    <small style="color:#64748b;">Tiempo estimado en minutos que tarda el bus en llegar a esta parada desde el origen.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-cancel" id="cancelStopModal">Cancelar</button>
                <button type="submit" class="modal-btn modal-btn-save" id="saveStopBtn">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══ Modal Confirmar Eliminación ═══ -->
<div class="modal-overlay" id="deleteStopModal">
    <div class="modal-container" style="max-width:380px;">
        <div class="modal-header">
            <h3>Eliminar parada</h3>
            <button class="modal-close" id="closeDeleteModal">&times;</button>
        </div>
        <div class="modal-body" style="display:block; padding:20px 24px;">
            <p>¿Seguro que quieres eliminar la parada <strong id="deleteStopName"></strong>?</p>
            <p style="color:#ef4444; font-size:13px;">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" id="cancelDeleteModal">Cancelar</button>
            <button type="button" class="modal-btn modal-btn-save" id="confirmDeleteBtn"
                    style="background:#ef4444;">Eliminar</button>
        </div>
    </div>
</div>

<script src="../../assets/js/notifications.js"></script>
<script src="../../assets/js/main.js"></script>
<script>
const API_PARADAS    = '../../api/routes_api.php';
const CTRL_INSERT    = '../../controllers/insert_parada.php';
const CTRL_UPDATE    = '../../controllers/update/update_parada.php';
const CTRL_DELETE    = '../../controllers/delete/delete_parada.php';

let currentRouteId   = null;
let deleteTargetId   = null;

const routeSelect    = document.getElementById('routeSelect');
const btnAddStop     = document.getElementById('btnAddStop');
const stopsTable     = document.getElementById('stopsTable');
const stopsBody      = document.getElementById('stopsBody');
const noStopsMsg     = document.getElementById('noStopsMsg');
const loadingStops   = document.getElementById('loadingStops');

// ── Cargar paradas al cambiar la ruta seleccionada ──
routeSelect.addEventListener('change', () => {
    currentRouteId = routeSelect.value ? parseInt(routeSelect.value) : null;
    btnAddStop.disabled = !currentRouteId;

    if (!currentRouteId) {
        hideTables();
        return;
    }

    loadStops(currentRouteId);
});

function hideTables() {
    stopsTable.style.display = 'none';
    noStopsMsg.style.display = 'none';
}

async function loadStops(id_ruta) {
    hideTables();
    loadingStops.style.display = 'block';

    try {
        const res  = await fetch(`${API_PARADAS}?action=paradas&id_ruta=${id_ruta}`);
        const data = await res.json();

        loadingStops.style.display = 'none';

        if (!Array.isArray(data) || data.length === 0) {
            noStopsMsg.style.display = 'block';
            return;
        }

        stopsTable.style.display  = 'table';
        renderStops(data);

    } catch (err) {
        loadingStops.style.display = 'none';
        noStopsMsg.style.display   = 'block';
        console.error(err);
    }
}

function renderStops(paradas) {
    stopsBody.innerHTML = '';

    // ordenar por orden ascendente (ya viene ordenado pero por seguridad)
    paradas.sort((a, b) => a.orden - b.orden);
    const maxOrden = Math.max(...paradas.map(p => p.orden));

    paradas.forEach(p => {
        const isFirst = p.orden === 0;
        const isLast  = p.orden === maxOrden && paradas.length > 1;
        let nameClass = '';
        let roleLabel = '';
        if (isFirst) { nameClass = 'stop-origin'; roleLabel = ' <em style="font-size:11px;color:#b45309;">(origen)</em>'; }
        if (isLast)  { nameClass = 'stop-dest';   roleLabel = ' <em style="font-size:11px;color:#7c3aed;">(destino)</em>'; }

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td data-label="Orden">
                <span class="badge-orden">${p.orden}</span>
            </td>
            <td data-label="Parada">
                <span class="${nameClass}">${escHtml(p.nombre)}</span>${roleLabel}
            </td>
            <td data-label="Minutos">
                <span class="badge-minutos">${p.minutos_desde_origen} min</span>
            </td>
            <td>
                <div class="kebab-menu">
                    <button class="kebab-btn" onclick="toggleKebabMenu(this, event)">
                        <span class="material-icons">more_vert</span>
                    </button>
                    <div class="dropdown-content">
                        <button class="dropdown-item btn-edit" onclick="openEditModal(${p.id_parada}, '${escAttr(p.nombre)}', ${p.orden}, ${p.minutos_desde_origen})">
                            <span class="material-icons">edit_square</span> Editar
                        </button>
                        <button class="dropdown-item btn-delete" onclick="openDeleteModal(${p.id_parada}, '${escAttr(p.nombre)}')">
                            <span class="material-icons">delete_outline</span> Eliminar
                        </button>
                    </div>
                </div>
            </td>
        `;
        stopsBody.appendChild(tr);
    });
}

// ── Escape helpers (seguridad XSS) ──
function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function escAttr(str) {
    return String(str).replace(/'/g,"\\'");
}

// ── Modal Agregar ──
btnAddStop.addEventListener('click', () => {
    document.getElementById('stopModalTitle').textContent = 'Agregar parada';
    document.getElementById('f_id_parada').value  = '';
    document.getElementById('f_id_ruta').value    = currentRouteId;
    document.getElementById('f_nombre').value     = '';
    document.getElementById('f_orden').value      = '';
    document.getElementById('f_minutos').value    = '';
    document.getElementById('stopModal').classList.add('active');
});

// ── Modal Editar ──
function openEditModal(id_parada, nombre, orden, minutos) {
    document.getElementById('stopModalTitle').textContent = 'Editar parada';
    document.getElementById('f_id_parada').value  = id_parada;
    document.getElementById('f_id_ruta').value    = currentRouteId;
    document.getElementById('f_nombre').value     = nombre;
    document.getElementById('f_orden').value      = orden;
    document.getElementById('f_minutos').value    = minutos;
    document.getElementById('stopModal').classList.add('active');
}

// ── Modal Eliminar ──
function openDeleteModal(id_parada, nombre) {
    deleteTargetId = id_parada;
    document.getElementById('deleteStopName').textContent = nombre;
    document.getElementById('deleteStopModal').classList.add('active');
}

// ── Cerrar modales ──
['closeStopModal','cancelStopModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', () => {
        document.getElementById('stopModal').classList.remove('active');
    });
});
['closeDeleteModal','cancelDeleteModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', () => {
        document.getElementById('deleteStopModal').classList.remove('active');
    });
});
document.getElementById('stopModal').addEventListener('click', e => {
    if (e.target === document.getElementById('stopModal'))
        document.getElementById('stopModal').classList.remove('active');
});
document.getElementById('deleteStopModal').addEventListener('click', e => {
    if (e.target === document.getElementById('deleteStopModal'))
        document.getElementById('deleteStopModal').classList.remove('active');
});

// ── Guardar (add / edit) ──
document.getElementById('stopForm').addEventListener('submit', async e => {
    e.preventDefault();
    const btn  = document.getElementById('saveStopBtn');
    const isEdit = !!document.getElementById('f_id_parada').value;
    const url  = isEdit ? CTRL_UPDATE : CTRL_INSERT;

    btn.disabled = true;
    btn.textContent = 'Guardando…';

    try {
        const fd  = new FormData(e.target);
        const res = await fetch(url, { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            document.getElementById('stopModal').classList.remove('active');
            showNotification(data.message, 'success');
            setTimeout(() => loadStops(currentRouteId), 800);
        } else {
            showNotification(data.message || 'Error al guardar', 'error');
        }
    } catch (err) {
        showNotification('Error de conexión', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Guardar';
    }
});

// ── Confirmar eliminación ──
document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
    if (!deleteTargetId) return;
    const btn = document.getElementById('confirmDeleteBtn');
    btn.disabled = true;

    try {
        const fd = new FormData();
        fd.append('id_parada', deleteTargetId);
        const res  = await fetch(CTRL_DELETE, { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            document.getElementById('deleteStopModal').classList.remove('active');
            showNotification(data.message, 'success');
            setTimeout(() => loadStops(currentRouteId), 800);
        } else {
            showNotification(data.message || 'Error al eliminar', 'error');
        }
    } catch (err) {
        showNotification('Error de conexión', 'error');
    } finally {
        btn.disabled = false;
        deleteTargetId = null;
    }
});
</script>
    <?php require_once __DIR__ . '/../../components/notifications_panel.php'; ?>
    <?php require_once __DIR__ . '/../../components/logout_modal.php'; ?>
</body>
</html>
