document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');

            const rfc_conductor = row.querySelector('td:first-child').getAttribute('data-id') || '';
            const rfc_empresa = row.querySelector('td:nth-child(2)').textContent;
            const nombre = row.querySelector('td:nth-child(3)').textContent;
            const licencia = row.querySelector('td:nth-child(4)').textContent;
            const telefono = row.querySelector('td:nth-child(5)').textContent;
            const activo = row.querySelector('td:nth-child(6)').textContent === 'Sí' ? '1' : '0';
            

            document.getElementById('edit_rfc_conductor').value = rfc_conductor;
            document.getElementById('edit_rfc_empresa').value = rfc_empresa;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_licencia').value = licencia;
            document.getElementById('edit_activo').value = activo;
            document.getElementById('edit_telefono').value = telefono;

            document.getElementById('editConductoresModal').classList.add('active');
        });
    });

    document.getElementById('closeEditConductoresModal').addEventListener('click', function() {
        document.getElementById('editConductoresModal').classList.remove('active');
    });

    document.getElementById('cancelEditConductoresModal').addEventListener('click', function() {
        document.getElementById('editConductoresModal').classList.remove('active');
    });

    document.getElementById('editConductoresModal').addEventListener('click', function(e) {
        if (e.target === this) {
            document.getElementById('editConductoresModal').classList.remove('active');
        }
    });
});
