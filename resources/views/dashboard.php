<?php
$activePage = 'inicio';
$pageEyebrow = 'PANEL GENERAL · RESUMEN OPERATIVO';
require __DIR__ . '/layout/header.php';
?>
<section class="welcome-row">
    <div>
        <p class="eyebrow">CENTRO DE OPERACIONES</p>
        <h1>Tu red, en perspectiva.</h1>
        <p class="page-lead">Inventario y monitoreo de dispositivos de red. Una consola clara para registrar, mantener y consultar tu infraestructura.</p>
    </div>
    <a class="button button-primary" href="/dispositivos/nuevo"><span>＋</span> Registrar dispositivo</a>
</section>

<section class="metric-grid" aria-label="Resumen de dispositivos">
    <article class="metric-card"><span class="metric-icon blue">▤</span><span class="metric-label">Dispositivos registrados</span><strong data-metric="total">—</strong><small>En el inventario</small></article>
    <article class="metric-card"><span class="metric-icon green">●</span><span class="metric-label">En línea</span><strong data-metric="online">—</strong><small>Estado conocido</small></article>
    <article class="metric-card"><span class="metric-icon red">!</span><span class="metric-label">Fuera de línea</span><strong data-metric="offline">—</strong><small>Requieren atención</small></article>
    <article class="metric-card"><span class="metric-icon amber">⚑</span><span class="metric-label">Incidencias abiertas</span><strong data-metric="incidents">—</strong><small>Por revisar</small></article>
</section>

<section class="dashboard-grid">
    <article class="panel">
        <div class="section-heading">
            <div><p class="eyebrow">INVENTARIO</p><h2>Dispositivos recientes</h2></div>
            <a class="text-link" href="/dispositivos">Ver inventario <span>→</span></a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Dispositivo</th><th>Dirección IP</th><th>Tipo</th><th>Estado</th></tr></thead>
                <tbody id="dashboard-devices"><tr><td colspan="4" class="loading-cell">Cargando inventario…</td></tr></tbody>
            </table>
        </div>
    </article>
    <article class="panel">
        <div class="section-heading"><div><p class="eyebrow">ACTIVIDAD</p><h2>Incidencias recientes</h2></div><a class="text-link" href="/incidencias">Ver todas <span>→</span></a></div>
        <div id="dashboard-incidents" class="activity-list"><p class="loading-cell">Cargando actividad…</p></div>
    </article>
</section>
<?php require __DIR__ . '/layout/footer.php'; ?>
