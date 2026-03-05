<?php
session_start();

// Verificar si el usuario está logueado y tiene rol=2
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoWay - Rutas de Transporte</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../../assets/images/logo.png" type="image/png">
    <style>
        :root {
            --primary-color: #2962FF;
            --primary-dark: #1565C0;
            --secondary-color: #FFC107;
            --text-color: #333;
            --light-gray: #f5f5f5;
            --medium-gray: #e0e0e0;
            --dark-gray: #757575;
            --black-color: #000000;
            --error-color: #D32F2F;
            --success-color: #388E3C;
            --header-height: 64px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            color: var(--text-color);
            background-color: var(--light-gray);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            display: flex;
            flex: 1;
            width: 100%;
        }

        /* Columna izquierda */
        .left-column {
            flex: 0 0 450px;
            padding: 20px;
            border-right: 1px solid var(--medium-gray);
            background-color: white;
            overflow-y: auto;
            overflow-x: hidden;
            height: calc(100vh - var(--header-height));
            position: sticky;
            top: var(--header-height);
        }

        /* Columna derecha */
        .right-column {
            flex: 1;
            padding: 20px;
            background-color: var(--light-gray);
            overflow-y: auto;
            height: calc(100vh - var(--header-height));
        }

        header {
            background-color: white;
            box-shadow: 0 1px 0 #e8e8e8;
            padding: 0 24px;
            height: var(--header-height);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1600px;
            margin: 0 auto;
            height: 100%;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 40px;
            margin-right: 10px;
        }

        .logo h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--black-color);
        }

        /* Estilos para el menú desplegable (app web del usuario)*/
        .user-dropdown {
            position: relative;
            display: inline-block;
            margin-left: auto;
        }

        /* Nav links visibles en desktop */
        .header-nav {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
        }

        .header-nav a {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            color: var(--dark-gray);
            border: 1.5px solid transparent;
            transition: all 0.2s;
        }

        .header-nav a:hover {
            border-color: var(--medium-gray);
            color: var(--primary-color);
            background-color: var(--light-gray);
        }

        .header-nav a.download-btn {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .header-nav a.download-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .header-nav a.logout {
            border-color: transparent;
            color: var(--dark-gray);
        }

        .header-nav a.logout:hover {
            border-color: #ffcdd2;
            background-color: #fff5f5;
            color: var(--error-color);
        }

        /* Ocultar dropdown en desktop */
        @media (min-width: 769px) {
            .user-dropdown { display: none; }
        }

        /* Ocultar nav en móvil */
        @media (max-width: 768px) {
            .header-nav { display: none; }
            .user-dropdown { display: inline-block; }
        }

        .user-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .user-btn:hover {
            background-color: var(--primary-dark);
            transform: scale(1.05);
        }

        .user-btn i {
            font-size: 18px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            z-index: 1000;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 10px;
        }

        .dropdown-content a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }

        .dropdown-content a:hover {
            background-color: #f5f5f5;
            color: var(--primary-color);
        }

        .dropdown-content a i {
            width: 20px;
            text-align: center;
        }

        .user-dropdown:hover .dropdown-content {
            display: block;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .search-section {
            background-color: transparent;
            border-radius: 0;
            padding: 10px 0 20px 0;
            box-shadow: none;
            margin-bottom: 0;
        }

        .greeting {
            font-size: 22px;
            font-weight: 700;
            color: var(--black-color);
            margin-bottom: 4px;
        }

        .greeting span {
            color: var(--black-color);
        }

        .search-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            color: var(--black-color);
        }

        .search-form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-gray);
        }

        select, button {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--medium-gray);
            border-radius: 18px;
            font-size: 16px;
            transition: all 0.3s;
        }

        select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn:hover {
            background-color: var(--primary-dark);
        }

        .btn i {
            margin-right: 8px;
        }

        .btn:disabled {
            background-color: var(--medium-gray);
            cursor: not-allowed;
        }

        .divider {
            height: 1px;
            background-color: var(--medium-gray);
            margin: 25px 0;
        }

        .results-section {
            background-color: transparent;
            border-radius: 0;
            padding: 0;
            box-shadow: none;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            color: var(--black-color);
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
            flex-direction: column;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(0,0,0,0.1);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 10px;
        }

        .loading-text {
            color: var(--dark-gray);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .no-routes {
            text-align: center;
            padding: 30px;
            color: var(--dark-gray);
        }

        .route-card {
            background-color: white;
            border-radius: 16px;
            border: 1px solid #e8e8e8;
            padding: 18px 20px;
            margin-bottom: 14px;
            transition: box-shadow 0.2s, transform 0.2s;
            cursor: pointer;
            position: relative;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }

        .route-card:hover {
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .route-card.selected {
            border-color: var(--primary-color);
            box-shadow: 0 4px 16px rgba(41,98,255,0.15);
        }

        /* Fila superior de la tarjeta: icono + empresa + corazón */
        .route-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .route-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .route-card-title i {
            font-size: 20px;
            color: var(--primary-color);
        }

        .route-company {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 17px;
        }

        /* Línea divisora dentro de la tarjeta */
        .route-card-divider {
            height: 1px;
            background-color: #f0f0f0;
            margin: 12px 0;
        }

        /* Fila origen → destino */
        .route-path {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0;
            flex-wrap: wrap;
            font-size: 15px;
            color: var(--text-color);
        }

        .route-path > i.arrow {
            color: var(--dark-gray);
            font-size: 13px;
        }

        .route-origin, .route-destination {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .route-origin i {
            color: #D32F2F;
            font-size: 14px;
        }

        .route-destination i {
            color: #388E3C;
            font-size: 14px;
        }

        /* Fila de horarios disponibles + botón */
        .route-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
        }

        .route-schedule {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-color);
            font-size: 14px;
        }

        .route-schedule i {
            color: var(--primary-color);
            font-size: 16px;
        }

        .route-schedule-count {
            font-weight: 700;
            font-size: 15px;
        }

        .btn-details {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            white-space: nowrap;
            width: auto;
        }

        .btn-details:hover {
            background-color: var(--primary-dark);
        }

        /* Detalles en columna derecha */
        .route-details {
            background-color: transparent;
            padding: 0;
        }

        /* Encabezado de detalle: ruta nombre grande */
        .route-detail-header {
            margin-bottom: 20px;
        }

        .route-detail-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--black-color);
            margin-bottom: 4px;
        }

        .route-full-path {
            font-size: 15px;
            color: var(--dark-gray);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .route-full-path i {
            font-size: 12px;
            color: var(--dark-gray);
        }

        .detail-divider {
            height: 1px;
            background-color: var(--medium-gray);
            margin: 18px 0;
        }

        /* Tarjeta contenedora blanca */
        .detail-card {
            background-color: white;
            border-radius: 16px;
            border: 1px solid #e8e8e8;
            padding: 20px 22px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }

        .info-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 18px;
            color: var(--black-color);
        }

        /* Fila icono + label encima + value abajo */
        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-row i {
            font-size: 20px;
            color: var(--primary-color);
            width: 22px;
            text-align: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .info-text {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 13px;
            color: var(--dark-gray);
            font-weight: 400;
            line-height: 1.3;
        }

        .info-value {
            font-size: 15px;
            font-weight: 500;
            color: var(--black-color);
            line-height: 1.4;
        }

        /* Encabezado de tarjeta de horario */
        .schedule-card {
            background-color: white;
            border-radius: 16px;
            border: 1px solid #e8e8e8;
            padding: 18px 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }

        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .schedule-company-name {
            font-weight: 700;
            font-size: 15px;
            color: var(--black-color);
        }

        .schedule-day {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--success-color);
            font-weight: 500;
            font-size: 14px;
        }

        .schedule-day i {
            font-size: 16px;
        }

        /* Fila origen → destino dentro del horario */
        .schedule-route-path {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 700;
            color: var(--black-color);
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f0f0f0;
        }

        .schedule-route-path > i {
            color: var(--dark-gray);
            font-size: 13px;
        }

        /* Salida / Llegada */
        .schedule-times {
            display: flex;
            justify-content: flex-start;
            gap: 40px;
            margin: 0 0 16px 0;
            padding: 12px 16px;
            background-color: #f8f9ff;
            border-radius: 10px;
        }

        /* Grid de 2 columnas: detalles a la izquierda, conductor/vehículo a la derecha */
        .schedule-card-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 24px;
            align-items: start;
        }

        .time-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .time-group i {
            font-size: 18px;
        }

        .departure i {
            color: var(--primary-color);
        }

        .arrival i {
            color: var(--error-color);
        }

        .time-text {
            display: flex;
            flex-direction: column;
        }

        .time-label {
            font-size: 12px;
            color: var(--dark-gray);
        }

        .time-value {
            font-size: 22px;
            font-weight: 700;
            color: var(--black-color);
            line-height: 1.2;
        }

        .schedule-details {
            margin-top: 4px;
        }

        .detail-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 14px;
            color: var(--text-color);
        }

        .detail-row i {
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .stops-list {
            margin: 4px 0 10px 30px;
            list-style: none;
            padding: 0;
        }

        .stops-list li {
            margin-bottom: 6px;
            font-size: 14px;
            color: var(--dark-gray);
            position: relative;
            padding-left: 14px;
        }

        .stops-list li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: var(--dark-gray);
        }

        /* Separador dentro del schedule-card para driver/vehicle */
        .driver-vehicle-info {
            margin-top: 0;
            padding-top: 0;
            border-top: none;
        }

        .driver-vehicle-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .driver-vehicle-row i {
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .driver-vehicle-row strong {
            font-weight: 600;
            color: var(--black-color);
        }

        /* Grid 2 columnas para info de empresa */
        .info-rows-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 16px;
        }

        /* Toast notification */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: var(--error-color);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            z-index: 1100;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast i {
            margin-right: 10px;
        }

        /* No selection view */
        .no-selection {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
            color: var(--dark-gray);
        }

        .no-selection i {
            font-size: 60px;
            color: var(--medium-gray);
            margin-bottom: 20px;
        }

        .no-selection h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--dark-gray);
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .container {
                flex-direction: column;
            }
            
            .left-column {
                flex: 1;
                border-right: none;
                border-bottom: 1px solid var(--medium-gray);
                height: auto;
                position: static;
            }
            
            .right-column {
                flex: 1;
                height: auto;
            }
            
            .route-details {
                margin-top: 30px;
            }
        }

        @media (max-width: 600px) {
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .info-label {
                width: 100%;
                margin-bottom: 5px;
            }
            
            .schedule-times {
                flex-direction: column;
                align-items: center;
                gap: 20px;
                padding: 0;
            }
            
            .route-full-path {
                flex-direction: column;
            }
            
            .route-full-path i {
                margin: 10px 0;
                transform: rotate(90deg);
            }
        }

        .icon-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            margin-left: 15px; /* Separación del logo */
        }

        .icon-btn:hover {
            background-color: var(--primary-dark);
            transform: scale(1.05);
        }

        .icon-btn i {
            font-size: 14px;
        }

        /* Botón de favorita */
        .favorite-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            color: var(--dark-gray);
            transition: transform 0.2s ease, color 0.2s;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-left: auto;
            width: auto;
            height: auto;
            line-height: 1;
        }

        .favorite-btn:hover {
            transform: scale(1.2);
        }

        .favorite-btn.active {
            color: #D32F2F;
        }

        /* Filtro de favoritas */
        .filter-section {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--medium-gray);
        }

        .filter-btn {
            padding: 8px 16px;
            border: 2px solid var(--medium-gray);
            background-color: white;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 500;
        }

        .filter-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .filter-btn.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .favorites-list {
            margin-bottom: 30px;
        }

        .favorites-section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
        }

        .favorites-section-title i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="GoWay Logo">
                <h1>GoWay</h1>
            </div>

            <!-- Nav links para desktop -->
            <nav class="header-nav">
                <a href="https://goway.netlify.app" target="_blank" class="download-btn">
                    <i class="fas fa-download"></i> Descargar App
                </a>
                <a href="../logout.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            </nav>

            <!-- Menú desplegable solo para móvil -->
            <div class="user-dropdown">
                <button class="user-btn">
                    <i class="fas fa-user-circle"></i>
                </button>
                <div class="dropdown-content">
                    <a href="https://goway.netlify.app" target="_blank">
                        <i class="fas fa-download"></i> Descargar App
                    </a>
                    <a href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Columna izquierda - Búsqueda y resultados -->
        <div class="left-column">
            <section class="search-section">
                <p class="greeting">Hola, <span><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></span> &#128075;</p>
                <h2 class="search-title">¿A dónde quieres ir?</h2>
                <form id="searchForm" class="search-form">
                    <div class="form-group">
                        <label for="origin">Origen</label>
                        <select id="origin" required>
                            <option value="">Seleccione el origen</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="destination">Destino</label>
                        <select id="destination" required>
                            <option value="">Seleccione el destino</option>
                        </select>
                    </div>
                    <button type="submit" id="searchBtn" class="btn" disabled>
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </form>
            </section>

            <div class="divider"></div>

            <section class="results-section">
                <h2 class="section-title">Disponibles</h2>
                
                <div class="filter-section">
                    <button class="filter-btn active" id="filterAll" data-filter="all">
                        <i class="fas fa-list"></i> Todas
                    </button>
                    <button class="filter-btn" id="filterFavorites" data-filter="favorites">
                        <i class="fas fa-heart"></i> Favoritas
                    </button>
                </div>
                
                <div id="resultsContainer">
                    <div class="no-routes">
                        <p>Seleccione origen y destino para buscar rutas</p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Columna derecha - Detalles de ruta -->
        <div class="right-column">
            <div id="routeDetailsContainer">
                <div class="no-selection">
                    <i class="fas fa-route"></i>
                    <h3>Selecciona una ruta</h3>
                    <p>Elige una ruta de la lista para ver los detalles completos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <i class="fas fa-exclamation-circle"></i>
        <span id="toastMessage"></span>
    </div>

    <script>
        // Configuración de API
        const API_BASE_URL = window.location.origin; // Obtiene http://localhost
        const API_URL = `${API_BASE_URL}/GoWay/api/routes_api.php`;
        const FAVORITES_URL = `${API_BASE_URL}/GoWay/api/favorites_routes_api.php`;
        const ID_USUARIO = <?php echo isset($_SESSION['id']) ? $_SESSION['id'] : 0; ?>;
        
        // Datos mock para cuando falle la API
        const MOCK_LOCATIONS = ["Centro", "Norte", "Sur", "Este", "Oeste"];
        const MOCK_ROUTES = [{
            id_ruta: 1,
            nombre: "Ruta de Prueba",
            origen: "Centro",
            destino: "Norte",
            empresa_nombre: "Transportes Ejemplo",
            empresa_telefono: "555-1234",
            empresa_direccion: "Calle Falsa 123",
            empresa_email: "contacto@ejemplo.com",
            horarios: [{
                dia_semana: "Lunes a Viernes",
                hora_salida: "08:00",
                hora_llegada: "09:30",
                frecuencia: "Cada 30 minutos",
                conductor_nombre: "Juan Pérez",
                conductor_licencia: "LIC-12345",
                vehiculo_modelo: "Mercedes Benz",
                vehiculo_placa: "ABC-123",
                vehiculo_capacidad: 40
            }],
            paradas: ["Parada A", "Parada B", "Parada C"]
        }];

        // Elementos del DOM
        const originSelect = document.getElementById('origin');
        const destinationSelect = document.getElementById('destination');
        const searchForm = document.getElementById('searchForm');
        const resultsContainer = document.getElementById('resultsContainer');
        const routeDetailsContainer = document.getElementById('routeDetailsContainer');
        const searchBtn = document.getElementById('searchBtn');
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        
        // Elementos de filtro
        const filterAllBtn = document.getElementById('filterAll');
        const filterFavoritesBtn = document.getElementById('filterFavorites');
        
        // Estado
        let routes = [];
        let selectedRouteId = null;
        let favorites = new Set();
        let currentFilter = 'all';
        let locations = [];

        // Inicializar la aplicación
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Iniciando aplicación...');
            console.log('URL base:', API_BASE_URL);
            console.log('URL API:', API_URL);
            
            fetchLocations();
            loadFavorites();
            
            // Escuchadores de eventos del formulario
            searchForm.addEventListener('submit', handleSearch);
            originSelect.addEventListener('change', updateSearchButton);
            destinationSelect.addEventListener('change', updateSearchButton);
            
            // Escuchadores de eventos de filtro
            filterAllBtn.addEventListener('click', () => {
                currentFilter = 'all';
                updateFilterButtons();
                filterAndDisplayRoutes();
            });
            
            filterFavoritesBtn.addEventListener('click', () => {
                currentFilter = 'favorites';
                updateFilterButtons();
                loadFavoritesAndDisplay();
            });
        });

        // Cargar rutas favoritas del servidor
        async function loadFavorites() {
            try {
                const response = await fetch(`${FAVORITES_URL}?action=get_favorites&id_usuario=${ID_USUARIO}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                console.log('Response favoritas:', response.status);
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('Datos favoritas recibidos:', data);
                    
                    if (Array.isArray(data)) {
                        data.forEach(fav => {
                            favorites.add(fav.id_ruta);
                        });
                        console.log('Favoritas cargadas:', favorites);
                    }
                } else {
                    console.error('Error en respuesta favoritas:', response.status);
                }
            } catch (error) {
                console.error('Error al cargar favoritas:', error);
            }
        }

        // Actualizar apariencia de botones de filtro
        function updateFilterButtons() {
            filterAllBtn.classList.toggle('active', currentFilter === 'all');
            filterFavoritesBtn.classList.toggle('active', currentFilter === 'favorites');
        }

        // Filtrar y mostrar rutas según el filtro actual
        function filterAndDisplayRoutes() {
            if (currentFilter === 'favorites') {
                if (routes.length === 0) {
                    // Si no hay búsqueda realizada, traer favoritas desde BD
                    loadFavoritesAndDisplay();
                } else {
                    const favoriteRoutes = routes.filter(route => favorites.has(route.id_ruta));
                    if (favoriteRoutes.length === 0) {
                        resultsContainer.innerHTML = '<div class="no-routes"><p>No tienes rutas favoritas aún</p></div>';
                    } else {
                        displayRoutes(favoriteRoutes);
                    }
                }
            } else {
                displayRoutes(routes);
            }
        }

        async function loadFavoritesAndDisplay() {
            try {
                showLoading(true, resultsContainer);
                
                const response = await fetch(`${FAVORITES_URL}?action=get_favorites&id_usuario=${ID_USUARIO}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const favoritesData = await response.json();
                    console.log('Favoritas cargadas:', favoritesData);
                    
                    if (Array.isArray(favoritesData) && favoritesData.length > 0) {
                        displayRoutes(favoritesData);
                    } else {
                        resultsContainer.innerHTML = '<div class="no-routes"><p>No tienes rutas favoritas aún</p></div>';
                    }
                } else {
                    resultsContainer.innerHTML = '<div class="no-routes"><p>Error al cargar favoritas</p></div>';
                }
            } catch (error) {
                console.error('Error al cargar favoritas:', error);
                resultsContainer.innerHTML = '<div class="no-routes"><p>Error al cargar favoritas</p></div>';
            } finally {
                showLoading(false, resultsContainer);
            }
        }

        // Obtener ubicaciones disponibles de la API
        async function fetchLocations() {
            try {
                showLoading(true);
                console.log('Consultando API en:', `${API_URL}?action=locations`);
                
                const response = await fetch(`${API_URL}?action=locations`, {
                    headers: {
                        'Accept': 'application/json'
                    },
                    cache: 'no-cache'
                });
                
                console.log('Respuesta recibida. Status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Error en respuesta:', errorText);
                    throw new Error(`Error HTTP ${response.status}: ${errorText}`);
                }
                
                const data = await response.json();
                console.log('Datos recibidos:', data);
                
                if (Array.isArray(data)) {
                    locations = data;
                } else {
                    console.warn('La API devolvió un array vacío o formato inválido');
                    throw new Error('Datos de ubicaciones no válidos');
                }
            } catch (error) {
                console.error('Error al cargar ubicaciones:', error);
                showToast('Error al cargar ubicaciones. Usando datos de prueba.');
                locations = MOCK_LOCATIONS;
            } finally {
                populateLocationSelects();
                showLoading(false);
            }
        }

        // Llenar selects de origen y destino con ubicaciones
        function populateLocationSelects() {
            console.log('Llenando selects con:', locations);
            
            // Limpiar opciones existentes (manteniendo la primera opción vacía)
            while (originSelect.options.length > 1) originSelect.remove(1);
            while (destinationSelect.options.length > 1) destinationSelect.remove(1);
            
            // Agregar nuevas opciones
            locations.forEach(location => {
                const option1 = document.createElement('option');
                option1.value = location;
                option1.textContent = location;
                originSelect.appendChild(option1);
                
                const option2 = document.createElement('option');
                option2.value = location;
                option2.textContent = location;
                destinationSelect.appendChild(option2);
            });
            
            updateSearchButton();
        }

        // Actualizar estado del botón de búsqueda según selecciones
        function updateSearchButton() {
            const hasSelection = originSelect.value && destinationSelect.value;
            searchBtn.disabled = !hasSelection;
        }

        // Manejar envío de formulario de búsqueda
        async function handleSearch(e) {
            e.preventDefault();
            
            const origin = originSelect.value;
            const destination = destinationSelect.value;
            
            if (!origin || !destination) {
                showToast('Seleccione origen y destino');
                return;
            }
            
            try {
                showLoading(true, resultsContainer);
                console.log(`Buscando rutas de ${origin} a ${destination}`);
                
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'search_routes',
                        origin: origin,
                        destination: destination
                    })
                });
                
                console.log('Respuesta de búsqueda. Status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`Error HTTP ${response.status}: ${errorText}`);
                }
                
                const data = await response.json();
                console.log('Rutas encontradas:', data);
                
                if (Array.isArray(data)) {
                    routes = processRoutes(data);
                } else if (data.error) {
                    throw new Error(data.error);
                } else {
                    throw new Error('Formato de respuesta no válido');
                }
            } catch (error) {
                console.error('Error buscando rutas:', error);
                showToast(error.message || 'Error al buscar rutas. Mostrando datos de prueba.');
                routes = [{
                    ...MOCK_ROUTES[0],
                    origen: origin,
                    destino: destination
                }];
            } finally {
                displayRoutes(routes);
                showLoading(false, resultsContainer);
                
                // Reset selección
                selectedRouteId = null;
                showNoSelection();
            }
        }

        // Procesar rutas para combinar duplicados con diferentes horarios
        function processRoutes(rawRoutes) {
            const uniqueRoutes = {};
            
            rawRoutes.forEach(route => {
                const routeId = route.id_ruta;
                
                if (uniqueRoutes[routeId]) {
                    // Combinar horarios
                    const scheduleMap = {};
                    
                    // Agregar horarios existentes
                    uniqueRoutes[routeId].horarios.forEach(schedule => {
                        const key = `${schedule.dia_semana}-${schedule.hora_salida}-${schedule.hora_llegada}`;
                        scheduleMap[key] = schedule;
                    });
                    
                    // Agregar nuevos horarios
                    route.horarios.forEach(schedule => {
                        const key = `${schedule.dia_semana}-${schedule.hora_salida}-${schedule.hora_llegada}`;
                        scheduleMap[key] = schedule;
                    });
                    
                    // Actualizar horarios
                    uniqueRoutes[routeId].horarios = Object.values(scheduleMap);
                } else {
                    // Agregar nueva ruta
                    uniqueRoutes[routeId] = {...route};
                    
                    // Asegurar que paradas es un array
                    if (typeof uniqueRoutes[routeId].paradas === 'string') {
                        uniqueRoutes[routeId].paradas = uniqueRoutes[routeId].paradas.split(', ');
                    }
                }
            });
            
            return Object.values(uniqueRoutes);
        }

        // Mostrar rutas en el contenedor de resultados
        function displayRoutes(routesToDisplay) {
            if (!routesToDisplay || routesToDisplay.length === 0) {
                displayNoRoutes();
                return;
            }
            
            resultsContainer.innerHTML = '';
            
            routesToDisplay.forEach(route => {
                const routeCard = document.createElement('div');
                routeCard.className = 'route-card';
                routeCard.setAttribute('data-route-id', route.id_ruta);
                if (route.id_ruta === selectedRouteId) {
                    routeCard.classList.add('selected');
                }
                
                // Obtener conteo de horarios únicos
                const uniqueSchedules = getUniqueSchedules(route.horarios || []);
                const isFavorite = favorites.has(route.id_ruta);
                const favoriteIcon = isFavorite ? 'fas fa-heart' : 'far fa-heart';
                
                routeCard.innerHTML = `
                    <div class="route-card-header">
                        <div class="route-card-title">
                            <i class="fas fa-building"></i>
                            <span class="route-company">${route.empresa_nombre || 'Transporte'}</span>
                        </div>
                        <button class="favorite-btn ${isFavorite ? 'active' : ''}" data-route-id="${route.id_ruta}" title="${isFavorite ? 'Eliminar de favoritas' : 'Agregar a favoritas'}">
                            <i class="${favoriteIcon}"></i>
                        </button>
                    </div>
                    <div class="route-path">
                        <div class="route-origin">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${route.origen}</span>
                        </div>
                        <i class="fas fa-arrow-right arrow"></i>
                        <div class="route-destination">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${route.destino}</span>
                        </div>
                    </div>
                    <div class="route-card-divider"></div>
                    <div class="route-card-footer">
                        <div class="route-schedule">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Horarios disponibles:</span>
                            <span class="route-schedule-count">${uniqueSchedules.length}</span>
                        </div>
                        <button class="btn-details">Ver detalles</button>
                    </div>
                `;
                
                // Agregar evento de clic a la tarjeta de ruta
                routeCard.addEventListener('click', (e) => {
                    // No seleccionar si se hace clic en el botón de favorita
                    if (!e.target.closest('.favorite-btn')) {
                        selectedRouteId = route.id_ruta;
                        updateSelectedRouteCard();
                        showRouteDetails(route);
                    }
                });

                // Botón "Ver detalles"
                const detailsBtn = routeCard.querySelector('.btn-details');
                detailsBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectedRouteId = route.id_ruta;
                    updateSelectedRouteCard();
                    showRouteDetails(route);
                });

                // Agregar escucha de evento del botón de favorita
                const favoriteBtn = routeCard.querySelector('.favorite-btn');
                favoriteBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleFavorite(route.id_ruta, favoriteBtn);
                });
                
                resultsContainer.appendChild(routeCard);
            });
        }

        // Alternar estado de favorita para una ruta
        async function toggleFavorite(routeId, buttonElement) {
            const isFavorite = favorites.has(routeId);
            const action = isFavorite ? 'remove_favorite' : 'add_favorite';
            
            try {
                const payload = {
                    action: action,
                    id_usuario: ID_USUARIO,
                    id_ruta: routeId
                };
                
                const response = await fetch(FAVORITES_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        // Actualizar el estado local
                        if (isFavorite) {
                            favorites.delete(routeId);
                            buttonElement.classList.remove('active');
                            buttonElement.innerHTML = '<i class="far fa-heart"></i>';
                            buttonElement.title = 'Agregar a favoritas';
                            showToast('Eliminado de favoritas');
                        } else {
                            favorites.add(routeId);
                            buttonElement.classList.add('active');
                            buttonElement.innerHTML = '<i class="fas fa-heart"></i>';
                            buttonElement.title = 'Eliminar de favoritas';
                            showToast('Agregado a favoritas');
                        }
                        
                        // Si estamos en vista de favoritas, actualizar lista
                        if (currentFilter === 'favorites') {
                            filterAndDisplayRoutes();
                        }
                    }
                } else {
                    showToast('Error al actualizar favorita');
                    console.error('Error response:', await response.text());
                }
            } catch (error) {
                console.error('Error al cambiar favorita:', error);
                showToast('Error al actualizar favorita');
            }
        }

        // Actualizar estilo de tarjeta de ruta seleccionada
        function updateSelectedRouteCard() {
            document.querySelectorAll('.route-card').forEach(card => {
                card.classList.remove('selected');
                if (parseInt(card.getAttribute('data-route-id')) === selectedRouteId) {
                    card.classList.add('selected');
                }
            });
        }

        // Obtener horarios únicos (evitando duplicados)
        function getUniqueSchedules(schedules) {
            const scheduleMap = {};
            
            schedules.forEach(schedule => {
                const key = `${schedule.dia_semana}-${schedule.hora_salida}-${schedule.hora_llegada}`;
                scheduleMap[key] = schedule;
            });
            
            return Object.values(scheduleMap);
        }

        // Mostrar mensaje "sin rutas"
        function displayNoRoutes() {
            resultsContainer.innerHTML = `
                <div class="no-routes">
                    <p>No se encontraron rutas para esta combinación</p>
                </div>
            `;
        }

        // Mostrar vista sin selección
        function showNoSelection() {
            routeDetailsContainer.innerHTML = `
                <div class="no-selection">
                    <i class="fas fa-route"></i>
                    <h3>Selecciona una ruta</h3>
                    <p>Elige una ruta de la lista para ver los detalles completos</p>
                </div>
            `;
        }

        // Mostrar detalles de ruta en columna derecha
        function showRouteDetails(route) {
            if (!route) {
                showNoSelection();
                return;
            }
            
            const uniqueSchedules = getUniqueSchedules(route.horarios || []);
            const isFavorite = favorites.has(route.id_ruta);
            const favoriteIcon = isFavorite ? 'fas fa-heart' : 'far fa-heart';
            
            // Construir contenido de detalles
            let contentHTML = `
                <div class="route-details">
                    <div class="route-detail-header">
                        <div class="route-detail-title">${route.empresa_nombre || 'Ruta'}</div>
                        <div class="route-full-path">
                            <span>${route.origen}</span>
                            <i class="fas fa-arrow-right"></i>
                            <span>${route.destino}</span>
                        </div>
                    </div>

                    <div class="detail-divider"></div>

                    <div class="detail-card">
                        <h4 class="info-title">Información de la empresa:</h4>
                        <div class="info-rows-grid">
                        
                        <div class="info-row">
                            <i class="fas fa-building"></i>
                            <div class="info-text">
                                <span class="info-label">Nombre:</span>
                                <span class="info-value">${route.empresa_nombre || 'No especificado'}</span>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <i class="fas fa-phone"></i>
                            <div class="info-text">
                                <span class="info-label">Teléfono:</span>
                                <span class="info-value">${route.empresa_telefono || 'No especificado'}</span>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="info-text">
                                <span class="info-label">Dirección:</span>
                                <span class="info-value">${route.empresa_direccion || 'No especificada'}</span>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <i class="fas fa-envelope"></i>
                            <div class="info-text">
                                <span class="info-label">Email:</span>
                                <span class="info-value">${route.empresa_email || 'No especificado'}</span>
                            </div>
                        </div>
                        </div>
                    </div>

                    <div class="detail-divider"></div>

                    <h4 class="info-title" style="margin-bottom:14px;font-size:17px;">Horarios disponibles:</h4>
            `;

            // Agregar horarios
            uniqueSchedules.forEach(schedule => {
                const paradas = Array.isArray(route.paradas) ? route.paradas : ['No especificadas'];
                contentHTML += `
                    <div class="schedule-card">
                        <div class="schedule-header">
                            <span class="schedule-company-name">${route.empresa_nombre || 'Transporte'}</span>
                            <div class="schedule-day">
                                <i class="fas fa-calendar-alt"></i>
                                ${schedule.dia_semana || 'No especificado'}
                            </div>
                        </div>

                        <div class="schedule-route-path">
                            <span>${route.origen}</span>
                            <i class="fas fa-arrow-right"></i>
                            <span>${route.destino}</span>
                        </div>

                        <div class="schedule-times">
                            <div class="time-group departure">
                                <i class="fas fa-map-marker-alt"></i>
                                <div class="time-text">
                                    <span class="time-label">Salida</span>
                                    <span class="time-value">${schedule.hora_salida || '--:--'}</span>
                                </div>
                            </div>
                            <div class="time-group arrival">
                                <i class="fas fa-map-marker-alt"></i>
                                <div class="time-text">
                                    <span class="time-label">Llegada</span>
                                    <span class="time-value">${schedule.hora_llegada || '--:--'}</span>
                                </div>
                            </div>
                        </div>

                        <div class="schedule-card-body">
                            <div class="schedule-details">
                                <div class="detail-row">
                                    <i class="fas fa-redo" style="color:#FFA000;"></i>
                                    <span>Frecuencia: ${schedule.frecuencia || 'No especificada'}</span>
                                </div>
                                <div class="detail-row">
                                    <i class="fas fa-traffic-light" style="color:#7B1FA2;"></i>
                                    <span>Paradas:</span>
                                </div>
                                <ul class="stops-list">
                                    ${paradas.map(stop => `<li>${stop}</li>`).join('')}
                                </ul>
                            </div>

                            <div class="driver-vehicle-info">
                                <div class="driver-vehicle-row">
                                    <i class="fas fa-user" style="color:#1565C0;"></i>
                                    <span><strong>Conductor: ${schedule.conductor_nombre || 'N/A'}</strong></span>
                                </div>
                                <div class="driver-vehicle-row">
                                    <i class="fas fa-bus" style="color:#1565C0;"></i>
                                    <span><strong>Vehículo: ${schedule.vehiculo_modelo || 'N/A'}</strong></span>
                                </div>
                                <div class="driver-vehicle-row">
                                    <i class="fas fa-ticket-alt" style="color:#E65100;"></i>
                                    <span><strong>Placa: ${schedule.vehiculo_placa || 'N/A'}</strong></span>
                                </div>
                                <div class="driver-vehicle-row">
                                    <i class="fas fa-users" style="color:#6A1B9A;"></i>
                                    <span><strong>Capacidad: ${schedule.vehiculo_capacidad || 'N/A'} pasajeros</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            contentHTML += `</div>`; // Close route-details div
            routeDetailsContainer.innerHTML = contentHTML;
        }

        // Mostrar indicador de carga
        function showLoading(show, container = document.body) {
            if (show) {
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'loading';
                loadingDiv.innerHTML = `
                    <div class="spinner"></div>
                    <div class="loading-text">Cargando...</div>
                `;
                
                // Limpiar solo si es el contenedor de resultados
                if (container === resultsContainer) {
                    container.innerHTML = '';
                }
                container.appendChild(loadingDiv);
            } else {
                const loadingElements = container.querySelectorAll('.loading');
                loadingElements.forEach(element => element.remove());
            }
        }

        // Mostrar notificación toast
        function showToast(message, duration = 3000) {
            toastMessage.textContent = message;
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, duration);
        }
    </script>
</body>
</html>
