<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
require_once '../../config/conexion_bd.php';

if (!isset($_POST['rfc_empresa'])) {
    echo json_encode(["success" => false, "message" => "RFC de empresa no proporcionado."]);
    exit();
}

$rfc = $_POST['rfc_empresa'];
$conn = $conexion;

$conn->begin_transaction();

try {
    // 1. Obtener IDs de rutas y vehículos de esta empresa (los necesitamos para limpiar dependencias)
    $stmt_r = $conn->prepare("SELECT id_ruta FROM rutas WHERE rfc_empresa = ?");
    $stmt_r->bind_param("s", $rfc);
    $stmt_r->execute();
    $rutas_ids = array_column($stmt_r->get_result()->fetch_all(MYSQLI_ASSOC), 'id_ruta');
    $stmt_r->close();

    $stmt_v = $conn->prepare("SELECT id_vehiculo FROM vehiculos WHERE rfc_empresa = ?");
    $stmt_v->bind_param("s", $rfc);
    $stmt_v->execute();
    $vehiculos_ids = array_column($stmt_v->get_result()->fetch_all(MYSQLI_ASSOC), 'id_vehiculo');
    $stmt_v->close();

    $stmt_c = $conn->prepare("SELECT rfc_conductor FROM conductores WHERE rfc_empresa = ?");
    $stmt_c->bind_param("s", $rfc);
    $stmt_c->execute();
    $conductores_rfcs = array_column($stmt_c->get_result()->fetch_all(MYSQLI_ASSOC), 'rfc_conductor');
    $stmt_c->close();

    // 2. Limpiar id_ruta_retorno que apunten a rutas de esta empresa (si existe la columna)
    if (!empty($rutas_ids)) {
        $ph = implode(',', array_fill(0, count($rutas_ids), '?'));
        $types = str_repeat('i', count($rutas_ids));

        $stmt_nr = $conn->prepare("UPDATE rutas SET id_ruta_retorno = NULL WHERE id_ruta_retorno IN ($ph)");
        if ($stmt_nr) {
            $stmt_nr->bind_param($types, ...$rutas_ids);
            $stmt_nr->execute();
            $stmt_nr->close();
        }

        // También NULL las propias rutas de esta empresa
        $stmt_nr2 = $conn->prepare("UPDATE rutas SET id_ruta_retorno = NULL WHERE id_ruta IN ($ph)");
        if ($stmt_nr2) {
            $stmt_nr2->bind_param($types, ...$rutas_ids);
            $stmt_nr2->execute();
            $stmt_nr2->close();
        }

        // 3. Eliminar reportes que referencien rutas de esta empresa
        $stmt_rep1 = $conn->prepare("DELETE FROM reportes WHERE id_ruta IN ($ph)");
        $stmt_rep1->bind_param($types, ...$rutas_ids);
        $stmt_rep1->execute();
        $stmt_rep1->close();

        // 4. Eliminar rutas_favoritas que referencien rutas de esta empresa
        $stmt_fav = $conn->prepare("DELETE FROM rutas_favoritas WHERE id_ruta IN ($ph)");
        $stmt_fav->bind_param($types, ...$rutas_ids);
        $stmt_fav->execute();
        $stmt_fav->close();
    }

    // 5. Eliminar reportes que referencien vehículos de esta empresa
    if (!empty($vehiculos_ids)) {
        $ph_v = implode(',', array_fill(0, count($vehiculos_ids), '?'));
        $types_v = str_repeat('i', count($vehiculos_ids));
        $stmt_rep2 = $conn->prepare("DELETE FROM reportes WHERE id_vehiculo IN ($ph_v)");
        $stmt_rep2->bind_param($types_v, ...$vehiculos_ids);
        $stmt_rep2->execute();
        $stmt_rep2->close();
    }

    // 6. Eliminar reportes que referencien conductores de esta empresa
    if (!empty($conductores_rfcs)) {
        $ph_c = implode(',', array_fill(0, count($conductores_rfcs), '?'));
        $types_c = str_repeat('s', count($conductores_rfcs));
        $stmt_rep3 = $conn->prepare("DELETE FROM reportes WHERE rfc_conductor IN ($ph_c)");
        $stmt_rep3->bind_param($types_c, ...$conductores_rfcs);
        $stmt_rep3->execute();
        $stmt_rep3->close();
    }

    // 7. Eliminar asignaciones (por rfc_empresa y también por conductor/vehículo/ruta para cubrir inconsistencias)
    $stmt1 = $conn->prepare("DELETE FROM asignaciones WHERE rfc_empresa = ?");
    $stmt1->bind_param("s", $rfc);
    $stmt1->execute();
    $stmt1->close();

    if (!empty($conductores_rfcs)) {
        $ph_c2 = implode(',', array_fill(0, count($conductores_rfcs), '?'));
        $types_c2 = str_repeat('s', count($conductores_rfcs));
        $stmt1b = $conn->prepare("DELETE FROM asignaciones WHERE rfc_conductor IN ($ph_c2)");
        $stmt1b->bind_param($types_c2, ...$conductores_rfcs);
        $stmt1b->execute();
        $stmt1b->close();
    }

    if (!empty($vehiculos_ids)) {
        $ph_v2 = implode(',', array_fill(0, count($vehiculos_ids), '?'));
        $types_v2 = str_repeat('i', count($vehiculos_ids));
        $stmt1c = $conn->prepare("DELETE FROM asignaciones WHERE id_vehiculo IN ($ph_v2)");
        $stmt1c->bind_param($types_v2, ...$vehiculos_ids);
        $stmt1c->execute();
        $stmt1c->close();
    }

    if (!empty($rutas_ids)) {
        $ph_r2 = implode(',', array_fill(0, count($rutas_ids), '?'));
        $types_r2 = str_repeat('i', count($rutas_ids));
        $stmt1d = $conn->prepare("DELETE FROM asignaciones WHERE id_ruta IN ($ph_r2)");
        $stmt1d->bind_param($types_r2, ...$rutas_ids);
        $stmt1d->execute();
        $stmt1d->close();
    }

    // 8. Eliminar checadores
    $stmt2 = $conn->prepare("DELETE FROM checadores WHERE rfc_empresa = ?");
    $stmt2->bind_param("s", $rfc);
    $stmt2->execute();
    $stmt2->close();

    // 9. Eliminar conductores
    $stmt3 = $conn->prepare("DELETE FROM conductores WHERE rfc_empresa = ?");
    $stmt3->bind_param("s", $rfc);
    $stmt3->execute();
    $stmt3->close();

    // 10. Eliminar horarios de las rutas de esta empresa
    if (!empty($rutas_ids)) {
        $ph = implode(',', array_fill(0, count($rutas_ids), '?'));
        $types = str_repeat('i', count($rutas_ids));
        $stmt4 = $conn->prepare("DELETE FROM horarios WHERE id_ruta IN ($ph)");
        $stmt4->bind_param($types, ...$rutas_ids);
        $stmt4->execute();
        $stmt4->close();
    }

    // 11. Eliminar rutas
    $stmt5 = $conn->prepare("DELETE FROM rutas WHERE rfc_empresa = ?");
    $stmt5->bind_param("s", $rfc);
    $stmt5->execute();
    $stmt5->close();

    // 12. Eliminar vehículos
    $stmt6 = $conn->prepare("DELETE FROM vehiculos WHERE rfc_empresa = ?");
    $stmt6->bind_param("s", $rfc);
    $stmt6->execute();
    $stmt6->close();

    // 13. Eliminar la empresa
    $stmt7 = $conn->prepare("DELETE FROM empresas WHERE rfc_empresa = ?");
    $stmt7->bind_param("s", $rfc);
    $stmt7->execute();
    $stmt7->close();

    $conn->commit();
    echo json_encode(["success" => true, "message" => "Empresa eliminada correctamente."]);

} catch (\Throwable $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Error al eliminar: " . $e->getMessage()]);
}

$conn->close();
