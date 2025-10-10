
    // Mostrar modal al hacer clic en "Agregar nueva ruta"
    document.querySelector('.btn-add').addEventListener('click', function() {
        document.getElementById('addRouteModal').classList.add('active');
    });

    // Ocultar modal al hacer clic en la X, en Cancelar o fuera del modal
    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('addRouteModal').classList.remove('active');
    });

    document.getElementById('cancelModal').addEventListener('click', function() {
        document.getElementById('addRouteModal').classList.remove('active');
    });

    document.getElementById('addRouteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            document.getElementById('addRouteModal').classList.remove('active');
        }
    });



    






