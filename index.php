<?php
// Si el usuario ya tiene sesión, ir al dashboard
session_start();
if (isset($_SESSION['id'])) {
    header('Location: pages/admin/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoWay - Tu destino, a un solo toque</title>
    <meta name="description" content="GoWay conecta ciudades, personas y destinos. Gestiona rutas, flotas y checadores en tiempo real.">
    <link rel="icon" href="assets/images/logo_new.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {
    --blue: #2962FF;
    --blue-hover: #1e4fff;
    --bg-main: #fbfbfd;
    --bg-card: #ffffff;
    --bg-surface: #f5f5f7;
    --text-title: #1d1d1f;
    --text-body: #86868b;
    --border-color: #d2d2d7;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body { 
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: var(--text-body); 
    background-color: var(--bg-main); 
    overflow-x: hidden; 
    -webkit-font-smoothing: antialiased;
}

/* NAVBAR */
nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 6%; height: 64px;
    background: rgba(251, 251, 253, 0.8); 
    backdrop-filter: saturate(180%) blur(20px);
    -webkit-backdrop-filter: saturate(180%) blur(20px);
    transition: all 0.3s ease;
    border-bottom: 1px solid transparent;
}
nav.scrolled { 
    border-bottom: 1px solid rgba(0,0,0,0.05); 
}
.nav-brand { display: flex; align-items: center; gap: 8px; text-decoration: none; }
.nav-brand img { width: 28px; height: 28px; object-fit: contain; }
.nav-brand span { font-size: 1.25rem; font-weight: 700; color: var(--text-title); letter-spacing: -0.5px; }

.nav-links { display: flex; align-items: center; gap: 24px; }
.nav-links a {
    text-decoration: none; font-size: 0.85rem; font-weight: 500;
    color: var(--text-title); 
    transition: color 0.2s;
}
.nav-links a:hover { color: var(--blue); }

.btn-primary-nav {
    padding: 8px 16px;
    border-radius: 99px;
    background: var(--blue);
    color: #fff !important;
    font-weight: 500 !important;
    transition: background 0.3s, transform 0.2s;
}
.btn-primary-nav:hover { background: var(--blue-hover); transform: scale(1.02); }

/* HERO SECTION */
.hero {
    min-height: 100vh;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 140px 6% 80px; 
    text-align: center;
    background: radial-gradient(circle at 50% -20%, #eef3ff 0%, var(--bg-main) 60%);
}

.hero h1 { 
    font-size: clamp(3.5rem, 8vw, 6.5rem); 
    font-weight: 800; 
    line-height: 1.05; 
    letter-spacing: -0.05em; 
    color: var(--text-title);
    margin-bottom: 20px;
    animation: fadeUp 1s ease forwards;
}
.hero h1 span {
    color: var(--blue);
}

.hero p { 
    font-size: clamp(1.2rem, 2.5vw, 1.5rem); 
    color: var(--text-body); 
    max-width: 700px; 
    margin: 0 auto 40px; 
    line-height: 1.5;
    font-weight: 500;
    animation: fadeUp 1s ease forwards 0.1s;
    opacity: 0;
}

.hero-cta { 
    display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;
    animation: fadeUp 1s ease forwards 0.2s;
    opacity: 0;
}

.btn-main {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 16px 32px; border-radius: 99px;
    font-size: 1.05rem; font-weight: 600; text-decoration: none;
    transition: all 0.3s ease;
}
.btn-company {
    background: var(--blue);
    color: #fff;
    box-shadow: 0 4px 14px rgba(41, 98, 255, 0.3);
}
.btn-company:hover {
    background: var(--blue-hover);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(41, 98, 255, 0.4);
}
.btn-user {
    background: #fff;
    color: var(--text-title);
    border: 1px solid var(--border-color);
}
.btn-user:hover {
    background: var(--bg-surface);
    border-color: #1d1d1f;
}

/* HERO IMAGE MOCKUP */
.hero-image {
    margin-top: 60px;
    width: 100%;
    max-width: 1000px;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    animation: fadeUp 1.2s ease forwards 0.3s;
    opacity: 0;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.05);
    display: flex; flex-direction: column;
}
.mockup-header { height: 40px; background: #f5f5f7; border-bottom: 1px solid rgba(0,0,0,0.05); display: flex; align-items: center; padding: 0 16px; gap: 8px; }
.mockup-dot { width: 12px; height: 12px; border-radius: 50%; background: #e5e5ea; }
.mockup-dot:nth-child(1) { background: #ff5f56; }
.mockup-dot:nth-child(2) { background: #ffbd2e; }
.mockup-dot:nth-child(3) { background: #27c93f; }
.mockup-body { width: 100%; display: flex; }
.mockup-body img { width: 100%; height: auto; display: block; border-bottom-left-radius: 24px; border-bottom-right-radius: 24px; }

/* BENTO GRID (CARACTERÍSTICAS) REDISEÑO PREMIUM */
.section-features {
    padding: 140px 6%;
    background: #fff;
    position: relative;
    overflow: hidden;
}

.section-features-title {
    text-align: center;
    font-size: clamp(2.5rem, 6vw, 4.5rem);
    font-weight: 800;
    line-height: 1.05;
    letter-spacing: -0.04em;
    margin-bottom: 80px;
    background: linear-gradient(135deg, #1d1d1f 0%, #434345 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.section-features-title span {
    background: linear-gradient(135deg, var(--blue) 0%, #9333ea 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* SECCION EMPRESA — DOS COLUMNAS + CARRUSEL INTEGRADO */
.section-empresa-intro {
    padding: 160px 0 0 0;
    background: #fbfbfd;
    position: relative;
    overflow: hidden;
}

.empresa-intro-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 6%;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
}

/* Faint radial glow background */
.section-empresa-intro::before {
    content: '';
    position: absolute;
    top: -100px;
    left: -200px;
    width: 700px;
    height: 700px;
    background: radial-gradient(circle, rgba(41,98,255,0.05) 0%, transparent 70%);
    pointer-events: none;
}

.empresa-text-col {
    position: relative;
    z-index: 1;
}

.empresa-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 16px;
    background: rgba(41, 98, 255, 0.07);
    color: var(--blue);
    font-size: 0.78rem;
    font-weight: 700;
    border-radius: 30px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    margin-bottom: 28px;
    border: 1px solid rgba(41,98,255,0.12);
}

.empresa-badge::before {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--blue);
    display: block;
}

.empresa-title {
    font-size: clamp(2.2rem, 4vw, 3.4rem);
    font-weight: 800;
    color: var(--text-title);
    line-height: 1.1;
    letter-spacing: -0.04em;
    margin-bottom: 20px;
}

.empresa-title span {
    color: var(--blue);
}

.empresa-desc {
    font-size: 1.1rem;
    line-height: 1.7;
    color: var(--text-body);
    margin-bottom: 36px;
    max-width: 500px;
}

.empresa-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 40px;
}

.empresa-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    background: #f5f5f7;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-title);
    transition: background 0.25s;
}

.empresa-chip:hover {
    background: #eaeaec;
}

.empresa-chip svg {
    width: 16px; height: 16px;
    color: var(--blue);
    flex-shrink: 0;
}

.empresa-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 16px 32px;
    background: var(--blue);
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    border-radius: 50px;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 8px 24px rgba(41,98,255,0.25);
}

.empresa-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 32px rgba(41,98,255,0.35);
}

.empresa-visual-col {
    position: relative;
}

.empresa-mockup {
    background: #f5f5f7;
    border-radius: 28px;
    padding: 16px 16px 0 16px;
    box-shadow: 0 30px 70px rgba(0,0,0,0.1);
    overflow: hidden;
    position: relative;
}

.empresa-mockup-bar {
    display: flex;
    gap: 7px;
    align-items: center;
    padding-bottom: 14px;
}

.empresa-mockup-dot {
    width: 11px;
    height: 11px;
    border-radius: 50%;
}

.empresa-mockup-dot:nth-child(1) { background: #ff5f57; }
.empresa-mockup-dot:nth-child(2) { background: #ffbd2e; }
.empresa-mockup-dot:nth-child(3) { background: #28ca41; }

.empresa-mockup img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 12px 12px 0 0;
}

/* Floating badge over mockup */
.empresa-float-badge {
    position: absolute;
    bottom: -20px;
    left: -24px;
    background: #fff;
    border-radius: 20px;
    padding: 14px 20px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 200px;
}

.empresa-float-badge .badge-icon {
    width: 44px; height: 44px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--blue), #9333ea);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}

.empresa-float-badge .badge-info strong {
    display: block;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-title);
}

.empresa-float-badge .badge-info span {
    font-size: 0.8rem;
    color: var(--text-body);
}

@media (max-width: 900px) {
    .empresa-intro-inner {
        grid-template-columns: 1fr;
        gap: 50px;
    }
    .empresa-title { font-size: 2.2rem; }
    .empresa-float-badge { display: none; }
}

/* TAB VIEWER integrado — estilo Apple */
.empresa-tabs-section {
    padding: 80px 6% 100px 6%;
    max-width: 1280px;
    margin: 0 auto;
}

.empresa-tabs-label {
    text-align: center;
    margin-bottom: 40px;
}

.empresa-tabs-label h3 {
    font-size: clamp(1.3rem, 2.5vw, 1.8rem);
    font-weight: 700;
    color: var(--text-title);
    letter-spacing: -0.02em;
    margin-bottom: 6px;
}

.empresa-tabs-label p {
    font-size: 1rem;
    color: var(--text-body);
}

.tab-nav {
    display: flex;
    justify-content: center;
    background: #f0f0f5;
    border-radius: 20px;
    padding: 6px;
    gap: 4px;
    margin-bottom: 40px;
    width: fit-content;
    margin-left: auto;
    margin-right: auto;
    flex-wrap: wrap;
}

.tab-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 22px;
    border-radius: 14px;
    border: none;
    background: transparent;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-body);
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.tab-btn svg {
    width: 15px;
    height: 15px;
    flex-shrink: 0;
    transition: color 0.3s;
}

.tab-btn.active {
    background: #fff;
    color: var(--blue);
    box-shadow: 0 2px 12px rgba(0,0,0,0.1);
}

.tab-btn.active svg {
    color: var(--blue);
}

.tab-panels {
    position: relative;
}

.tab-panel {
    display: none;
    animation: tabFadeIn 0.4s ease;
}

.tab-panel.active {
    display: block;
}

@keyframes tabFadeIn {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}

.tab-panel-inner {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 60px;
    align-items: center;
}

.tab-panel-text h4 {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--text-title);
    letter-spacing: -0.03em;
    margin-bottom: 14px;
}

.tab-panel-text p {
    font-size: 1.05rem;
    line-height: 1.65;
    color: var(--text-body);
}

.tab-panel-img {
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.04);
}

.tab-panel-img img {
    width: 100%;
    height: auto;
    display: block;
}

@media (max-width: 900px) {
    .tab-panel-inner {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    .tab-nav {
        width: 100%;
        overflow-x: auto;
    }
}

.carousel-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0 6%;
    max-width: 1000px;
    margin: 0 auto 60px auto;
    text-align: center;
}

.carousel-header .section-features-title {
    margin-bottom: 24px;
    text-align: center;
    font-size: clamp(3rem, 7vw, 5rem);
    letter-spacing: -0.05em;
    line-height: 1.05;
}



/* ROLES SECTION */
.roles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 32px;
    max-width: 1200px;
    margin: 0 auto;
}

.role-card {
    background: var(--bg-surface);
    border-radius: 32px;
    padding: 48px 40px;
    display: flex; flex-direction: column; align-items: center; text-align: center;
    transition: transform 0.3s ease;
}
.role-card:hover { transform: translateY(-8px); }

.role-icon {
    width: 80px; height: 80px; border-radius: 24px;
    background: #fff; box-shadow: 0 8px 16px rgba(0,0,0,0.05);
    display: flex; align-items: center; justify-content: center;
    font-size: 2.5rem; margin-bottom: 24px;
}

.role-card h3 { font-size: 1.75rem; font-weight: 700; color: var(--text-title); margin-bottom: 16px; }
.role-card p { font-size: 1.05rem; line-height: 1.5; margin-bottom: 32px; flex-grow: 1; }

.role-link {
    font-size: 1.05rem; font-weight: 600; color: var(--blue); text-decoration: none;
    display: inline-flex; align-items: center; gap: 4px;
}
.role-link:hover { text-decoration: underline; }
.role-link svg { transition: transform 0.2s; }
.role-link:hover svg { transform: translateX(4px); }

.role-notice {
    font-size: 0.85rem; color: #ff9500; background: rgba(255, 149, 0, 0.1);
    padding: 12px 16px; border-radius: 12px; margin-bottom: 24px;
    font-weight: 500; text-align: left;
}


/* FOOTER (old — replaced by site-footer below) */
footer:not(.site-footer) {
    display: none;
}

.site-footer {
    text-align: left;
}

/* ANIMATIONS */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* MOBILE APP SECTIONS (Checadores & Usuarios) */
.section-mobile {
    padding: 140px 6% 120px 6%;
    position: relative;
    overflow: hidden;
}

.section-mobile.alt-bg {
    background: #fbfbfd;
}

.mobile-inner {
    max-width: 1280px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
}

.mobile-inner.reverse {
    direction: rtl;
}
.mobile-inner.reverse > * {
    direction: ltr;
}

.mobile-text-col {
    position: relative;
    z-index: 1;
}

.mobile-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 16px;
    font-size: 0.78rem;
    font-weight: 700;
    border-radius: 30px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    margin-bottom: 28px;
    border: 1px solid;
}

.mobile-badge.checador {
    background: rgba(41,98,255,0.07);
    color: var(--blue);
    border-color: rgba(41,98,255,0.12);
}
.mobile-badge.checador::before {
    content: '';
    width: 8px; height: 8px;
    border-radius: 50%;
    background: var(--blue);
    display: block;
}

.mobile-badge.usuario {
    background: rgba(41,98,255,0.07);
    color: var(--blue);
    border-color: rgba(41,98,255,0.12);
}
.mobile-badge.usuario::before {
    content: '';
    width: 8px; height: 8px;
    border-radius: 50%;
    background: var(--blue);
    display: block;
}

.mobile-title {
    font-size: clamp(2.2rem, 4vw, 3.2rem);
    font-weight: 800;
    color: var(--text-title);
    line-height: 1.1;
    letter-spacing: -0.04em;
    margin-bottom: 20px;
}

.mobile-title span {
    color: var(--blue);
}

.mobile-desc {
    font-size: 1.1rem;
    line-height: 1.7;
    color: var(--text-body);
    margin-bottom: 36px;
    max-width: 500px;
}

.mobile-features {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 40px;
}

.mobile-feature-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}

.mfi-icon {
    width: 40px; height: 40px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 1rem;
    background: rgba(41,98,255,0.08);
    color: var(--blue);
}

.mfi-text strong {
    display: block;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-title);
    margin-bottom: 2px;
}

.mfi-text span {
    font-size: 0.875rem;
    color: var(--text-body);
    line-height: 1.4;
}

.mobile-notice {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px 20px;
    background: rgba(41,98,255,0.04);
    border: 1px solid rgba(41,98,255,0.12);
    border-radius: 16px;
    font-size: 0.9rem;
    color: var(--text-title);
    line-height: 1.5;
}

.mobile-notice svg {
    width: 20px; height: 20px;
    color: var(--blue);
    flex-shrink: 0;
    margin-top: 1px;
}

.mobile-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 16px 32px;
    background: var(--blue);
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    border-radius: 50px;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 8px 24px rgba(41,98,255,0.25);
}
.mobile-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 32px rgba(41,98,255,0.35);
}

/* Screenshots Grid */
.screenshots-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    align-items: start;
}

.screenshot-card {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.04);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: #f5f5f7;
}

.screenshot-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.14);
}

.screenshot-card img {
    width: 100%;
    height: auto;
    display: block;
}

@media (max-width: 900px) {
    .mobile-inner, .mobile-inner.reverse {
        grid-template-columns: 1fr;
        direction: ltr;
        gap: 50px;
    }
    .screenshots-grid {
        max-width: 420px;
        margin: 0 auto;
    }
}

/* MODERN FOOTER */
.site-footer {
    background: #1d1d1f;
    color: #a1a1a6;
    padding: 80px 6% 40px 6%;
}

.footer-inner {
    max-width: 1280px;
    margin: 0 auto;
}

.footer-top {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 60px;
    padding-bottom: 60px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    margin-bottom: 40px;
}

.footer-brand p {
    font-size: 0.9rem;
    line-height: 1.6;
    margin-top: 12px;
    max-width: 260px;
}

.footer-brand .brand-name {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1.2rem;
    font-weight: 700;
    color: #fff;
}

.footer-brand .brand-name img {
    width: 26px;
    height: 26px;
    object-fit: contain;
}

.footer-col h4 {
    font-size: 0.75rem;
    font-weight: 700;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 16px;
}

.footer-col a {
    display: block;
    color: #a1a1a6;
    text-decoration: none;
    font-size: 0.9rem;
    margin-bottom: 10px;
    transition: color 0.2s;
}

.footer-col a:hover { color: #fff; }

.footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.82rem;
    flex-wrap: wrap;
    gap: 12px;
}

.footer-bottom a {
    color: #a1a1a6;
    text-decoration: none;
}

.footer-bottom a:hover { color: #fff; }

@media (max-width: 900px) {
    .footer-top {
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }
    .footer-brand { grid-column: 1 / -1; }
}
@media (max-width: 768px) {
    .nav-links a:not(.btn-primary-nav) { display: none; }
    .hero { padding-top: 120px; }
    .hero h1 { font-size: clamp(2.5rem, 10vw, 3.5rem); }
    .hero-cta { flex-direction: column; width: 100%; max-width: 300px; margin: 0 auto; }
    .btn-main { width: 100%; }
}
</style>
</head>
<body>

<nav id="mainNav">
    <a href="index.php" class="nav-brand">
        <img src="assets/images/logo_new.png" alt="GoWay Logo">
        <span>GoWay</span>
    </a>
    <div class="nav-links">
        <a href="#empresas">Empresas</a>
        <a href="#checadores">Checadores</a>
        <a href="#usuarios">Usuarios</a>
        <a href="pages/login.php" class="btn-primary-nav">Iniciar sesión</a>
    </div>
</nav>

<section class="hero" id="inicio">
    <h1>El transporte,<br><span>hecho inteligente.</span></h1>
    <p>
        GoWay transforma la manera en que gestionas flotas y rutas. Una plataforma centralizada y potente para un servicio eficiente.
    </p>
    
    <div class="hero-cta">
        <a href="pages/registro_empresa.php" class="btn-main btn-company">
            Registrar mi Empresa
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
        <a href="pages/registro.php" class="btn-main btn-user">
            Registrarme como Usuario
        </a>
    </div>

    <div class="hero-image">
        <div class="mockup-header">
            <div class="mockup-dot"></div>
            <div class="mockup-dot"></div>
            <div class="mockup-dot"></div>
        </div>
        <div class="mockup-body">
            <img src="assets/images/empresa/1-dashboard.png" alt="GoWay Dashboard">
        </div>
    </div>
</section>

<section class="section-empresa-intro" id="empresas">
    <div class="empresa-intro-inner">

        <!-- TEXTO -->
        <div class="empresa-text-col">
            <div class="empresa-badge">Para Empresas</div>
            <h2 class="empresa-title">El panel que tu<br>empresa <span>necesita.</span></h2>
            <p class="empresa-desc">
                Administra tu flota de forma centralizada. Controla rutas, vehículos, horarios y checadores desde un solo lugar, con métricas en tiempo real que te ayudan a tomar mejores decisiones.
            </p>

            <div class="empresa-chips">
                <span class="empresa-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                    Gestión de Rutas
                </span>
                <span class="empresa-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 3v18"/></svg>
                    Control de Flota
                </span>
                <span class="empresa-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 3v18h18"/><path d="m7 16 4-4 4 4 5-5"/></svg>
                    KPIs en Tiempo Real
                </span>
                <span class="empresa-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Gestión de Checadores
                </span>
                <span class="empresa-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Reportes Automáticos
                </span>
            </div>

            <a href="pages/registro_empresa.php" class="empresa-cta">
                Registrar mi empresa
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        </div>

        <!-- VISUAL -->
        <div class="empresa-visual-col">
            <div class="empresa-mockup">
                <div class="empresa-mockup-bar">
                    <div class="empresa-mockup-dot"></div>
                    <div class="empresa-mockup-dot"></div>
                    <div class="empresa-mockup-dot"></div>
                </div>
                <img src="assets/images/empresa/1-dashboard.png" alt="Dashboard Empresa GoWay">
            </div>
            <div class="empresa-float-badge">
                <div class="badge-icon">📊</div>
                <div class="badge-info">
                    <strong>KPIs en vivo</strong>
                    <span>Actualización en tiempo real</span>
                </div>
            </div>
        </div>

    </div>

    <!-- TAB VIEWER - Explorer de funcionalidades -->
    <div class="empresa-tabs-section" id="caracteristicas">
        <div class="empresa-tabs-label">
            <h3>Explora cada funcionalidad</h3>
            <p>Selecciona una herramienta para ver cómo funciona en tu panel.</p>
        </div>

        <div class="tab-nav" role="tablist">
            <button class="tab-btn active" onclick="switchTab(this, 'tab-rutas')" role="tab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                Rutas
            </button>
            <button class="tab-btn" onclick="switchTab(this, 'tab-flota')" role="tab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 3v18"/></svg>
                Vehículos
            </button>
            <button class="tab-btn" onclick="switchTab(this, 'tab-kpis')" role="tab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 3v18h18"/><path d="m7 16 4-4 4 4 5-5"/></svg>
                KPIs
            </button>
            <button class="tab-btn" onclick="switchTab(this, 'tab-reportes')" role="tab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Reportes
            </button>
            <button class="tab-btn" onclick="switchTab(this, 'tab-notificaciones')" role="tab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                Notificaciones
            </button>
        </div>

        <div class="tab-panels">

            <div class="tab-panel active" id="tab-rutas">
                <div class="tab-panel-inner">
                    <div class="tab-panel-text">
                        <h4>Gestión de Rutas</h4>
                        <p>Diseña, edita y supervisa cada recorrido de tu flota. Agrega paradas, ajusta horarios y monitorea el estado de cada ruta desde un panel centralizado y fácil de usar.</p>
                    </div>
                    <div class="tab-panel-img">
                        <img src="assets/images/empresa/2-rutas.png" alt="Gestión de Rutas">
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="tab-flota">
                <div class="tab-panel-inner">
                    <div class="tab-panel-text">
                        <h4>Control de Flota</h4>
                        <p>Registra y administra cada vehículo de tu empresa. Asigna conductores, revisa el estado operativo y mantén tu flota siempre en orden y disponible para el servicio.</p>
                    </div>
                    <div class="tab-panel-img">
                        <img src="assets/images/empresa/5-vehiculos.png" alt="Vehículos">
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="tab-kpis">
                <div class="tab-panel-inner">
                    <div class="tab-panel-text">
                        <h4>Indicadores y KPIs</h4>
                        <p>Visualiza el rendimiento de tu operación con gráficos claros y actualizados. Identifica áreas de mejora y toma decisiones informadas basándote en datos reales de tu servicio.</p>
                    </div>
                    <div class="tab-panel-img">
                        <img src="assets/images/empresa/1-dashboard.png" alt="KPIs y Dashboard">
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="tab-reportes">
                <div class="tab-panel-inner">
                    <div class="tab-panel-text">
                        <h4>Generación de Reportes</h4>
                        <p>Exporta informes detallados de rutas, vehículos e incidencias. Documenta tu operación y comparte resultados con tu equipo de forma rápida y estructurada.</p>
                    </div>
                    <div class="tab-panel-img">
                        <img src="assets/images/empresa/8-reportes.png" alt="Reportes">
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="tab-notificaciones">
                <div class="tab-panel-inner">
                    <div class="tab-panel-text">
                        <h4>Notificaciones</h4>
                        <p>Envía alertas en tiempo real a usuarios y checadores sobre cambios en rutas, retrasos o incidencias. Mantén a todos informados con un solo clic desde tu panel.</p>
                    </div>
                    <div class="tab-panel-img">
                        <img src="assets/images/empresa/9-notificaciones.png" alt="Notificaciones">
                    </div>
                </div>
            </div>

        </div>
    </div>

</section>

<!-- SECCIÓN CHECADORES -->
<section class="section-mobile alt-bg" id="checadores">
    <div class="mobile-inner">

        <div class="mobile-text-col">
            <div class="mobile-badge checador">Para Checadores</div>
            <h2 class="mobile-title">Tu herramienta en el<br><span>campo de operación.</span></h2>
            <p class="mobile-desc">
                La app móvil de GoWay para checadores es rápida, intuitiva y diseñada para trabajar en movimiento. Reporta incidencias, busca vehículos y mantén el flujo del transporte actualizado en tiempo real.
            </p>

            <div class="mobile-features">
                <div class="mobile-feature-item">
                    <div class="mfi-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <div class="mfi-text">
                        <strong>Buscar vehículo asignado</strong>
                        <span>Localiza rápidamente el autobús asignado a tu ruta del día.</span>
                    </div>
                </div>
                <div class="mobile-feature-item">
                    <div class="mfi-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div class="mfi-text">
                        <strong>Actualizar lugares</strong>
                        <span>Registra la ocupación del vehículo y paradas en tiempo real.</span>
                    </div>
                </div>
                <div class="mobile-feature-item">
                    <div class="mfi-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                    <div class="mfi-text">
                        <strong>Reporte de incidencias</strong>
                        <span>Documenta problemas en ruta con detalle y envía al instante.</span>
                    </div>
                </div>
            </div>

            <div class="mobile-notice">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span><strong>Acceso por invitación:</strong> Tu cuenta es creada por la empresa de transporte para la que trabajas. No es necesario registrarte.</span>
            </div>
        </div>

        <div class="screenshots-grid">
            <div class="screenshot-card">
                <img src="assets/images/checador/1-bucar-vehiculo.jpeg" alt="Buscar vehículo">
            </div>
            <div class="screenshot-card">
                <img src="assets/images/checador/3-reportes.jpeg" alt="Reportes">
            </div>
            <div class="screenshot-card">
                <img src="assets/images/checador/4-perfil.jpeg" alt="Perfil">
            </div>
        </div>

    </div>
</section>

<!-- SECCIÓN USUARIOS -->
<section class="section-mobile" id="usuarios">
    <div class="mobile-inner reverse">

        <div class="mobile-text-col">
            <div class="mobile-badge usuario">Para Usuarios</div>
            <h2 class="mobile-title">Tu transporte,<br><span>en tu bolsillo.</span></h2>
            <p class="mobile-desc">
                Consulta rutas, revisa horarios disponibles, guarda tus favoritas y recibe notificaciones en tiempo real. La app de usuario de GoWay hace que moverte por la ciudad sea simple y sin sorpresas.
            </p>

            <div class="mobile-features">
                <div class="mobile-feature-item">
                    <div class="mfi-icon"><i class="fa-solid fa-route"></i></div>
                    <div class="mfi-text">
                        <strong>Selección de rutas</strong>
                        <span>Explora todas las rutas disponibles y elige la que más te convenga.</span>
                    </div>
                </div>
                <div class="mobile-feature-item">
                    <div class="mfi-icon"><i class="fa-solid fa-clock"></i></div>
                    <div class="mfi-text">
                        <strong>Horarios en tiempo real</strong>
                        <span>Consulta los próximos horarios de salida y evita la espera.</span>
                    </div>
                </div>
                <div class="mobile-feature-item">
                    <div class="mfi-icon"><i class="fa-solid fa-file-lines"></i></div>
                    <div class="mfi-text">
                        <strong>Reportes</strong>
                        <span>Consulta el historial de tus viajes y reporta incidencias desde la app.</span>
                    </div>
                </div>
            </div>

            <a href="pages/registro.php" class="mobile-cta">
                Crear cuenta gratuita
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        </div>

        <div class="screenshots-grid">
            <div class="screenshot-card">
                <img src="assets/images/usuario/1-seleccion-rutas.jpeg" alt="Selección de rutas">
            </div>
            <div class="screenshot-card">
                <img src="assets/images/usuario/2-horarios-disponibles.jpeg" alt="Horarios disponibles">
            </div>
            <div class="screenshot-card">
                <img src="assets/images/usuario/4-reportes.jpeg" alt="Reportes">
            </div>
        </div>

    </div>
</section>

<!-- FOOTER MODERNO -->
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-top">
            <div class="footer-brand">
                <div class="brand-name">
                    <img src="assets/images/logo_new.png" alt="GoWay">
                    GoWay
                </div>
                <p>Sistema inteligente de transporte público. Conectamos empresas, checadores y usuarios en una sola plataforma segura y eficiente.</p>
            </div>
            <div class="footer-col">
                <h4>Plataforma</h4>
                <a href="#empresas">Para Empresas</a>
                <a href="#checadores">Para Checadores</a>
                <a href="#usuarios">Para Usuarios</a>
                <a href="pages/login.php">Iniciar sesión</a>
            </div>
            <div class="footer-col">
                <h4>Cuenta</h4>
                <a href="pages/registro_empresa.php">Registrar empresa</a>
                <a href="pages/registro.php">Crear cuenta usuario</a>
                <a href="pages/login.php">Acceder al panel</a>
            </div>
            <div class="footer-col">
                <h4>GoWay</h4>
                <a href="#inicio">Inicio</a>
                <a href="#empresas">Características</a>
                <a href="pages/login.php">Soporte</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?php echo date('Y'); ?> GoWay. Todos los derechos reservados.</span>
        </div>
    </div>
</footer>

<script>
    // Tab Viewer logic
    function switchTab(btn, panelId) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(panelId).classList.add('active');
    }

    // Navbar scroll effect
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 10);
    });
</script>
</body>
</html>
