// delete_rutas.js
// Este script maneja la eliminación de rutas en la página de gestión de rutas
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clic en botones de eliminar
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const id_ruta = row.querySelector('td:first-child').getAttribute('data-id') || '';
           // const idVehiculo = this.getAttribute('data-id');

            if (confirm('¿Estás seguro de que deseas eliminar esta ruta? con ID='+id_ruta +' Se eliminarán todos sus horarios y asignaciones relacionadas.')) {
                // Crear formulario para enviar la solicitud DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/GoWay/controllers/eliminar_ruta.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id_ruta';
                input.value = id_ruta;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
