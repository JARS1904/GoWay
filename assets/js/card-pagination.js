// card-pagination.js
class CardPagination {
    constructor(containerSelector, itemsPerPage = 6) {
        this.container = document.querySelector(containerSelector);
        if (!this.container) {
            console.error(`Container ${containerSelector} no encontrado`);
            return;
        }
        
        this.cards = Array.from(this.container.querySelectorAll('.card'));
        this.itemsPerPage = itemsPerPage;
        this.currentPage = 1;
        this.totalPages = Math.ceil(this.cards.length / this.itemsPerPage);
        
        if (this.cards.length > 0) {
            this.init();
        }
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
            this.container.insertAdjacentHTML('afterend', paginationHTML);
        }
        
        // Agregar event listeners
        document.getElementById('prevPage').addEventListener('click', () => this.previousPage());
        document.getElementById('nextPage').addEventListener('click', () => this.nextPage());
    }
    
    showPage(page) {
        this.currentPage = page;
        
        // Ocultar todas las cards con animación
        this.cards.forEach(card => {
            card.style.opacity = '0';
            card.style.pointerEvents = 'none';
            setTimeout(() => {
                card.style.display = 'none';
            }, 150);
        });
        
        // Mostrar solo las cards de la página actual con animación
        const start = (page - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        
        setTimeout(() => {
            this.cards.slice(start, end).forEach((card, index) => {
                card.style.display = '';
                card.style.pointerEvents = 'auto';
                // Agregar delay escalonado para efecto cascada
                setTimeout(() => {
                    card.style.opacity = '1';
                }, index * 50);
            });
        }, 150);
        
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
    new CardPagination('.card-container', 6); // 6 cards por página
});
