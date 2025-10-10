document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar todos los botones de editar
    const editButtons = document.querySelectorAll('.btn-edit');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Obtener la fila padre
            const row = this.closest('tr');
            
            // Obtener los datos de la fila
            const id = row.querySelector('td:nth-child(1)').getAttribute('data-id') || '';
            const nombre = row.querySelector('td:nth-child(2)').textContent;
            const email = row.querySelector('td:nth-child(3)').textContent;
            const password = row.querySelector('td:nth-child(4)').textContent;
            const rol = row.querySelector('td:nth-child(5)').textContent;
            
            // Llenar el formulario de edición
            document.getElementById('edit_id_usuario').value = id;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_password').value = password;
            document.getElementById('edit_rol').value = rol;

            // Mostrar el modal
            document.getElementById('editUserModal').classList.add('active');
        });
    });

    // Cerrar modal de edición
    document.getElementById('closeEditModal').addEventListener('click', function() {
        document.getElementById('editUserModal').classList.remove('active');
    });
    
    document.getElementById('cancelEditModal').addEventListener('click', function() {
        document.getElementById('editUserModal').classList.remove('active');
    });
    
    document.getElementById('editUserModal').addEventListener('click', function(e) {
        if (e.target === this) {
            document.getElementById('editUserModal').classList.remove('active');
        }
    });
});
