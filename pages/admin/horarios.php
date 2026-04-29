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
        <form id="routeForm" action="../../controllers/insert_horarios.php" method="POST">
            <div class="modal-body">
                <!-- Columna izquierda -->
                <div>
                    <div class="modal-form-group">
                        <label >Ruta</label>
                        <select name="id_ruta" id="">
                            
                            <?php
                            $conn = $conexion;
                            $result = $conn->query("SELECT id_ruta, nombre FROM rutas");
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
    <form id="editRouteForm" action="actualizar/actu_horariosSql.php" method="POST">
      <input type="hidden" name="id_horario" id="edit_id_horario">
      <div class="modal-body">
        <div>
          <div class="modal-form-group">
            <label>Ruta</label>
            <select name="id_ruta" id="edit_id_ruta">
              <?php
              $conn = $conexion;
              $result = $conn->query("SELECT id_ruta, nombre FROM rutas");
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
        'Horario agregado exitosamente'
    );

    // Actualización
    handleUpdateForm(
        document.getElementById('editRouteForm'),
        'Horario actualizado exitosamente'
    );

    // Eliminación
    initializeDeleteButtons(
        '.btn-delete',
        '/GoWay/controllers/delete/delete_horarios.php',
        'id_horario',
        '¿Estás seguro de que deseas eliminar este horario?'
    );

    // Editar: leer datos del data-* del tr
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            document.getElementById('edit_id_horario').value  = row.dataset.id;
            document.getElementById('edit_id_ruta').value     = row.dataset.idRuta;
            document.getElementById('edit_tipo_dia').value  = row.dataset.tipoDia;
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
</body>
</html>
