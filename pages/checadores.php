<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conductores - Transporte Público</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Flex:opsz,wght@8..144,100..1000&display=swap" rel="stylesheet">
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
                        <a href="reportes.html">
                            <img src="../assets/images/icons/icon_reportes.png" alt="Reportes" class="icon">
                            <span>Reportes</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Botón de Cerrar Sesión -->
            <div class="logout-button">
                <a href="login.PHP" id="logout">
                    <img src="../assets/images/icons/icon_cerrar_sesion.png" alt="Cerrar sesión" class="icon"> 
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="main-content">
            <header class="header">
                <h2>Gestión de Checadores</h2>
                <div class="user-info">
                    <span>Admin</span>
                    <img src="../assets/images/icons/administrador.png" alt="Usuario">

                </div>
            </header>

            <section class="content">
                <h3>Lista de Checadores</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>RFC del checador</th>
                            <th>RFC de empresa</th>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Contraseña</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Conexión a la base de datos
                        $conn = new mysqli("localhost", "root", "", "goway");
                        
                        if ($conn->connect_error) {
                            die("Error de conexión: " . $conn->connect_error);
                        }
                        
                        // Consulta para obtener los checadores
                        $sql = "SELECT * FROM checadores";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $statusClass = $row["activo"] ? 'status-active' : 'status-inactive';
                                $statusText = $row["activo"] ? 'Sí' : 'No';
                                
                                echo '<tr>
                                        <td data-label="RFC Checador" data-id="'.$row["rfc_checador"].'">'.$row["rfc_checador"].'</td>
                                        <td data-label="RFC Empresa">'.$row["rfc_empresa"].'</td>
                                        <td data-label="Nombre">'.$row["nombre"].'</td>
                                        <td data-label="Usuario">'.$row["usuario"].'</td>
                                        <td data-label="Contraseña">'.$row["contrasena"].'</td>
                                        <td data-label="Estado"><span class="'.$statusClass.'">'.$statusText.'</span></td>
                                        <td>
                                            <button class="btn-action btn-edit">Editar</button>
                                            <button class="btn-action btn-delete">Eliminar</button>
                                        </td>
                                    </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7">No hay checadores registrados</td></tr>';
                        }
                        
                        $conn->close();
                        ?>
                    </tbody>
                </table>
                <button class="btn-add">Agregar nuevo checador</button>
            </section>
        </main>
    </div>

       <!-- Modal para agregar nuevo checador -->
<div class="modal-overlay" id="addRouteModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Agregar nuevo checador</h3>
            <button class="modal-close" id="closeModal">&times;</button>
        </div>
        <form id="routeForm" action="../controllers/insert_checador.php" method="POST">
            <div class="modal-body">
                <!-- Columna izquierda -->
                <div>
                    <div class="modal-form-group">
                        <label >RFC de Checador</label>
                        <input type="text" id="" name="rfc_checador" placeholder="" required>
                    </div>
                    <div class="modal-form-group">
                        <label >RFC de la Empresa</label>
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
                    <div class="modal-form-group">
                        <label >Nombre</label>
                        <input type="text" id="" name="nombre" placeholder="" required>
                    </div>
                </div>
                
                <!-- Columna derecha -->
                <div>
                    <div class="modal-form-group">
                        <label >Usuario</label>
                        <select name="usuario" id="">
                        <?php
                        $conn = new mysqli("localhost", "root", "", "goway");
                        $result = $conn->query("SELECT email, nombre FROM usuarios");
                        while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['email']}'>{$row['nombre']}</option>";
                        }
                        ?>

                        </select>


                    </div>
                    <div class="modal-form-group">
                        <label >Contraseña</label>
                        <input type="text" id="" name="password" placeholder=""></input>
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



   <!-- Modal para editar conductores -->

<div class="modal-overlay" id="editChecadoresModal">
  <div class="modal-container">
    <div class="modal-header">
      <h3>Editar Checador</h3>
      <button class="modal-close" id="closeEditChecadoresModal">×</button>
    </div>
    <form id="editChecadoresForm" action="../pages/actualizar/actu_checadoresSql.php" method="POST">
      <!--<input type="text" id="edit_rfc_conductor" name="rfc_conductor" >-->
      <div class="modal-body">
        <div>
          <div class="modal-form-group">
            <label for="edit_rfc_checador">RFC de Checador</label>
            <input type="text" id="edit_rfc_checador" name="rfc_checador" required>
          </div>

          <div class="modal-form-group">
            <label for="edit_rfc_empresa">RFC de Empresa</label>
            <input type="text" id="edit_rfc_empresa" name="rfc_empresa" required>
          </div>

          <div class="modal-form-group">
            <label for="edit_capacidad">Nombre</label>
            <input type="text" id="edit_nombre" name="nombre" required>
          </div>
        </div>
        <div>

        <div class="modal-form-group">
            <label for="edit_licencia">Usuario</label>
            <input type="text" id="edit_usuario" name="usuario" required>
          </div>

        <div class="modal-form-group">
            <label for="edit_capacidad">Contraseña</label>
            <input type="text" id="edit_password" name="password" required>
          </div>

          <div class="modal-form-group">
            <label for="edit_activo">Activo</label>
            <select id="edit_activo" name="activo">
              <option value="1">Sí</option>
              <option value="0">No</option>
            </select>
          </div>


        </div>
        <div>

        
                                
        </div>
        <div>

        
         

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="modal-btn modal-btn-cancel" id="cancelEditEmpresasModal">Cancelar</button>
        <button type="submit" class="modal-btn modal-btn-save">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>



<script src="../assets/js/main.js"></script>
<script src="../assets/js/update/actu_checadores.js"></script>
<script src="../assets/js/delete/delete_checadores.js"></script>
</body>
</html>