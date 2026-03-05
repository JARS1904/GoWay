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
    <link rel="icon" href="../../assets/images/logo.png" type="image/png">
</head>
<body>
    <div class="container">
        <!-- Overlay para fondo oscuro -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

        <!-- Barra Superior Móvil -->
        <div class="mobile-topbar">
            <div class="mobile-topbar-content">
                <div class="mobile-topbar-left">
                    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
                    <h1 class="mobile-page-title">Gestión de Horarios</h1>
                </div>
                <div class="mobile-topbar-right">
                    <div class="mobile-user-info">
                        <span><?php echo $_SESSION['nombre']; ?></span>
                        <?php echo !empty($_SESSION['foto']) ? '<img src="../../assets/images/profiles/' . htmlspecialchars($_SESSION['foto']) . '" alt="Usuario" class="header-user-avatar">' : '<img src="../../assets/images/icons/administrador.png" alt="Usuario">'; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menú Lateral -->
        <aside class="sidebar" id="sidebar">
            <!-- Botón de Cerrar para Móvil -->
            <button class="sidebar-close" onclick="closeSidebar()">&times;</button>
            
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="Logo de GoWay" class="logo-img">
                <h1>GoWay</h1>
            </div>
            <nav>
                <ul>
                    <li>
                        <a href="../../index.php">
                            <img src="../../assets/images/icons/icon_dashboard.png" alt="Dashboard" class="icon">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="empresas.php">
                            <img src="../../assets/images/icons/icon_empresas.png" alt="Empresas" class="icon">
                            <span>Empresas</span>
                        </a>
                    </li>
                    <li>
                        <a href="conductores.php">
                            <img src="../../assets/images/icons/icon_conductores.png" alt="Conductores" class="icon">
                            <span>Conductores</span>
                        </a>
                    </li>
                    <li>
                        <a href="vehiculos.php">
                            <img src="../../assets/images/icons/icon_vehiculos.png" alt="Vehículos" class="icon">
                            <span>Vehículos</span>
                        </a>
                    </li>
                    <li>
                        <a href="rutas.php">
                            <img src="../../assets/images/icons/icon_rutas.png" alt="Rutas" class="icon">
                            <span>Rutas</span>
                        </a>
                    </li>
                    <li>
                        <a href="horarios.php">
                            <img src="../../assets/images/icons/icon_horarios.png" alt="Horarios" class="icon">
                            <span>Horarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="paradas.php">
                            <img src="../../assets/images/icons/icon_paradas.png" alt="Paradas" class="icon">
                            <span>Asignaciones</span>
                        </a>
                    </li>
                    <li>
                        <a href="checadores.php">
                            <img src="../../assets/images/icons/icon_checadores.png" alt="Checadores" class="icon">
                            <span>Checadores</span>
                        </a>
                    </li>
                    <li>
                        <a href="reportes.php">
                            <img src="../../assets/images/icons/icon_reportes.png" alt="Reportes" class="icon">
                            <span>Reportes</span>
                        </a>
                    </li>
                    <li>
                        <a href="usuarios.php">
                            <img src="../../assets/images/icons/icon_usuarios.png" alt="Usuarios" class="icon">
                            <span>Usuarios</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Botón de Cerrar Sesión -->
            <div class="logout-button">
                <a href="../login.php" id="logout">
                    <img src="../../assets/images/icons/icon_cerrar_sesion.png" alt="Cerrar sesión" class="icon"> 
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Gestión de Horarios</h2>
                <div class="user-info">
                    <span><?php echo $_SESSION['nombre']; ?></span>
                    <?php echo !empty($_SESSION['foto']) ? '<img src="../../assets/images/profiles/' . htmlspecialchars($_SESSION['foto']) . '" alt="Usuario" class="header-user-avatar">' : '<img src="../../assets/images/icons/administrador.png" alt="Usuario">'; ?>
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
                            <th>Día</th>
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
                                $dia       = htmlspecialchars($row['dia_semana']);
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
                                        <button class=\"btn-action btn-edit\" data-id=\"{$row['id_horario']}\">Editar</button>
                                        <button class=\"btn-action btn-delete\" data-id=\"{$row['id_horario']}\">Eliminar</button>
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
                        <label >Día de la semana</label>
                        <input type="text" id="" name="dia_semana" placeholder="Ej.Lunes, Martes, etc." required>
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
            <label>Día de la semana</label>
            <input type="text" name="dia_semana" id="edit_dia_semana" required>
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
            document.getElementById('edit_dia_semana').value  = row.dataset.dia;
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
</body>
</html>
