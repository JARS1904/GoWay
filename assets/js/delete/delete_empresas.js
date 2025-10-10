// delete_empresas.js
// Este script maneja la eliminación de empresas en la página de gestión de empresas
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clic en botones de eliminar
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const rfc_empresa = row.querySelector('td:first-child').getAttribute('data-id') || '';
           // const idVehiculo = this.getAttribute('data-id');

            if (confirm('¿Estás seguro de que deseas eliminar esta empresa? con RFC='+rfc_empresa +' Se eliminarán TODOS sus vehículos, rutas, conductores, checadores y asignaciones relacionadas. Esta acción es irreversible.')) {
                // Crear formulario para enviar la solicitud DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/GoWay/controllers/delete/delete_empresas.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'rfc_empresa';
                input.value = rfc_empresa;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
