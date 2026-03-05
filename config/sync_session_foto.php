<?php
// Refresca $_SESSION['foto'] desde la DB en cada carga de página admin
if (!empty($_SESSION['id'])) {
    require_once __DIR__ . '/conexion_bd.php';
    $__stmt = $conexion->prepare("SELECT foto FROM usuarios WHERE id = ?");
    $__stmt->bind_param("i", $_SESSION['id']);
    $__stmt->execute();
    $_SESSION['foto'] = $__stmt->get_result()->fetch_assoc()['foto'] ?? null;
    $__stmt->close();
    unset($__stmt);
}
