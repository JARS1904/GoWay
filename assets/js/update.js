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
});
