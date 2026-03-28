// Mostrar modal al hacer clic en "Agregar nueva ruta"
if (document.querySelector('.btn-add')) {
    document.querySelectorAll('.btn-add').forEach(function(btn) {
        if (btn.id !== 'openAddNotificationModal') {
            btn.addEventListener('click', function() {
                const modal = document.getElementById('addRouteModal');
                if (modal) modal.classList.add('active');
            });
        }
    });
}

// Ocultar modal al hacer clic en la X, en Cancelar o fuera del modal
if (document.getElementById('closeModal')) {
    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('addRouteModal').classList.remove('active');
    });
}

if (document.getElementById('cancelModal')) {
    document.getElementById('cancelModal').addEventListener('click', function() {
        document.getElementById('addRouteModal').classList.remove('active');
    });
}

if (document.getElementById('addRouteModal')) {
    document.getElementById('addRouteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
}

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

// Marcar enlace activo según la página actual
document.addEventListener('DOMContentLoaded', function () {
    const currentFile = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.sidebar nav ul li a').forEach(link => {
        const linkFile = link.getAttribute('href').split('/').pop();
        if (linkFile === currentFile) {
            link.classList.add('nav-active');
        }
    });
});

// ====================================================
// Table Toolbar: search, sort, count
// Injected automatically before every .data-table
// ====================================================
document.addEventListener('DOMContentLoaded', function () {
    const table = document.querySelector('.data-table');
    if (!table) return;

    const content = table.closest('.content');
    if (!content) return;

    const sectionHeader = content.querySelector('.section-header');
    if (!sectionHeader) return;

    const toolbar = document.createElement('div');
    toolbar.className = 'table-toolbar';
    toolbar.innerHTML = `
        <div class="toolbar-left">
            <span class="toolbar-count" id="toolbarCount"></span>
        </div>
        <div class="toolbar-right">
            <button class="btn-toolbar" id="btnSortTable" title="Ordenar por primera columna">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
                Ordenar
            </button>
            <div class="toolbar-search">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" class="table-search-input" id="tableSearch" placeholder="Buscar...">
            </div>
        </div>
    `;
    sectionHeader.insertAdjacentElement('afterend', toolbar);

    // Wire up search — pagination.js exposes window.paginationInstance
    const searchInput = document.getElementById('tableSearch');
    searchInput.addEventListener('input', function () {
        if (window.paginationInstance) {
            window.paginationInstance.filterRows(this.value);
        } else {
            // Fallback: simple row visibility (no pagination)
            const q = this.value.toLowerCase().trim();
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(r => {
                r.style.display = q && !r.textContent.toLowerCase().includes(q) ? 'none' : '';
            });
        }
    });

    // Wire up sort button - sorts by first data column (index 0)
    const sortBtn = document.getElementById('btnSortTable');
    let sortLabels = ['Ordenar', 'A → Z', 'Z → A'];
    sortBtn.addEventListener('click', function () {
        if (window.paginationInstance) {
            const dir = window.paginationInstance.sortByColumn(0);
            const idx = dir === 1 ? 1 : dir === -1 ? 2 : 0;
            this.querySelector('span') && (this.querySelector('span').textContent = sortLabels[idx]);
            this.classList.toggle('active', dir !== 0);
            // Update label text node
            const textNodes = [...this.childNodes].filter(n => n.nodeType === 3);
            const labelNode = textNodes[textNodes.length - 1];
            if (labelNode) labelNode.textContent = ' ' + sortLabels[idx];
        }
    });

    // Initial count — wait for pagination.js to init first
    setTimeout(() => {
        if (window.paginationInstance) {
            window.paginationInstance._updateCount();
        } else {
            const el = document.getElementById('toolbarCount');
            if (el) {
                const count = table.querySelectorAll('tbody tr').length;
                el.textContent = `${count} registro${count !== 1 ? 's' : ''}`;
            }
        }
    }, 50);
});