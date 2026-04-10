<?php
// Componente compartido: Modal de confirmación de cierre de sesión.
// Diseño inspirado en el modal de acceso de administrador (adm-card).
?>
<style>
/* ── Logout Confirm Modal ───────────────────────────── */
.lgout-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(10, 15, 30, 0.65);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.lgout-overlay.active { display: flex; }

.lgout-card {
    position: relative;
    background: #fff;
    width: 100%;
    max-width: 340px;
    border-radius: 20px;
    padding: 44px 40px 36px;
    box-shadow: 0 24px 64px rgba(0,0,0,.22);
    animation: lgout-in .25s ease;
    text-align: center;
}
@keyframes lgout-in {
    from { opacity: 0; transform: translateY(-22px) scale(.97); }
    to   { opacity: 1; transform: translateY(0)     scale(1);   }
}

.lgout-close {
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
.lgout-close:hover { background: #e2e6ef; color: #111; }

.lgout-icon-wrap {
    width: 64px; height: 64px;
    background: linear-gradient(135deg, #ef444422, #ef444411);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 18px;
}
.lgout-icon-wrap .material-icons {
    font-size: 30px;
    color: #ef4444;
}

.lgout-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 6px;
}
.lgout-subtitle {
    font-size: .85rem;
    color: #6B7280;
    margin: 0 0 28px;
    line-height: 1.5;
}

.lgout-actions {
    display: flex;
    gap: 10px;
    margin-top: 4px;
}

.lgout-btn-cancel {
    flex: 1;
    padding: 12px;
    background: transparent;
    color: #6B7280;
    font-size: .92rem;
    font-weight: 500;
    border: 1.5px solid #E5E7EB;
    border-radius: 10px;
    cursor: pointer;
    transition: border-color .18s, color .18s;
}
.lgout-btn-cancel:hover { border-color: #9CA3AF; color: #374151; }

.lgout-btn-confirm {
    flex: 1;
    padding: 12px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #fff;
    font-size: .92rem;
    font-weight: 600;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    letter-spacing: .02em;
    box-shadow: 0 4px 14px rgba(239,68,68,.35);
    transition: opacity .18s, transform .12s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.lgout-btn-confirm:hover  { opacity: .92; transform: translateY(-1px); }
.lgout-btn-confirm:active { transform: translateY(0); }

@media (max-width: 480px) {
    .lgout-overlay {
        align-items: flex-end;
    }
    .lgout-card {
        max-width: 100%;
        width: 100%;
        border-radius: 20px 20px 0 0;
        padding: 36px 22px 32px;
        animation: lgout-slide-up .3s ease;
    }
    @keyframes lgout-slide-up {
        from { transform: translateY(100%); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
}
</style>

<div class="lgout-overlay" id="logoutConfirmModal">
    <div class="lgout-card">
        <button class="lgout-close" id="closeLogoutModal">&times;</button>

        <div class="lgout-icon-wrap">
            <span class="material-icons">power_settings_new</span>
        </div>

        <h2 class="lgout-title">¿Cerrar sesión?</h2>
        <p class="lgout-subtitle">Tu sesión actual se cerrará y tendrás que volver a iniciar sesión para acceder al panel.</p>

        <div class="lgout-actions">
            <button type="button" class="lgout-btn-cancel" id="cancelLogoutModal">Cancelar</button>
            <a href="#" id="confirmLogoutBtn" class="lgout-btn-confirm">
                <span class="material-icons" style="font-size:16px;">logout</span>
                Sí, salir
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoutLink = document.getElementById('logout');
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('confirmLogoutBtn').setAttribute('href', this.getAttribute('href'));
                document.getElementById('logoutConfirmModal').classList.add('active');
            });
        }

        const closeLogoutFn = () => document.getElementById('logoutConfirmModal').classList.remove('active');
        document.getElementById('closeLogoutModal')?.addEventListener('click', closeLogoutFn);
        document.getElementById('cancelLogoutModal')?.addEventListener('click', closeLogoutFn);
        document.getElementById('logoutConfirmModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeLogoutFn();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLogoutFn();
        });
    });
</script>
