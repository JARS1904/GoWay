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

// Manejar envío del formulario de agregar con AJAX
document.addEventListener('DOMContentLoaded', function() {
    const routeForm = document.getElementById('routeForm');
    if (routeForm) {
        // Solo ejecutar si notifications.js no está cargado (para compatibilidad con páginas antiguas)
        if (typeof handleInsertForm === 'undefined') {
            routeForm.addEventListener('submit', function(e) {
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
                        // Limpiar formulario
                        routeForm.reset();
                        
                        // Cerrar modal
                        document.getElementById('addRouteModal').classList.remove('active');
                        
                        // Mostrar notificación en AZUL para inserción
                        if (typeof showNotification === 'function') {
                            showNotification(data.message || 'Ruta agregada exitosamente', 'info');
                        }

                        // Recargar tabla después de 3 segundos (más tiempo para ver la notificación)
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    } else {
                        // Error
                        if (typeof showNotification === 'function') {
                            showNotification(data.message || 'Error al agregar la ruta', 'error');
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
                        showNotification('Error de conexión al agregar', 'error');
                    } else {
                        alert('Error de conexión: ' + error.message);
                    }
                });
            });
        }
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