// delete_vehiculos.js
// Este script maneja la eliminación de vehículos en la página de gestión de vehículos
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clic en botones de eliminar
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const id_vehiculo = row.querySelector('td:first-child').getAttribute('data-id') || '';
           // const idVehiculo = this.getAttribute('data-id');

            if (confirm('¿Estás seguro de que deseas eliminar este vehículo? con ID='+id_vehiculo +' Se eliminarán todas sus asignaciones.')) {
                // Crear formulario para enviar la solicitud DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../controllers/delete/delete_vehiculo.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id_vehiculo';
                input.value = id_vehiculo;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
