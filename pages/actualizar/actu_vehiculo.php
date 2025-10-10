<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Actualizar Vehículo</title>
</head>
<body>
    
            <!-- Contenido Principal -->
        <main class="main-content">
            <header class="header">
                <h2>Editar Vehículo</h2>
                <div class="user-info">
                    <span>Admin</span>
                    <img src="../assets/images/icons/administrador.png" alt="Usuario">
                </div>
            </header>

            <section class="content">
                <?php
                // Obtener el ID del vehículo de la URL
                $id_vehiculo = isset($_GET['id']) ? $_GET['id'] : null;
                
                if ($id_vehiculo) {
                    // Conexión a la base de datos
                    $conn = new mysqli("localhost", "root", "", "goway");
                    
                    if ($conn->connect_error) {
                        die("Error de conexión: " . $conn->connect_error);
                    }
                    
                    // Consulta para obtener los datos del vehículo
                    $sql = "SELECT * FROM vehiculos WHERE id_vehiculo = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id_vehiculo);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $vehiculo = $result->fetch_assoc();
                ?>
                <form id="editVehicleForm" action="controllers/update/actu_vehiculos.php" method="POST" class="form-container">
                    <input type="hidden" id="edit_id_vehiculo" name="id_vehiculo" value="<?php echo $vehiculo['id_vehiculo']; ?>">
                    
                    <div class="form-columns">
                        <!-- Columna izquierda -->
                        <div class="form-column">
                            <div class="form-group">
                                <label for="edit_placa">Placa</label>
                                <input type="text" id="edit_placa" name="placa" value="<?php echo htmlspecialchars($vehiculo['placa']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_modelo">Modelo</label>
                                <input type="text" id="edit_modelo" name="modelo" value="<?php echo htmlspecialchars($vehiculo['modelo']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_capacidad">Capacidad</label>
                                <input type="number" id="edit_capacidad" name="capacidad" value="<?php echo htmlspecialchars($vehiculo['capacidad']); ?>" required>
                            </div>
                        </div>
                        
                        <!-- Columna derecha -->
                        <div class="form-column">
                            <div class="form-group">
                                <label for="edit_activo">Activo</label>
                                <select id="edit_activo" name="activo">
                                    <option value="1" <?php echo $vehiculo['activo'] ? 'selected' : ''; ?>>Sí</option>
                                    <option value="0" <?php echo !$vehiculo['activo'] ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_rfc_empresa">RFC Empresa</label>
                                <select id="edit_rfc_empresa" name="rfc_empresa" required>
                                    <option disabled>Seleccione Empresa</option>
                                    <?php
                                    $empresas = $conn->query("SELECT rfc_empresa, nombre FROM empresas");
                                    while ($empresa = $empresas->fetch_assoc()) {
                                        $selected = ($empresa['rfc_empresa'] == $vehiculo['rfc_empresa']) ? 'selected' : '';
                                        echo "<option value='{$empresa['rfc_empresa']}' $selected>{$empresa['nombre']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="vehiculos.php" class="btn btn-cancel">Cancelar</a>
                        <button type="submit" class="btn btn-save">Guardar cambios</button>
                    </div>
                </form>
                <?php
                    } else {
                        echo "<p>No se encontró el vehículo solicitado.</p>";
                    }
                    
                    $conn->close();
                } else {
                    echo "<p>No se ha especificado un vehículo para editar.</p>";
                }
                ?>
            </section>
        </main>


</body>
</html>