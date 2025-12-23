/* 
document.querySelectorAll('.btn-edit').forEach(button => {
  button.addEventListener('click', function () {
    const row = this.closest('tr');
    const cells = row.querySelectorAll('td');

    document.getElementById('edit-id').value = this.dataset.id;
    document.getElementById('edit-placa').value = cells[0].textContent;
    document.getElementById('edit-modelo').value = cells[1].textContent;
    document.getElementById('edit-capacidad').value = cells[2].textContent;
    document.getElementById('edit-rfc').value = cells[3].textContent;
    document.getElementById('edit_activa').value=cells[4].textContent === 'Sí' ? '1' : '0';
    //const activa = row.querySelector('td:nth-child(5)').textContent === 'Sí' ? '1' : '0';

    document.getElementById('editVehicleModal').classList.add('active');
  });
});

 // Cerrar modal de edición
    document.getElementById('closeEditModal').addEventListener('click', function() {
        document.getElementById('editVehicleModal').classList.remove('active');
    });
    
    document.getElementById('cancelEditModal').addEventListener('click', function() {
        document.getElementById('editVehicleModal').classList.remove('active');
    });
    
    document.getElementById('editVehicleModal').addEventListener('click', function(e) {
        if (e.target === this) {
            document.getElementById('editVehicleModal').classList.remove('active');
        }
    }); */


document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');

            const id_vehiculo = row.querySelector('td:first-child').getAttribute('data-id') || '';
            const placa = row.querySelector('td:nth-child(1)').textContent;
            const modelo = row.querySelector('td:nth-child(2)').textContent;
            const capacidad = row.querySelector('td:nth-child(3)').textContent;
            const rfc_empresa = row.querySelector('td:nth-child(4)').textContent;
            const activo = row.querySelector('td:nth-child(5)').textContent === 'Sí' ? '1' : '0';

            document.getElementById('edit_id_vehiculo').value = id_vehiculo;
            document.getElementById('edit_placa').value = placa;
            document.getElementById('edit_modelo').value = modelo;
            document.getElementById('edit_capacidad').value = capacidad;
            document.getElementById('edit_activo').value = activo;
            document.getElementById('edit_rfc_empresa').value = rfc_empresa;

            document.getElementById('editVehicleModal').classList.add('active');
        });
    });

    document.getElementById('closeEditVehicleModal').addEventListener('click', function() {
        document.getElementById('editVehicleModal').classList.remove('active');
    });

    document.getElementById('cancelEditVehicleModal').addEventListener('click', function() {
        document.getElementById('editVehicleModal').classList.remove('active');
    });

    document.getElementById('editVehicleModal').addEventListener('click', function(e) {
        if (e.target === this) {
            document.getElementById('editVehicleModal').classList.remove('active');
        }
    });
});





