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

// ==================== //
// FUNCIONES PARA EL MENÚ HAMBURGUESA
// ==================== //

// Función para abrir/cerrar el sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.querySelector('.toggle-btn');
    
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    
    // Ocultar botón hamburguesa cuando el menú está abierto
    if (sidebar.classList.contains('active')) {
        toggleBtn.style.opacity = '0';
        toggleBtn.style.visibility = 'hidden';
    } else {
        toggleBtn.style.opacity = '1';
        toggleBtn.style.visibility = 'visible';
    }
    
    // Prevenir scroll del body cuando el menú está abierto
    document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
}

// Función para cerrar el sidebar
function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.querySelector('.toggle-btn');
    
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
    
    // Mostrar botón hamburguesa al cerrar
    toggleBtn.style.opacity = '1';
    toggleBtn.style.visibility = 'visible';
    
    document.body.style.overflow = '';
}

// Cerrar sidebar al hacer clic en un enlace (solo en móvil)
document.querySelectorAll('.sidebar nav a').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            closeSidebar();
        }
    });
});

// Cerrar sidebar con tecla ESC
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeSidebar();
    }
});

// Cerrar sidebar al redimensionar la ventana si es mayor a móvil
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        closeSidebar();
    }
});