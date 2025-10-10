<?php
if (!isset($_POST['rfc_empresa'])) {
    die("RFC de empresa no proporcionado.");
}

$rfc = $_POST['rfc_empresa'];

$conn = new mysqli("localhost", "root", "", "goway");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // 1. Eliminar asignaciones relacionadas
    $sql1 = "DELETE FROM asignaciones WHERE rfc_empresa = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("s", $rfc);
    $stmt1->execute();
    
    // 2. Eliminar checadores relacionados
    $sql2 = "DELETE FROM checadores WHERE rfc_empresa = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $rfc);
    $stmt2->execute();
    
    // 3. Eliminar conductores relacionados
    $sql3 = "DELETE FROM conductores WHERE rfc_empresa = ?";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("s", $rfc);
    $stmt3->execute();
    
    // 4. Eliminar horarios relacionados (a través de rutas)
    // Primero obtenemos los IDs de rutas de esta empresa
    $sql4a = "SELECT id_ruta FROM rutas WHERE rfc_empresa = ?";
    $stmt4a = $conn->prepare($sql4a);
    $stmt4a->bind_param("s", $rfc);
    $stmt4a->execute();
    $result = $stmt4a->get_result();
    $rutas_ids = [];
    while ($row = $result->fetch_assoc()) {
        $rutas_ids[] = $row['id_ruta'];
    }
    $stmt4a->close();
    
    if (!empty($rutas_ids)) {
        $placeholders = implode(',', array_fill(0, count($rutas_ids), '?'));
        $sql4b = "DELETE FROM horarios WHERE id_ruta IN ($placeholders)";
        $stmt4b = $conn->prepare($sql4b);
        $types = str_repeat('i', count($rutas_ids));
        $stmt4b->bind_param($types, ...$rutas_ids);
        $stmt4b->execute();
        $stmt4b->close();
    }
    
    // 5. Eliminar rutas de la empresa
    $sql5 = "DELETE FROM rutas WHERE rfc_empresa = ?";
    $stmt5 = $conn->prepare($sql5);
    $stmt5->bind_param("s", $rfc);
    $stmt5->execute();
    
    // 6. Eliminar vehículos de la empresa
    $sql6 = "DELETE FROM vehiculos WHERE rfc_empresa = ?";
    $stmt6 = $conn->prepare($sql6);
    $stmt6->bind_param("s", $rfc);
    $stmt6->execute();
    
    // 7. Finalmente, eliminar la empresa
    $sql7 = "DELETE FROM empresas WHERE rfc_empresa = ?";
    $stmt7 = $conn->prepare($sql7);
    $stmt7->bind_param("s", $rfc);
    $stmt7->execute();
    
    // Confirmar transacción
    $conn->commit();
    
    header("Location: /GoWay/pages/empresas.php");
    echo "Empresa eliminada correctamente.";
    exit();
} catch (Exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    echo "Error al eliminar la empresa: " . $e->getMessage();
}

// Cerrar conexiones
if (isset($stmt1)) $stmt1->close();
if (isset($stmt2)) $stmt2->close();
if (isset($stmt3)) $stmt3->close();
if (isset($stmt5)) $stmt5->close();
if (isset($stmt6)) $stmt6->close();
if (isset($stmt7)) $stmt7->close();
$conn->close();
?>