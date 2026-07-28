<?php
/**
 * auth_admin.php — Middleware de autenticación y autorización para páginas administrativas (admin/*.php)
 * Evita que usuarios normales (rol 2/3) accedan al panel de administración (IDOR / Escalada vertical).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id'])) {
    header('Location: ../../index.php');
    exit();
}

// 2. Control de Acceso Estricto (RBAC):
// Solo Superadmin (rol == 1) y Empresa (rol == 4) tienen permiso para entrar a pages/admin/*
$user_rol = (int)($_SESSION['rol'] ?? 0);
if ($user_rol !== 1 && $user_rol !== 4) {
    // Si un pasajero o usuario normal intenta entrar por URL al panel admin, es bloqueado y redirigido.
    header('Location: ../../index.php');
    exit();
}

// 3. Helper centralizado para filtros Multi-Tenant (Empresa vs Superadmin)
$is_superadmin = ($user_rol === 1);
$is_empresa    = ($user_rol === 4);
$rfc_empresa_session = trim($_SESSION['rfc_empresa'] ?? '');

if ($is_empresa && empty($rfc_empresa_session)) {
    // Si tiene rol 4 pero no tiene rfc_empresa en la sesión, denegar o cerrar sesión para evitar fugas de datos
    header('Location: ../../pages/admin/logout.php');
    exit();
}

// Generación segura del filtro WHERE para consultas en el panel administrativo
$rfc_sanitizado = addslashes($rfc_empresa_session);
$where_emp      = $is_empresa ? " WHERE rfc_empresa = '$rfc_sanitizado'" : "";
$where_emp_and  = $is_empresa ? " AND rfc_empresa = '$rfc_sanitizado'" : "";
$where_emp_v    = $is_empresa ? " WHERE v.rfc_empresa = '$rfc_sanitizado'" : "";
$where_emp_r    = $is_empresa ? " WHERE r.rfc_empresa = '$rfc_sanitizado'" : "";
?>
