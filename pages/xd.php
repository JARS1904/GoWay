<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "goway");

// Consulta para obtener empresas existentes
$empresas = $conn->query("SELECT rfc_empresa, nombre FROM empresas");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro Completo GoWay</title>
    <style>
        .form-section { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; }
        .dynamic-select { width: 100%; padding: 8px; margin-top: 5px; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Sistema GoWay - Registro Completo</h1>
    
    <form action="procesar.php" method="post">
        <!-- Sección Empresa -->
        <div class="form-section">
            <h2>Empresa</h2>
            <label>
                Seleccionar empresa existente:
                <select name="rfc_empresa_existente" class="dynamic-select" id="select-empresa">
                    <option value="">-- Seleccione --</option>
                    <?php while($empresa = $empresas->fetch_assoc()): ?>
                        <option value="<?= $empresa['rfc_empresa'] ?>"><?= $empresa['nombre'] ?></option>
                    <?php endwhile; ?>
                    <option value="new">+ Nueva empresa</option>
                </select>
            </label>
            
            <div id="nueva-empresa" style="display:none;">
                <h3>Datos de nueva empresa</h3>
                <label>RFC: <input type="text" name="rfc_empresa_nueva" pattern="[A-Z0-9]{12,13}"></label>
                <label>Nombre: <input type="text" name="nombre_empresa_nueva"></label>
                <label>Dirección: <input type="text" name="direccion_empresa_nueva"></label>
            </div>
        </div>

        <!-- Sección Ruta -->
        <div class="form-section">
            <h2>Ruta</h2>
            <label>Nombre: <input type="text" name="nombre_ruta" required></label>
            <label>Origen: <input type="text" name="origen_ruta" required></label>
            <label>Destino: <input type="text" name="destino_ruta" required></label>
        </div>

        <!-- Sección Vehículo -->
        <div class="form-section">
            <h2>Vehículo</h2>
            <label>Placa: <input type="text" name="placa_vehiculo" required></label>
            <label>Modelo: <input type="text" name="modelo_vehiculo"></label>
        </div>

        <!-- Sección Conductor -->
        <div class="form-section">
            <h2>Conductor</h2>
            <label>RFC: <input type="text" name="rfc_conductor" required pattern="[A-Z0-9]{12,13}"></label>
            <label>Nombre: <input type="text" name="nombre_conductor" required></label>
        </div>

        <button type="submit">Guardar</button>
    </form>

    <script>
    $(document).ready(function() {
        $('#select-empresa').change(function() {
            if($(this).val() == 'new') {
                $('#nueva-empresa').show();
                $('#nueva-empresa input').prop('required', true);
            } else {
                $('#nueva-empresa').hide();
                $('#nueva-empresa input').prop('required', false);
            }
        });
    });
    </script>
</body>
</html>