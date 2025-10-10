<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Transporte Público</title>
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
    <div class="container">
        <!-- Menú Lateral -->
        <aside class="sidebar">
            <div class="logo">
                <img src="assets/images/logo.png" alt="Logo de GoWay" class="logo-img">
                <h1>GoWay</h1>
            </div>
            <nav>
                <ul>
                    <li>
                        <a href="/index.php">
                            <img src="assets/images/icons/icon_dashboard.png" alt="Dashboard" class="icon">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/rutas.php">
                            <img src="assets/images/icons/icon_rutas.png" alt="Rutas" class="icon">
                            <span>Rutas</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/horarios.php">
                            <img src="assets/images/icons/icon_horarios.png" alt="Horarios" class="icon">
                            <span>Horarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/vehiculos.php">
                            <img src="assets/images/icons/icon_vehiculos.png" alt="Vehículos" class="icon">
                            <span>Vehículos</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/conductores.php">
                            <img src="assets/images/icons/icon_conductores.png" alt="Conductores" class="icon">
                            <span>Conductores</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/empresas.php">
                            <img src="assets/images/icons/icon_empresas.png" alt="Empresas" class="icon">
                            <span>Empresas</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/checadores.php">
                            <img src="assets/images/icons/icon_checadores.png" alt="Checadores" class="icon">
                            <span>Checadores</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/paradas.php">
                            <img src="assets/images/icons/icon_paradas.png" alt="Paradas" class="icon">
                            <span>Asignaciones</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/usuarios.php">
                            <img src="assets/images/icons/icon_usuarios.png" alt="Usuarios" class="icon">
                            <span>Usuarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/reportes.html">
                            <img src="assets/images/icons/icon_reportes.png" alt="Reportes" class="icon">
                            <span>Reportes</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Botón de Cerrar Sesión -->
            <div class="logout-button">
                <a href="pages/login.php" id="logout">
                    <img src="assets/images/icons/icon_cerrar_sesion.png" alt="Cerrar sesión" class="icon">
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="main-content">
            <header class="header">
                <h2>Dashboard</h2>
                <div class="user-info">
                    <span>Admin</span>
                    <img src="./assets/images/icons/administrador.png" alt="Usuario">
                </div>
            </header>




            <section class="content">
                <h3>Resumen General</h3>
                <?php
                $conn = new mysqli("localhost", "root", "", "goway");

                if ($conn->connect_error) {
                    die("Error de conexión: " . $conn->connect_error);
                }

                $sql = "SELECT 
                        (SELECT COUNT(*) FROM empresas) AS total_empresas,
                        (SELECT COUNT(*) FROM rutas) AS total_rutas,
                        (SELECT COUNT(*) FROM vehiculos) AS total_vehiculos,
                        (SELECT COUNT(*) FROM conductores) AS total_conductores";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $total_empresas = $row['total_empresas'];
                $total_rutas = $row['total_rutas'];
                $total_vehiculos = $row['total_vehiculos'];
                $total_conductores = $row['total_conductores'];
                ?>
                <div class="card">
                    <h4>Empresas Registradas</h4>
                    <p>Total: <?php echo $total_empresas; ?></p>
                </div>
                <div class="card">
                    <h4>Rutas Activas</h4>
                    <p>Total: <?php echo $total_rutas; ?></p>
                </div>
                <div class="card">
                    <h4>Vehículos en Operación</h4>
                    <p>Total: <?php echo $total_vehiculos; ?></p>
                </div>
                <div class="card">
                    <h4>Conductores Activos</h4>
                    <p>Total: <?php echo $total_conductores; $conn->close();?></p>
                </div>

                <button class="btn-add"> + Agregar nuevo servicio</button>
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
                    <button type="submit" class="modal-btn modal-btn-save">Guardar</button>
                </div>
            </form>
        </div>
    </div>











    <script src="assets/js/main.js"></script>
</body>

</html>