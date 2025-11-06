<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignaciones - Transporte Público</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Flex:opsz,wght@8..144,100..1000&display=swap" rel="stylesheet">
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
                    <h1 class="mobile-page-title">Gestión de Asignaciones</h1>
                </div>
                <div class="mobile-topbar-right">
                    <div class="mobile-user-info">
                        <span>Admin</span>
                        <img src="../assets/images/icons/administrador.png" alt="Usuario">
                    </div>
                </div>
            </div>
        </div>

        <!-- Menú Lateral -->
        <aside id="sidebar" class="sidebar">
            <!-- Botón de Cerrar para Móvil -->
            <button class="sidebar-close" onclick="closeSidebar()">&times;</button>
            
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
        <main class="main-content" id="mainContent">
            <!-- Header para escritorio -->
            <header class="header">
                <h2>Gestión de Asignaciones</h2>
                <div class="user-info">
                    <span>Admin</span>
                    <img src="../assets/images/icons/administrador.png" alt="Usuario">
                </div>
            </header>

            <section class="content">
                <h3>Lista de Asignaciones</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>RFC de la empresa</th>
                            <th>ID vehiculo</th>
                            <th>RFC del conductor</th>
                            <th>ID Ruta</th>
                            <th>ID Horario</th>
                            <th>Fecha</th>
                            <th>Activa</th>
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
                        
                        // Consulta para obtener las asignaciones
                        $sql = "SELECT * FROM asignaciones";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $statusClass = $row["activa"] ? 'status-active' : 'status-inactive';
                                $statusText = $row["activa"] ? 'Sí' : 'No';
                                
                                echo '<tr>
                                        <td data-label="RFC de la Empresa" data-id="'.$row["id_asignacion"].'">'.$row["rfc_empresa"].'</td>
                                        <td data-label="ID Vehículo">'.$row["id_vehiculo"].'</td>
                                        <td data-label="RFC Conductor">'.$row["rfc_conductor"].'</td>
                                        <td data-label="ID Ruta">'.$row["id_ruta"].'</td>
                                        <td data-label="ID Horario">'.$row["id_horario"].'</td>
                                        <td data-label="Fecha">'.$row["fecha"].'</td>
                                        <td data-label="Activa"><span class="'.$statusClass.'">'.$statusText.'</span></td>
                                        <td>
                                            <button class="btn-action btn-delete">Eliminar</button>
                                        </td>
                                    </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="8">No hay asignaciones registradas</td></tr>';
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

                <button class="btn-add">+ Agregar nueva asignación</button>
            </section>
        </main>
    </div>

    <!-- Modal para agregar nueva Asignación -->
    <div class="modal-overlay" id="addRouteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Agregar nueva Asignacion</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <form id="routeForm" action="../controllers/insert_asignaciones.php" method="POST">
                <div class="modal-body">
                    <!-- Columna izquierda -->
                    <div>
                        <div class="modal-form-group">
                            <label>RFC de la Empresa</label>
                            <select name="rfc_empresa" required>
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
                            <label>ID de vehiculo</label>
                            <select name="id_vehiculo" required>
                                <?php
                                $conn = new mysqli("localhost", "root", "", "goway");
                                $result = $conn->query("SELECT id_vehiculo, placa FROM vehiculos");
                                while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_vehiculo']}'>{$row['placa']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>RFC Conductor</label>
                            <select name="rfc_conductor" required>
                                <?php
                                $conn = new mysqli("localhost", "root", "", "goway");
                                $result = $conn->query("SELECT rfc_conductor, nombre FROM conductores");
                                while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['rfc_conductor']}'>{$row['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Columna derecha -->
                    <div>
                        <div class="modal-form-group">
                            <label>ID Ruta</label>
                            <select name="id_ruta" required>
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
                            <label>ID horario</label>
                            <select name="id_horario" required>
                                <?php
                                $conn = new mysqli("localhost", "root", "", "goway");
                                $result = $conn->query("SELECT id_horario, dia_semana FROM horarios");
                                while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_horario']}'>{$row['dia_semana']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-form-group">
                            <label>Fecha</label>
                            <input type="date" name="fecha" required placeholder="Fecha">
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

    <script>
        // Funciones para el menú hamburguesa
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleBtn = document.querySelector('.toggle-btn');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            
            // Ocultar/mostrar botón hamburguesa
            if (sidebar.classList.contains('active')) {
                toggleBtn.style.opacity = '0';
                toggleBtn.style.visibility = 'hidden';
            } else {
                toggleBtn.style.opacity = '1';
                toggleBtn.style.visibility = 'visible';
            }
            
            // Prevenir scroll del body cuando el menú está abierto
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleBtn = document.querySelector('.toggle-btn');
            
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            
            // Mostrar botón hamburguesa al cerrar
            toggleBtn.style.opacity = '1';
            toggleBtn.style.visibility = 'visible';
            
            document.body.style.overflow = '';
        }

        // Cerrar sidebar al hacer clic en un enlace (en móvil)
        document.querySelectorAll('.sidebar nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });

        // Cerrar sidebar con tecla ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });

        // Ajustar en redimensionamiento de ventana
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });
    </script>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/delete/delete_asignaciones.js"></script>
    <script src="../assets/js/pagination.js"></script>
</body>
</html>