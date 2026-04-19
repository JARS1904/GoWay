
<?php
session_start();
header('Content-Type: application/json');
require_once '../../../config/conexion_bd.php';
require_once '../../../controllers/upload_foto.php';

$conn = $conexion;

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

$id       = $_POST['id_usuario'];
$nombre   = $_POST['nombre'];
$email    = $_POST['email'];
$password = $_POST['password'];
$rol      = $_POST['rol'];

if (!empty($password) && trim($password) !== '●●●●●●●●' && trim($password) !== 'Sin contraseña') {
    $password = password_hash($password, PASSWORD_DEFAULT);
} else {
    // Keep existing password
    $row = $conn->query("SELECT password FROM usuarios WHERE id = " . (int)$id)->fetch_assoc();
    $password = $row['password'];
}

$nueva_foto = uploadFoto($_FILES['foto'] ?? [], 'usuario');

if ($nueva_foto !== null) {
    $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, email=?, password=?, rol=?, foto=? WHERE id=?");
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("sssssi", $nombre, $email, $password, $rol, $nueva_foto, $id);
} else {
    $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, email=?, password=?, rol=? WHERE id=?");
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("ssssi", $nombre, $email, $password, $rol, $id);
}

if ($stmt->execute()) {
    // Si el admin actualizó su propio perfil, refrescar la foto en sesión
    if (isset($_SESSION['id']) && (int)$id === (int)$_SESSION['id'] && $nueva_foto !== null) {
        $_SESSION['foto'] = $nueva_foto;
    }
    echo json_encode(["success" => true, "message" => "Usuario actualizado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error en la actualización: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

