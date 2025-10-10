// delete_usuarios.js
// Este script maneja la eliminación de usuarios en la página de gestión de usuarios
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clic en botones de eliminar
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const id = row.querySelector('td:first-child').getAttribute('data-id') || '';
           // const idVehiculo = this.getAttribute('data-id');

            if (confirm('¿Estás seguro de que deseas eliminar este usuario? con ID= '+id )) {
                // Crear formulario para enviar la solicitud DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/GoWay/controllers/delete/delete_usuarios.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id';
                input.value = id;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
