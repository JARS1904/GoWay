<?php
// actualizar_ruta.php

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "goway";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Recoger datos del formulario
$id_ruta = $_POST['id_ruta'];
$nombre = $_POST['nombre'];
$origen = $_POST['origen'];
$destino = $_POST['destino'];
$paradas = $_POST['paradas'];
$activo = $_POST['activo'];
$rfc_empresa = $_POST['rfc_empresa'];

// Preparar la consulta SQL
$sql = "UPDATE rutas SET 
        rfc_empresa = ?,
        nombre = ?,
        origen = ?,
        destino = ?,
        paradas = ?,
        activa = ?
        WHERE id_ruta = ?";

// Preparar statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error en la preparación: " . $conn->error);
}

// Vincular parámetros
$stmt->bind_param("sssssii", $rfc_empresa, $nombre, $origen, $destino, $paradas, $activa, $id_ruta);

// Ejecutar consulta
if ($stmt->execute()) {
    echo "Ruta actualizada exitosamente";
    // Redireccionar después de 2 segundos
    header("Refresh: 2; URL=/GoWay/pages/rutas.php");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>
