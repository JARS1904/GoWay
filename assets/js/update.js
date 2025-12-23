// Mostrar modal de edición y cargar datos
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar todos los botones de editar
    const editButtons = document.querySelectorAll('.btn-edit');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Obtener la fila padre
            const row = this.closest('tr');
            
            // Obtener los datos de la fila
            const id_ruta = row.querySelector('td:first-child').getAttribute('data-id') || '';
            const nombre = row.querySelector('td:nth-child(1)').textContent;
            const origen = row.querySelector('td:nth-child(2)').textContent;
            const destino = row.querySelector('td:nth-child(3)').textContent;
            const paradas = row.querySelector('td:nth-child(4)').textContent;
            const activa = row.querySelector('td:nth-child(5)').textContent === 'Sí' ? '1' : '0';
            const rfc_empresa = row.querySelector('td:nth-child(6)').textContent;
            
            // Llenar el formulario de edición
            document.getElementById('edit_id_ruta').value = id_ruta;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_origen').value = origen;
            document.getElementById('edit_destino').value = destino;
            document.getElementById('edit_paradas').value = paradas;
            document.getElementById('edit_activa').value = activa;
            document.getElementById('edit_rfc_empresa').value = rfc_empresa;
            
            // Mostrar el modal
            document.getElementById('editRouteModal').classList.add('active');
        });
    });
    
    // Cerrar modal de edición
    document.getElementById('closeEditModal').addEventListener('click', function() {
        document.getElementById('editRouteModal').classList.remove('active');
    });
    
    document.getElementById('cancelEditModal').addEventListener('click', function() {
        document.getElementById('editRouteModal').classList.remove('active');
    });
    
    document.getElementById('editRouteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            document.getElementById('editRouteModal').classList.remove('active');
        }
    });

    // Manejar envío del formulario de edición con AJAX
    const editRouteForm = document.getElementById('editRouteForm');
    if (editRouteForm) {
        editRouteForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Deshabilitar botón submit
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';

            // Enviar con AJAX
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;

                if (data.success) {
                    // Cerrar modal
                    document.getElementById('editRouteModal').classList.remove('active');
                    
                    // Mostrar notificación
                    if (typeof showNotification === 'function') {
                        showNotification(data.message || 'Ruta actualizada exitosamente', 'success');
                    }

                    // Recargar tabla después de 3 segundos
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                } else {
                    // Error
                    if (typeof showNotification === 'function') {
                        showNotification(data.message || 'Error al actualizar la ruta', 'error');
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                console.error('Error:', error);
                if (typeof showNotification === 'function') {
                    showNotification('Error de conexión al actualizar', 'error');
                } else {
                    alert('Error de conexión: ' + error.message);
                }
            });
        });
    }
});
