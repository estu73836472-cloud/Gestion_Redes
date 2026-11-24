<?php
$activePage = 'monitoreo';
$pageEyebrow = 'HU03 · MONITOREO DE DISPOSITIVOS';
require __DIR__ . '/layout/header.php';
?>
<section class="page-heading">
    <div><p class="eyebrow">VISIBILIDAD OPERATIVA</p><h1>Monitoreo</h1><p class="page-lead">Consulta el estado operativo de cada equipo de la red.</p></div>
    <button id="ping-all" class="button button-primary" type="button">⌁ Consultar todos</button>
</section>
<section class="panel monitor-search">
    <form id="monitor-search-form">
        <label class="field"><span>Código del dispositivo</span><input id="monitor-code" type="search" placeholder="Ej. DEV-001" autocomplete="off"></label>
        <button class="button button-primary" type="submit">⌕ Buscar</button>
        <button id="monitor-clear" class="button button-secondary" type="button">Ver todos los dispositivos</button>
    </form>
    <small>Ingresa el código completo para consultar un equipo específico.</small>
</section>
<div id="monitor-message" class="notice" role="status" hidden></div>
<section class="section-block">
    <div class="section-heading">
        <div><p class="eyebrow">ESTADO DE LA RED</p><h2 id="monitor-heading">Dispositivos en monitoreo</h2></div>
        <div class="status-summary"><span class="summary-online" id="monitor-online">— activos</span><span id="monitor-offline">— en falla</span></div>
    </div>
    <div class="panel table-panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Código</th><th>Nombre</th><th>Dirección IP</th><th>Estado</th><th>Última consulta</th><th>Acciones</th></tr></thead>
                <tbody id="monitor-table"><tr><td colspan="6" class="loading-cell">Cargando dispositivos…</td></tr></tbody>
            </table>
        </div>
        <div class="table-foot"><span id="monitor-count">— dispositivos</span><span>Consulta manual · Datos desde el inventario</span></div>
    </div>
</section>
<div class="notice notice-info"><span class="notice-icon">i</span><span><strong>Estados:</strong> Activo indica respuesta satisfactoria; En falla, falta de respuesta. Ejecuta una consulta para actualizar el estado.</span></div>
<?php require __DIR__ . '/layout/footer.php'; ?>
