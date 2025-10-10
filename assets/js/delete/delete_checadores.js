// delete_checadores.js
// Este script maneja la eliminación de checadores en la página de gestión de checadores
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clic en botones de eliminar
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const rfc_checador = row.querySelector('td:first-child').getAttribute('data-id') || '';
           // const idVehiculo = this.getAttribute('data-id');

            if (confirm('¿Estás seguro de que deseas eliminar este checador? con RFC='+rfc_checador )) {
                // Crear formulario para enviar la solicitud DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/GoWay/controllers/delete/delete_checadores.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'rfc_checador';
                input.value = rfc_checador;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
