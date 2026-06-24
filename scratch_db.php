<?php
require 'config/conexion_bd.php';
$res = $conexion->query('DESCRIBE paradas_ruta');
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}
?>
