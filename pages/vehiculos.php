<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehículos - Transporte Público</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!--
    Esta línea causa comflicto por lo que se comento
    <link rel="stylesheet" href="../assets/css/xd.css">
    -->

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
                        <a href="usuarios.html">
                            <img src="../assets/images/icons/icon_usuarios.png" alt="Usuarios" class="icon">
                            <span>Usuarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="reportes.html">
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
                <h2>Gestión de Vehículos</h2>
                <div class="user-info">
                    <span>Admin</span>
                    <img src="../assets/images/icons/administrador.png" alt="Usuario">

                </div>
            </header>

            <section class="content">
                <h3>Lista de Vehículos</h3>
                <table class="data-table">
                    <thead>
                        <tr>

                            <th>Número de placa</th>
                            <th>Modelo</th>
                            <th>Capacidad</th>
                            <th>RFC de la empresa</th>
                            <th>Activa</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Conexión a la base de datos
                    $conn = new mysqli("localhost", "root", "", "goway");

                    if ($conn->connect_error) {
                        die("Error de conexión: " . $conn->connect_error);
                    }

                    // Consulta para obtener los vehículos
                    $sql = "SELECT * FROM vehiculos";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $statusClass = $row["activo"] ? 'status-active' : 'status-inactive';
                            $statusText = $row["activo"] ? 'Sí' : 'No';
                            
                            echo '<tr>
                                    <td data-label="Placa" data-id="'.$row["id_vehiculo"].'">' . $row["placa"] . '</td>
                                    <td data-label="Modelo">' . $row["modelo"] . '</td>
                                    <td data-label="Capacidad">' . $row["capacidad"] . '</td>
                                    <td data-label="RFC de la Empresa">' . $row["rfc_empresa"] . '</td>
                                    <td data-label="Activa"><span class="'.$statusClass.'">' . $statusText . '</span></td>
                                    <td>
                                        <button class="btn-action btn-edit">Editar</button>
                                        <button class="btn-action btn-delete">Eliminar</button>
                                    </td>
                                </tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">No hay vehículos registrados</td></tr>';
                    }

                    $conn->close();
                    ?>
                </tbody>
                </table>
                
                <!-- Paginación -->
                <div class="pagination">
                    <button class="pagination-btn" id="prevPage" disabled>‹ Anterior</button>
                    <div class="pagination-info" id="pageInfo">Página 1 de 5</div>
                    <button class="pagination-btn" id="nextPage">Siguiente ›</button>
                </div>

                <button class="btn-add">+ Agregar nuevo vehículo</button>


            </section>
        </main>
    </div>




    <!-- Modal para agregar nuevo vehiculo -->
    <div class="modal-overlay" id="addRouteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Agregar nuevo Vehiculo</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <form id="routeForm" action="../controllers/insert_vehiculos.php" method="POST">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>Placa del Vehiculo</label>
                            <input type="text" id="" name="placa" placeholder="" required>
                        </div>
                        <div class="modal-form-group">
                            <label>RFC de Empresa</label>
                            <select name="rfc_empresa" id="">

                                <?php
                                $conn = new mysqli("localhost", "root", "", "goway");
                                $result = $conn->query("SELECT rfc_empresa, nombre FROM empresas");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['rfc_empresa']}'>{$row['nombre']}</option>";
                                }
                                ?>

                            </select>


                        </div>

                    </div>

                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label>Modelo de Vehiculo</label>
                            <input type="text" id="" name="modelo" placeholder="" required>
                        </div>
                        <div class="modal-form-group">
                            <label>Capacidad del Vehiculo</label>
                            <input type="number" id="" name="capacidad" placeholder=""></input>
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










    <!-- Modal para editar vehiculo -->
<div class="modal-overlay" id="editVehicleModal">
  <div class="modal-container">
    <div class="modal-header">
      <h3>Editar Vehículo</h3>
      <button class="modal-close" id="closeEditVehicleModal">×</button>
    </div>
    <form id="editVehicleForm" action="../controllers/update/actu_vehiculos.php" method="POST">
      <input type="number" id="edit_id_vehiculo" name="id_vehiculo">
      <div class="modal-body">
        <div>
          <div class="modal-form-group">
            <label for="edit_placa">Placa</label>
            <input type="text" id="edit_placa" name="placa" required>
          </div>
          <div class="modal-form-group">
            <label for="edit_modelo">Modelo</label>
            <input type="text" id="edit_modelo" name="modelo" required>
          </div>
          <div class="modal-form-group">
            <label for="edit_capacidad">Capacidad</label>
            <input type="number" id="edit_capacidad" name="capacidad" required>
          </div>
        </div>
        <div>
          <div class="modal-form-group">
            <label for="edit_activo">Activo</label>
            <select id="edit_activo" name="activo">
              <option value="1">Sí</option>
              <option value="0">No</option>
            </select>
          </div>
          <div class="modal-form-group">
            <label for="edit_rfc_empresa">RFC Empresa</label>
            <input type="text" id="edit_rfc_empresa" name="rfc_empresa" required >
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="modal-btn modal-btn-cancel" id="cancelEditVehicleModal">Cancelar</button>
        <button type="submit" class="modal-btn modal-btn-save">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/update/actu_vehiculo.js"></script>
    <script src="../assets/js/delete/delete_vehiculos.js"></script>
    <script src="../assets/js/pagination.js"></script>
</body>
</html>