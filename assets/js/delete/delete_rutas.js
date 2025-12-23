// delete_rutas.js
// Este script maneja la eliminación de rutas en la página de gestión de rutas
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clic en botones de eliminar
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const id_ruta = row.querySelector('td:first-child').getAttribute('data-id') || '';

            if (confirm('¿Estás seguro de que deseas eliminar esta ruta? Se eliminarán todos sus horarios y asignaciones relacionadas.')) {
                // Deshabilitar botón y mostrar carga
                const deleteBtn = this;
                deleteBtn.disabled = true;
                deleteBtn.style.opacity = '0.5';
                const originalText = deleteBtn.textContent;
                deleteBtn.textContent = 'Eliminando...';

                // Crear FormData para enviar con AJAX
                const formData = new FormData();
                formData.append('id_ruta', id_ruta);

                // Enviar con AJAX en lugar de form.submit()
                fetch('/GoWay/controllers/eliminar_ruta.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    deleteBtn.disabled = false;
                    deleteBtn.textContent = originalText;

                    if (data.success) {
                        // Mostrar notificación en ROJO para eliminación
                        if (typeof showNotification === 'function') {
                            showNotification(data.message || 'Ruta eliminada exitosamente', 'error');
                        }

                        // Recargar la página después de 3 segundos
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    } else {
                        if (typeof showNotification === 'function') {
                            showNotification(data.message || 'Error al eliminar la ruta', 'error');
                        }
                    }
                })
                .catch(error => {
                    deleteBtn.disabled = false;
                    deleteBtn.textContent = originalText;
                    console.error('Error:', error);
                    if (typeof showNotification === 'function') {
                        showNotification('Error de conexión al eliminar', 'error');
                    }
                });
            }
        });
    });
});
