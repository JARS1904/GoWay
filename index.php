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
                        <a href="index.html">
                            <img src="assets/images/icons/icon_dashboard.png" alt="Dashboard" class="icon">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/rutas.html">
                            <img src="assets/images/icons/icon_rutas.png" alt="Rutas" class="icon">
                            <span>Rutas</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/horarios.html">
                            <img src="assets/images/icons/icon_horarios.png" alt="Horarios" class="icon">
                            <span>Horarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/vehiculos.html">
                            <img src="assets/images/icons/icon_vehiculos.png" alt="Vehículos" class="icon">
                            <span>Vehículos</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/conductores.html">
                            <img src="assets/images/icons/icon_conductores.png" alt="Conductores" class="icon">
                            <span>Conductores</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/empresas.html">
                            <img src="assets/images/icons/icon_empresas.png" alt="Empresas" class="icon">
                            <span>Empresas</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/checadores.html">
                            <img src="assets/images/icons/icon_checadores.png" alt="Checadores" class="icon">
                            <span>Checadores</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/paradas.html">
                            <img src="assets/images/icons/icon_paradas.png" alt="Paradas" class="icon">
                            <span>Paradas</span>
                        </a>
                    </li>
                    <li>
                        <a href="pages/usuarios.html">
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
                    <img src="https://via.placeholder.com/40" alt="Usuario">
                </div>
            </header>

            <section class="content">
                <h3>Resumen General</h3>
                <div class="card">
                    <h4>Rutas Activas</h4>
                    <p>Total: 10</p>
                </div>
                <div class="card">
                    <h4>Vehículos en Operación</h4>
                    <p>Total: 15</p>
                </div>
                <div class="card">
                    <h4>Conductores Activos</h4>
                    <p>Total: 20</p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>