// pagination.js
class Pagination {
    constructor(tableId, itemsPerPage = 10) {
        this.table = document.querySelector(tableId);
        this.tbody = this.table.querySelector('tbody');
        this.rows = Array.from(this.tbody.querySelectorAll('tr'));
        this.itemsPerPage = itemsPerPage;
        this.currentPage = 1;
        this.totalPages = Math.ceil(this.rows.length / this.itemsPerPage);
        
        this.init();
    }
    
    init() {
        this.createPaginationControls();
        this.showPage(1);
    }
    
    createPaginationControls() {
        // Crear controles de paginación si no existen
        if (!document.querySelector('.pagination')) {
            const paginationHTML = `
                <div class="pagination">
                    <button class="pagination-btn" id="prevPage" disabled>‹ Anterior</button>
                    <div class="pagination-info" id="pageInfo">Página 1 de ${this.totalPages}</div>
                    <button class="pagination-btn" id="nextPage">Siguiente ›</button>
                </div>
            `;
            this.table.parentNode.insertAdjacentHTML('afterend', paginationHTML);
        }
        
        // Agregar event listeners
        document.getElementById('prevPage').addEventListener('click', () => this.previousPage());
        document.getElementById('nextPage').addEventListener('click', () => this.nextPage());
    }
    
    showPage(page) {
        this.currentPage = page;
        
        // Ocultar todas las filas
        this.rows.forEach(row => row.style.display = 'none');
        
        // Mostrar solo las filas de la página actual
        const start = (page - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        
        this.rows.slice(start, end).forEach(row => {
            row.style.display = '';
        });
        
        // Actualizar controles
        this.updatePaginationControls();
    }
    
    updatePaginationControls() {
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        const pageInfo = document.getElementById('pageInfo');
        
        prevBtn.disabled = this.currentPage === 1;
        nextBtn.disabled = this.currentPage === this.totalPages;
        
        pageInfo.textContent = `Página ${this.currentPage} de ${this.totalPages}`;
        
        // Actualizar estilos de botones
        prevBtn.style.opacity = prevBtn.disabled ? '0.5' : '1';
        nextBtn.style.opacity = nextBtn.disabled ? '0.5' : '1';
    }
    
    previousPage() {
        if (this.currentPage > 1) {
            this.showPage(this.currentPage - 1);
        }
    }
    
    nextPage() {
        if (this.currentPage < this.totalPages) {
            this.showPage(this.currentPage + 1);
        }
    }
}

// Inicializar paginación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    new Pagination('.data-table', 7); // 5 registros por página
});