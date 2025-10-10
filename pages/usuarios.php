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
                <a href="login.php" id="logout">
                    <img src="../assets/images/icons/icon_cerrar_sesion.png" alt="Cerrar sesión" class="icon">
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="main-content">
            <header class="header">
                <h2>Gestión de Usuarios</h2>
                <div class="user-info">
                    <span>Admin</span>
                    <img src="../assets/images/icons/administrador.png" alt="Usuario">

                </div>
            </header>

            <section class="content">
                <h3>Lista de Usuarios</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID usuario</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Rol</th>
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

                        // Consulta para obtener los usuarios
                        $sql = "SELECT * FROM usuarios";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>
                                        <td data-label="ID Usuario" data-id="' . $row["id"] . '">' . $row["id"] . '</td>
                                        <td data-label="Nombre">' . $row["nombre"] . '</td>
                                        <td data-label="Email">' . $row["email"] . '</td>
                                        <td data-label="Password">' . $row["password"] . '</td>
                                        <td data-label="Rol">' . $row["rol"] . '</td>
                                        <td>
                                            <button class="btn-action btn-edit">Editar</button>
                                            <button class="btn-action btn-delete">Eliminar</button>
                                        </td>
                                    </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="6">No hay usuarios registrados</td></tr>';
                        }

                        $conn->close();
                        ?>
                    </tbody>
                </table>
                <button class="btn-add">Agregar nuevo usuario</button>
            </section>
        </main>
    </div>



    <!-- Modal para agregar nuevo usuario -->
    <div class="modal-overlay" id="addRouteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Agregar nuevo usuario</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <form id="routeForm" action="../controllers/insert_user.php" method="POST">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label for="nombre">Nombre
                            </label>
                            <input type="text" id="" name="nombre" placeholder="" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="Email">Email</label>
                            <input type="email" id="" name="email" placeholder="" required>
                        </div>

                    </div>

                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label for="">Contraseña</label>
                            <input type="text" id="" name="password" placeholder="" required>
                        </div>
                        <div class="modal-form-group">
                            <label for="">Rol</label>
                            <input type="text" id="" name="rol" placeholder=""></input>
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








    <!--  modal para edición de usuarios  -->
    <div class="modal-overlay" id="editUserModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Editar usuario</h3>
                <button class="modal-close" id="closeEditModal">&times;</button>
            </div>
            <form id="editUserForm" action="../pages/actualizar/actu_usuariosSql.php" method="POST">
                <input type="hidden" id="edit_id_usuario" name="id_usuario">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>Nombre</label>
                            <input type="text" id="edit_nombre" name="nombre">
                        </div>

                        <div class="modal-form-group">
                            <label for="edit_email">E-mail</label>
                            <input type="email" id="edit_email" name="email">
                        </div>
                    </div>

                        <!-- Columna derecha -->
                        <div>
                            <div class="modal-form-group">
                                <label for="edit_origen">Contraseña</label>
                                <input type="text" id="edit_password" name="password">
                            </div>
                            <div class="modal-form-group">
                                <label for="edit_rol">Rol</label>
                                <input type="text" id="edit_rol" name="rol">
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


    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/update/actu_usuarios.js"></script>
    <script src="../assets/js/delete/delete_usuarios.js"></script>
</body>

</html>