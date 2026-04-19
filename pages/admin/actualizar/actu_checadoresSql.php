
<?php
header('Content-Type: application/json');
require_once '../../../config/conexion_bd.php';
require_once '../../../controllers/upload_foto.php';

$conn = $conexion;

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

$rfc_checador = $_POST['rfc_checador'];
$rfc_empresa  = $_POST['rfc_empresa'];
$nombre       = $_POST['nombre'];
$usuario      = $_POST['usuario'];
$contrasena   = $_POST['password'];
$activo       = $_POST['activo'];

if (trim($contrasena) === '●●●●●●●●' || trim($contrasena) === 'Sin contraseña') {
    $stmt_pwd = $conn->prepare("SELECT contrasena FROM checadores WHERE rfc_checador = ?");
    $stmt_pwd->bind_param("s", $rfc_checador);
    $stmt_pwd->execute();
    $res_pwd = $stmt_pwd->get_result();
    if ($row_pwd = $res_pwd->fetch_assoc()) {
        $contrasena = $row_pwd['contrasena'];
    }
    $stmt_pwd->close();
}

$nueva_foto = uploadFoto($_FILES['foto'] ?? [], 'checador');

if ($nueva_foto !== null) {
    $stmt = $conn->prepare("UPDATE checadores SET rfc_empresa=?, nombre=?, usuario=?, contrasena=?, activo=?, foto=? WHERE rfc_checador=?");
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("ssssisss", $rfc_empresa, $nombre, $usuario, $contrasena, $activo, $nueva_foto, $rfc_checador);
} else {
    $stmt = $conn->prepare("UPDATE checadores SET rfc_empresa=?, nombre=?, usuario=?, contrasena=?, activo=? WHERE rfc_checador=?");
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("ssssis", $rfc_empresa, $nombre, $usuario, $contrasena, $activo, $rfc_checador);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Checador actualizado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
