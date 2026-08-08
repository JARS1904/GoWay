<?php
// Componente compartido: Modal de Créditos de íconos
?>
<style>
/* ── Credits Modal ───────────────────────────── */
.cred-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(10, 15, 30, 0.65);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.cred-overlay.active { display: flex; }

.cred-card {
    position: relative;
    background: #fff;
    width: 100%;
    max-width: 400px;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 24px 64px rgba(0,0,0,.22);
    animation: cred-in .25s ease;
}
@keyframes cred-in {
    from { opacity: 0; transform: translateY(-22px) scale(.97); }
    to   { opacity: 1; transform: translateY(0)     scale(1);   }
}

.cred-close {
    position: absolute;
    top: 16px; right: 18px;
    background: #f1f3f7;
    border: none;
    width: 32px; height: 32px;
    border-radius: 50%;
    font-size: 18px;
    line-height: 1;
    cursor: pointer;
    color: #555;
    display: flex; align-items: center; justify-content: center;
    transition: background .18s;
}
.cred-close:hover { background: #e2e6ef; color: #111; }

.cred-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 16px;
    text-align: center;
}
.cred-list {
    max-height: 300px;
    overflow-y: auto;
    font-size: 0.85rem;
    line-height: 1.6;
    color: #6B7280;
    padding-right: 10px;
}
.cred-list a {
    color: #3b82f6;
    text-decoration: none;
}
.cred-list a:hover {
    text-decoration: underline;
}
.cred-list ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.cred-list li {
    margin-bottom: 8px;
    border-bottom: 1px solid #f1f3f7;
    padding-bottom: 8px;
}
.cred-list li:last-child {
    border-bottom: none;
}
</style>

<div class="cred-overlay" id="creditsModal">
    <div class="cred-card">
        <button class="cred-close" id="closeCreditsModal">&times;</button>
        <h2 class="cred-title">Créditos de Íconos</h2>
        <div class="cred-list">
            <ul>
                <li><a target="_blank" href="https://icons8.com/icon/uWyVYfqqdYxW/dashboard-layout">Dashboard Layout</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/jLkEh2zFhThT/select-cursor">puntero azul</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/83147/clock">Reloj</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/93868/driver">Conductor</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/93914/shuttle-bus">Servicio de transporte</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/5eWKaecnIr9F/private">Privado</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/NEa2fDnzL4XQ/code-fork">Bifurcación código</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/L7vFC7IIQjcT/checked-identification-documents">Documentos de identificación comprobados</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/EYKscqtifDDh/document">Document</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/32058/bell">Campana</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/7AV1RfsFfo4a/show-right-side-panel">Mostrar panel lateral derecho</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/2yC9SZKcXDdX/profile">Usuario masculino en círculo</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/88693/business-building">Enterprise</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/lrrWa22VGVi6/organization">Enterprise</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/DvHoFO0szQ0v/road">Carretera</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/SHlV5jVHvSGN/public-transportation">Transporte terrestre</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/17507/driver">Conductor</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/PTVr218c3sJO/schedule">Horas extras</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/bTYe4FY9DVty/verified-account">Verified account</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/gs0Idfz02EOO/bus">Bus</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/11269/hierarchy">Hierarchy</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/Od91LuQPaarw/place-marker">Geo-cerca</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
                <li><a target="_blank" href="https://icons8.com/icon/yqTGgVcAZYHJ/bus-stop">Bus Stop</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a></li>
            </ul>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const credLink = document.getElementById('openCredits');
        if (credLink) {
            credLink.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('creditsModal').classList.add('active');
            });
        }
        const closeCredFn = () => document.getElementById('creditsModal').classList.remove('active');
        document.getElementById('closeCreditsModal')?.addEventListener('click', closeCredFn);
        document.getElementById('creditsModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeCredFn();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeCredFn();
        });
    });
</script>
