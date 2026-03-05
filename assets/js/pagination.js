// pagination.js
class Pagination {
    constructor(tableId, itemsPerPage = 10) {
        this.table = document.querySelector(tableId);
        this.tbody = this.table.querySelector('tbody');
        this.allRows = Array.from(this.tbody.querySelectorAll('tr'));
        this.filteredRows = [...this.allRows];
        this.itemsPerPage = itemsPerPage;
        this.currentPage = 1;
        this.totalPages = Math.ceil(this.filteredRows.length / this.itemsPerPage);
        this._sortCol = -1;
        this._sortDir = 0; // 0=default, 1=asc, -1=desc

        this.init();
    }

    init() {
        this.createPaginationControls();
        this.showPage(1);
    }

    // Filter rows by text query and re-paginate
    filterRows(query) {
        const q = query.toLowerCase().trim();
        if (!q) {
            this.filteredRows = [...this.allRows];
        } else {
            this.filteredRows = this.allRows.filter(row =>
                row.textContent.toLowerCase().includes(q)
            );
        }
        this.totalPages = Math.max(1, Math.ceil(this.filteredRows.length / this.itemsPerPage));
        this.currentPage = 1;
        this._renderRows();
        this.updatePaginationControls();
        this._updateCount();
    }

    // Sort rows by column index; cycles default → asc → desc
    sortByColumn(colIndex) {
        if (this._sortCol === colIndex) {
            this._sortDir = this._sortDir === 1 ? -1 : (this._sortDir === -1 ? 0 : 1);
        } else {
            this._sortCol = colIndex;
            this._sortDir = 1;
        }

        if (this._sortDir === 0) {
            this.filteredRows = [...this.allRows];
        } else {
            this.filteredRows.sort((a, b) => {
                const aText = (a.cells[colIndex]?.textContent || '').trim().toLowerCase();
                const bText = (b.cells[colIndex]?.textContent || '').trim().toLowerCase();
                return aText < bText ? -this._sortDir : aText > bText ? this._sortDir : 0;
            });
        }
        this.totalPages = Math.max(1, Math.ceil(this.filteredRows.length / this.itemsPerPage));
        this.currentPage = 1;
        this._renderRows();
        this.updatePaginationControls();
        return this._sortDir;
    }

    _renderRows() {
        // Reorder DOM to match the sorted/filtered array
        this.filteredRows.forEach(row => this.tbody.appendChild(row));
        // Hide all, then reveal current page window
        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        this.allRows.forEach(row => row.style.display = 'none');
        this.filteredRows.slice(start, end).forEach(row => row.style.display = '');
    }

    _updateCount() {
        const el = document.getElementById('toolbarCount');
        if (el) {
            const total = this.allRows.length;
            const shown = this.filteredRows.length;
            el.textContent = shown === total
                ? `${total} registro${total !== 1 ? 's' : ''}`
                : `${shown} de ${total} registros`;
        }
    }

    createPaginationControls() {
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
        document.getElementById('prevPage').addEventListener('click', () => this.previousPage());
        document.getElementById('nextPage').addEventListener('click', () => this.nextPage());
    }

    showPage(page) {
        this.currentPage = page;
        this._renderRows();
        this.updatePaginationControls();
    }

    updatePaginationControls() {
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        const pageInfo = document.getElementById('pageInfo');

        prevBtn.disabled = this.currentPage === 1;
        nextBtn.disabled = this.currentPage === this.totalPages;
        pageInfo.textContent = `Página ${this.currentPage} de ${this.totalPages}`;
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
    if (document.querySelector('.data-table')) {
        window.paginationInstance = new Pagination('.data-table', 7);
    }
});