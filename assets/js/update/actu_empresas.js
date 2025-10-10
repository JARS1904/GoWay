document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');

            const rfc_empresa = row.querySelector('td:first-child').getAttribute('data-id') || '';
            const nombre_empresa = row.querySelector('td:nth-child(2)').textContent;
            const direccion_empresa = row.querySelector('td:nth-child(3)').textContent;
            const telefono = row.querySelector('td:nth-child(4)').textContent;
            const email_empresa = row.querySelector('td:nth-child(5)').textContent;
            const activo = row.querySelector('td:nth-child(6)').textContent === 'Sí' ? '1' : '0';

            document.getElementById('edit_rfc_empresa').value = rfc_empresa;
            document.getElementById('edit_nombre_empresa').value = nombre_empresa;
            document.getElementById('edit_direccion_empresa').value = direccion_empresa;
            document.getElementById('edit_telefono').value = telefono;
            document.getElementById('edit_email_empresa').value = email_empresa;
            document.getElementById('edit_activo').value = activo;

            document.getElementById('editEmpresasModal').classList.add('active');
        });
    });

    document.getElementById('closeEditEmpresasModal').addEventListener('click', function() {
        document.getElementById('editEmpresasModal').classList.remove('active');
    });

    document.getElementById('cancelEditEmpresasModal').addEventListener('click', function() {
        document.getElementById('editEmpresasModal').classList.remove('active');
    });

    document.getElementById('editEmpresasModal').addEventListener('click', function(e) {
        if (e.target === this) {
            document.getElementById('editEmpresasModal').classList.remove('active');
        }
    });
});
