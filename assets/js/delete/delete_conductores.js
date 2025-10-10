// delete_conductores.js
// Este script maneja la eliminación de conductores en la página de gestión de conductores
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clic en botones de eliminar
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const rfc_conductor = row.querySelector('td:first-child').getAttribute('data-id') || '';
           // const idVehiculo = this.getAttribute('data-id');

            if (confirm('¿Estás seguro de que deseas eliminar este conductor? con RFC='+rfc_conductor +' Se eliminarán todas sus asignaciones relacionadas.')) {
                // Crear formulario para enviar la solicitud DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/GoWay/controllers/delete/delete_conductores.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'rfc_conductor';
                input.value = rfc_conductor;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
