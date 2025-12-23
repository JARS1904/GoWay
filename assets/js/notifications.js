/**
 * Sistema de Notificaciones Reutilizable
 * Uso: showNotification('mensaje', 'success|error|info')
 */

function showNotification(message, type = 'info') {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;

    // Estilos básicos
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-width: 350px;
        cursor: pointer;
    `;

    // Colores según tipo
    if (type === 'success') {
        notification.style.background = 'linear-gradient(135deg, #10b981, #059669)'; // Verde
    } else if (type === 'error') {
        notification.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)'; // Rojo
    } else if (type === 'info') {
        notification.style.background = 'linear-gradient(135deg, #3b82f6, #1d4ed8)'; // Azul
    }

    // Animación
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    if (!document.querySelector('style[data-notification="true"]')) {
        style.setAttribute('data-notification', 'true');
        document.head.appendChild(style);
    }

    // Añadir al documento
    document.body.appendChild(notification);

    // Auto-eliminar después de 5 segundos
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 5000);

    // Permitir cerrar manualmente
    notification.addEventListener('click', () => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    });
}

/**
 * Función para manejar inserción con AJAX
 * @param {HTMLFormElement} formElement - El formulario a enviar
 * @param {string} successMessage - Mensaje de éxito personalizado
 */
function handleInsertForm(formElement, successMessage = 'Registro agregado exitosamente') {
    if (!formElement) return;

    formElement.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Guardando...';

        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;

            if (data.success) {
                formElement.reset();
                
                // Cerrar modal si existe
                const modal = document.getElementById('addRouteModal');
                if (modal) {
                    modal.classList.remove('active');
                }
                
                // Notificación AZUL para inserción
                showNotification(data.message || successMessage, 'info');

                // Recargar después de 3 segundos
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                showNotification(data.message || 'Error al guardar', 'error');
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            console.error('Error:', error);
            showNotification('Error de conexión', 'error');
        });
    });
}

/**
 * Función para manejar actualización con AJAX
 * @param {HTMLFormElement} formElement - El formulario a enviar
 * @param {string} successMessage - Mensaje de éxito personalizado
 */
function handleUpdateForm(formElement, successMessage = 'Registro actualizado exitosamente') {
    if (!formElement) return;

    formElement.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Guardando...';

        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;

            if (data.success) {
                // Cerrar modal si existe
                const modal = document.getElementById('editRouteModal');
                if (modal) {
                    modal.classList.remove('active');
                }
                
                // Notificación VERDE para actualización
                showNotification(data.message || successMessage, 'success');

                // Recargar después de 3 segundos
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                showNotification(data.message || 'Error al actualizar', 'error');
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            console.error('Error:', error);
            showNotification('Error de conexión', 'error');
        });
    });
}

/**
 * Función para manejar eliminación con AJAX
 * @param {HTMLButtonElement} deleteButton - El botón de eliminar
 * @param {string} deleteEndpoint - URL del controlador de eliminación
 * @param {string} idParamName - Nombre del parámetro del ID (ej: 'id_ruta', 'id_horario')
 * @param {string} confirmMessage - Mensaje de confirmación personalizado
 */
function handleDeleteButton(deleteButton, deleteEndpoint, idParamName, confirmMessage = '¿Estás seguro?') {
    deleteButton.addEventListener('click', function() {
        if (!confirm(confirmMessage)) {
            return;
        }

        const row = this.closest('tr') || this.closest('.card') || this.closest('[data-id]');
        const dataAttribute = this.getAttribute('data-id') || row?.getAttribute('data-id') || row?.querySelector('[data-id]')?.getAttribute('data-id');
        
        if (!dataAttribute) {
            showNotification('No se encontró el ID del registro', 'error');
            return;
        }

        this.disabled = true;
        this.style.opacity = '0.5';
        const originalText = this.textContent;
        this.textContent = 'Eliminando...';

        const formData = new FormData();
        formData.append(idParamName, dataAttribute);

        fetch(deleteEndpoint, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            this.disabled = false;
            this.textContent = originalText;
            this.style.opacity = '1';

            if (data.success) {
                // Notificación ROJA para eliminación
                showNotification(data.message || 'Registro eliminado exitosamente', 'error');

                // Recargar después de 3 segundos
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                showNotification(data.message || 'Error al eliminar', 'error');
            }
        })
        .catch(error => {
            this.disabled = false;
            this.textContent = originalText;
            this.style.opacity = '1';
            console.error('Error:', error);
            showNotification('Error de conexión', 'error');
        });
    });
}

/**
 * Inicializar todos los botones de eliminar en una página
 * @param {string} buttonSelector - Selector CSS de los botones
 * @param {string} deleteEndpoint - URL del controlador
 * @param {string} idParamName - Nombre del parámetro del ID
 * @param {string} confirmMessage - Mensaje de confirmación
 */
function initializeDeleteButtons(buttonSelector, deleteEndpoint, idParamName, confirmMessage = '¿Estás seguro?') {
    document.querySelectorAll(buttonSelector).forEach(button => {
        handleDeleteButton(button, deleteEndpoint, idParamName, confirmMessage);
    });
}
