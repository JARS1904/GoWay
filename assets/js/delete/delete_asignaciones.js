// delete_asignaciones.js
// Este script maneja la eliminación de asignaciones en la página de gestión de asignaciones
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clic en botones de eliminar
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const id_asignacion = row.querySelector('td:first-child').getAttribute('data-id') || '';
           // const idVehiculo = this.getAttribute('data-id');

            if (confirm('¿Estás seguro de que deseas eliminar esta asignación? con ID='+id_asignacion )) {
                // Crear formulario para enviar la solicitud DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/GoWay/controllers/delete/delete_asignaciones.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id_asignacion';
                input.value = id_asignacion;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
