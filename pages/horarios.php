<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios - Transporte Público</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/images/logo.png" type="image/png">
</head>
<body>
    <div class="container">
        <!-- Menú Lateral -->
        <aside class="sidebar">
            <div class="logo">
                <img src="../assets/images/logo.png" alt="Logo de GoWay" class="logo-img">
                <h1>GoWay</h1>
            </div>
            <nav>
                <ul>
                    <li>
                        <a href="../index.php">
                            <img src="../assets/images/icons/icon_dashboard.png" alt="Dashboard" class="icon">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="rutas.php">
                            <img src="../assets/images/icons/icon_rutas.png" alt="Rutas" class="icon">
                            <span>Rutas</span>
                        </a>
                    </li>
                    <li>
                        <a href="horarios.php">
                            <img src="../assets/images/icons/icon_horarios.png" alt="Horarios" class="icon">
                            <span>Horarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="vehiculos.php">
                            <img src="../assets/images/icons/icon_vehiculos.png" alt="Vehículos" class="icon">
                            <span>Vehículos</span>
                        </a>
                    </li>
                    <li>
                        <a href="conductores.php">
                            <img src="../assets/images/icons/icon_conductores.png" alt="Conductores" class="icon">
                            <span>Conductores</span>
                        </a>
                    </li>
                    <li>
                        <a href="empresas.php">
                            <img src="../assets/images/icons/icon_empresas.png" alt="Empresas" class="icon">
                            <span>Empresas</span>
                        </a>
                    </li>
                    <li>
                        <a href="checadores.php">
                            <img src="../assets/images/icons/icon_checadores.png" alt="Checadores" class="icon">
                            <span>Checadores</span>
                        </a>
                    </li>
                    <li>
                        <a href="paradas.php">
                            <img src="../assets/images/icons/icon_paradas.png" alt="Paradas" class="icon">
                            <span>Asignaciones</span>
                        </a>
                    </li>
                    <li>
                        <a href="usuarios.php">
                            <img src="../assets/images/icons/icon_usuarios.png" alt="Usuarios" class="icon">
                            <span>Usuarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="reportes.php">
                            <img src="../assets/images/icons/icon_reportes.png" alt="Reportes" class="icon">
                            <span>Reportes</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Botón de Cerrar Sesión -->
            <div class="logout-button">
                <a href="login.php" id="logout">
                    <img src="../assets/images/icons/icon_cerrar_sesion.png" alt="Cerrar sesión" class="icon"> 
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="main-content">
            <header class="header">
                <h2>Gestión de Horarios</h2>
                <div class="user-info">
                    <span>Admin</span>
                    <img src="../assets/images/icons/administrador.png" alt="Usuario">

                </div>
            </header>

            <section class="content">
                <h3>Horarios Disponibles</h3>
                                    <?php
                    // Conexión a la base de datos
                    $conn = new mysqli("localhost", "root", "", "goway");

                    if ($conn->connect_error) {
                        die("Error de conexión: " . $conn->connect_error);
                    }

                    // Consulta para obtener los horarios
                    $sql = "SELECT * FROM horarios";
                    $result = $conn->query($sql);

                    echo '<div class="card-container">'; // Contenedor principal para las cards

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '
                            <div class="card">
                                <div class="card-header">
                                    <h3>Horario #'.$row["id_horario"].'</h3>
                                    <span class="route-id">Ruta ID: '.$row["id_ruta"].'</span>
                                </div>
                                <div class="card-body">
                                    <div class="schedule-info">
                                        <p><strong>Día:</strong> '.$row["dia_semana"].'</p>
                                        <p><strong>Salida:</strong> '.$row["hora_salida"].'</p>
                                        <p><strong>Llegada:</strong> '.$row["hora_llegada"].'</p>
                                    </div>
                                    <div class="frequency-info">
                                        <p><strong>Frecuencia:</strong> '.$row["frecuencia"].'</p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <small>Creado: '.$row["created_at"].'</small>
                                    <div class="card-actions">
                                        <button class="btn-action btn-edit">Editar</button>
                                        <button class="btn-action btn-delete">Eliminar</button>
                                    </div>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<div class="no-schedules">No hay horarios registrados</div>';
                    }

                    echo '</div>'; // Cierre del contenedor

                    $conn->close();
                    ?>

                
                <button class="btn-add">Agregar Nuevo Horario</button>
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
        <form id="routeForm" action="../controllers/insert_horarios.php" method="POST">
            <div class="modal-body">
                <!-- Columna izquierda -->
                <div>
                    <div class="modal-form-group">
                        <label >Ruta</label>
                        <select name="id_ruta" id="">
                            
                            <?php
                            $conn = new mysqli("localhost", "root", "", "goway");
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
    <form id="editRouteForm" action="../pages/actualizar/actu_horariosSql.php" method="POST">
      <input type="text" name="id_horario" id="edit_id_horario">
      <div class="modal-body">
        <div>
          <div class="modal-form-group">
            <label>Ruta</label>
            <select name="id_ruta" id="edit_id_ruta">
              <?php
              $conn = new mysqli("localhost", "root", "", "goway");
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




<script src="../assets/js/main.js"></script>
<script src="../assets/js/update/actu_horarios.js"></script>
</body>
</html>