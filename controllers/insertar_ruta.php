<?php
// insertar_ruta.php

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
$nombre = $_POST['nombre'];
$origen = $_POST['origen'];
$destino = $_POST['destino'];
$paradas = $_POST['paradas'];
$activa = $_POST['activa'];

// IMPORTANTE: Debes obtener el RFC de la empresa de alguna manera
// Esto puede venir de un campo oculto en el formulario o de la sesión
$rfc_empresa = $_POST['rfc_empresa']; // Ejemplo - debes reemplazar esto

// Preparar la consulta SQL (añadiendo rfc_empresa)
$sql = "INSERT INTO rutas (rfc_empresa, nombre, origen, destino, paradas)
        VALUES (?, ?, ?, ?, ?)";

// Preparar statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error en la preparación: " . $conn->error);
}

// Vincular parámetros (6 parámetros: s=string, i=integer)
$stmt->bind_param("sssss", $rfc_empresa, $nombre, $origen, $destino, $paradas);

// Ejecutar consulta
if ($stmt->execute()) {
    echo "Nueva ruta creada exitosamente";
    // Redireccionar después de 2 segundos
    header("Refresh: 2; URL=/GoWay/pages/horarios.php");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar conexiones
$stmt->close();
$conn->close();
?>