document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.btn-edit');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const card = this.closest('.card');

            const id_horario = card.querySelector('.card-header h3').textContent.split('#')[1];
            const id_ruta = card.querySelector('.route-id').textContent.split(': ')[1];
            const dia = card.querySelector('.schedule-info p:nth-child(1)').textContent.split(': ')[1];
            const salida = card.querySelector('.schedule-info p:nth-child(2)').textContent.split(': ')[1];
            const llegada = card.querySelector('.schedule-info p:nth-child(3)').textContent.split(': ')[1];
            const frecuencia = card.querySelector('.frequency-info p').textContent.split(': ')[1];

            // Llenar los campos del formulario de edición
            document.getElementById('edit_id_horario').value = id_horario;
            document.getElementById('edit_id_ruta').value = id_ruta;
            document.getElementById('edit_dia_semana').value = dia;
            document.getElementById('edit_hora_salida').value = salida;
            document.getElementById('edit_hora_llegada').value = llegada;
            document.getElementById('edit_frecuencia').value = frecuencia;

            // Seleccionar la opción correcta en el select de rutas
            const selectRuta = document.getElementById('edit_id_ruta');
            for (let option of selectRuta.options) {
                option.selected = option.value === id_ruta;
            }

            // Mostrar el modal
            document.getElementById('editRouteModal').classList.add('active');
        });
    });

    // Cerrar modal
    document.getElementById('closeEditModal').addEventListener('click', function () {
        document.getElementById('editRouteModal').classList.remove('active');
    });

    document.getElementById('cancelEditModal').addEventListener('click', function () {
        document.getElementById('editRouteModal').classList.remove('active');
    });

    document.getElementById('editRouteModal').addEventListener('click', function (e) {
        if (e.target === this) {
            document.getElementById('editRouteModal').classList.remove('active');
        }
    });
});





document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const card = this.closest('.card');
            const id_horario = card.querySelector('.card-header h3').textContent.split('#')[1];

            if (confirm('¿Estás seguro de que deseas eliminar este horario?')) {
                // Redirigir al archivo PHP con el ID como parámetro GET
                window.location.href = `../controllers/delete/delete_horario.php?id=${id_horario}`;
            }
        });
    });
});
