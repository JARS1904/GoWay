// delete_empresas.js
// Este script maneja la eliminación de empresas en la página de gestión de empresas
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const rfc_empresa = row.querySelector('td:first-child').getAttribute('data-id') || '';

            if (confirm('¿Estás seguro de que deseas eliminar esta empresa con RFC=' + rfc_empresa + '? Se eliminarán TODOS sus vehículos, rutas, conductores, checadores y asignaciones relacionadas. Esta acción es irreversible.')) {
                const formData = new FormData();
                formData.append('rfc_empresa', rfc_empresa);

                fetch('/GoWay/controllers/delete/delete_empresas.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Empresa eliminada correctamente.');
                        window.location.reload();
                    } else {
                        alert('Error al eliminar: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error de red: ' + error.message);
                });
            }
        });
    });
});
