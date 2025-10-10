<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conductores - Transporte Público</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!--
    Esta línea causa cnflicto por lo que se comento
    <link rel="stylesheet" href="../assets/css/xd.css">
    -->

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
                            <span>Asinaciones</span>
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
                <a href="login.php" id="logout">
                    <img src="../assets/images/icons/icon_cerrar_sesion.png" alt="Cerrar sesión" class="icon"> 
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="main-content">
            <header class="header">
                <h2>Gestión de Empresas</h2>
                <div class="user-info">
                    <span>Admin</span>
                    <img src="../assets/images/icons/administrador.png" alt="Usuario">

                </div>
            </header>

            <section class="content">
                <h3>Lista de Empresas</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>RFC de la empresa</th>
                            <th>Nombre</th>
                            <th>Direccion</th>
                            <th>Teefono</th>
                            <th>Email</th>
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
                        
                        // Consulta para obtener las empresas
                        $sql = "SELECT * FROM empresas";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $statusClass = $row["activo"] ? 'status-active' : 'status-inactive';
                                $statusText = $row["activo"] ? 'Sí' : 'No';
                                
                                echo '<tr>
                                        <td data-label="RFC de la Empresa" data-id="'.$row["rfc_empresa"].'">'.$row["rfc_empresa"].'</td>
                                        <td data-label="Nombre">'.$row["nombre"].'</td>
                                        <td data-label="Dirección">'.$row["direccion"].'</td>
                                        <td data-label="Teléfono">'.$row["telefono"].'</td>
                                        <td data-label="Email">'.$row["email"].'</td>
                                        <td data-label="Activa"><span class="'.$statusClass.'">'.$statusText.'</span></td>
                                        <td>
                                            <button class="btn-action btn-edit">Editar</button>
                                            <button class="btn-action btn-delete">Eliminar</button>
                                        </td>
                                    </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7">No hay empresas registradas</td></tr>';
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

                <!--<button class="btn-add">Agregar nueva empresa</button>-->
                <button class="btn-add">+ Agregar nueva empresa</button>
            </section>
        </main>
    </div>


    <!-- Modal para agregar nueva Empresa -->
    <div class="modal-overlay" id="addRouteModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Agregar nueva empresa</h3>
            <button class="modal-close" id="closeModal">&times;</button>
        </div>
        <form id="routeForm" action="./controllers/insert_empresa.php" method="POST">
            <div class="modal-body">
                <!-- Columna izquierda -->
                <div>
                    <div class="modal-form-group">
                        <label for="nombre">RFC de la Empresa</label>
                        <input type="text" id="rfc_empresa" name="rfc_empresa" placeholder="" required>
                    </div>
                    <div class="modal-form-group">
                        <label for="destino">Nombre de Empresa</label>
                        <input type="text" id="nombre_empresa" name="nombre_empresa" placeholder="" required>
                    </div>
                    <div class="modal-form-group">
                        <label for="destino">Direccion de Empresa</label>
                        <input type="text" id="direccion_empresa" name="direccion_empresa" placeholder="" required>
                    </div>
                </div>
                
                <!-- Columna derecha -->
                <div>
                    <div class="modal-form-group">
                        <label for="origen">Telefono</label>
                        <input type="text" id="tel_empresa" name="tel_empresa" placeholder="" required>
                    </div>
                    <div class="modal-form-group">
                        <label for="paradas">E-mail</label>
                        <input type="email" id="email_empresa" name="email_empresa" placeholder=""></input>
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

<div class="modal-overlay" id="editEmpresasModal">
  <div class="modal-container">
    <div class="modal-header">
      <h3>Editar Empresa</h3>
      <button class="modal-close" id="closeEditEmpresasModal">×</button>
    </div>
    <form id="editEmpresasForm" action="../pages/actualizar/actu_empresasSql.php" method="POST">
      <!--<input type="text" id="edit_rfc_conductor" name="rfc_conductor" >-->
      <div class="modal-body">
        <div>
          <div class="modal-form-group">
            <label for="edit_rfc_empresa">RFC de la empresa</label>
            <input type="text" id="edit_rfc_empresa" name="rfc_empresa" required>
          </div>

          <div class="modal-form-group">
            <label for="edit_nombre_empresa">Nombre de Empresa</label>
            <input type="text" id="edit_nombre_empresa" name="nombre_empresa" required>
          </div>

          <div class="modal-form-group">
            <label for="edit_capacidad">Direccion de Empresa</label>
            <input type="text" id="edit_direccion_empresa" name="direccion_empresa" required>
          </div>
        </div>
        <div>

        <div class="modal-form-group">
            <label for="edit_licencia">Telefono</label>
            <input type="text" id="edit_telefono" name="telefono" required>
          </div>

        <div class="modal-form-group">
            <label for="edit_capacidad">E-mail</label>
            <input type="text" id="edit_email_empresa" name="email_empresa" required>
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
<script src="../assets/js/update/actu_empresas.js"></script>
<script src="../assets/js/delete/delete_empresas.js"></script>
<script src="../assets/js/pagination.js"></script>

</body>
</html>