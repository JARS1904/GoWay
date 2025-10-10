document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');

            const rfc_checador = row.querySelector('td:first-child').getAttribute('data-id') || '';
            const rfc_empresa = row.querySelector('td:nth-child(2)').textContent;
            const nombre = row.querySelector('td:nth-child(3)').textContent;
            const usuario = row.querySelector('td:nth-child(4)').textContent;
            const password= row.querySelector('td:nth-child(5)').textContent;
            const activo = row.querySelector('td:nth-child(6)').textContent === 'Sí' ? '1' : '0';

            document.getElementById('edit_rfc_checador').value = rfc_checador;
            document.getElementById('edit_rfc_empresa').value = rfc_empresa;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_usuario').value = usuario;
            document.getElementById('edit_password').value = password;
            document.getElementById('edit_activo').value = activo;

            document.getElementById('editChecadoresModal').classList.add('active');
        });
    });

    document.getElementById('closeEditChecadoresModal').addEventListener('click', function() {
        document.getElementById('editChecadoresModal').classList.remove('active');
    });

    document.getElementById('cancelEditChecadoresModal').addEventListener('click', function() {
        document.getElementById('editChecadoresModal').classList.remove('active');
    });

    document.getElementById('editChecadoresModal').addEventListener('click', function(e) {
        if (e.target === this) {
            document.getElementById('editChecadoresModal').classList.remove('active');
        }
    });
});
