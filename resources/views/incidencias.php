<?php
$activePage = 'incidencias';
$pageEyebrow = 'HU04 · REGISTRO Y SEGUIMIENTO';
require __DIR__ . '/layout/header.php';
?>
<section class="page-heading">
    <div><p class="eyebrow">SEGUIMIENTO DE ANOMALÍAS</p><h1>Incidencias</h1><p class="page-lead">Registra fallas y consulta su estado asociado a los dispositivos.</p></div>
    <button class="button button-primary" type="button" id="toggle-incident-form">＋ Nueva incidencia</button>
</section>
<section class="metric-grid metric-grid-three" aria-label="Resumen de incidencias">
    <article class="metric-card"><span class="metric-icon red">!</span><span class="metric-label">Abiertas</span><strong data-incident-metric="abiertas">—</strong><small>Requieren atención</small></article>
    <article class="metric-card"><span class="metric-icon amber">◷</span><span class="metric-label">En proceso</span><strong data-incident-metric="en_proceso">—</strong><small>En seguimiento</small></article>
    <article class="metric-card"><span class="metric-icon green">✓</span><span class="metric-label">Resueltas</span><strong data-incident-metric="resueltas">—</strong><small>Atendidas</small></article>
</section>
<section id="incident-form-panel" class="panel incident-form-panel" hidden>
    <div class="section-heading"><div><p class="eyebrow">NUEVO REPORTE</p><h2>Registrar incidencia</h2></div><button class="icon-button" type="button" id="close-incident-form" aria-label="Cerrar formulario">×</button></div>
    <div id="incident-form-message" class="notice" role="alert" hidden></div>
    <form id="incident-form" class="incident-form">
        <label class="field"><span>Dispositivo afectado *</span><select name="dispositivo_id" id="incident-device" required><option value="">Seleccionar dispositivo</option></select></label>
        <label class="field"><span>Título *</span><input name="titulo" required maxlength="160" placeholder="Describe brevemente el problema"></label>
        <label class="field"><span>Gravedad *</span><select name="gravedad" required><option value="baja">Baja</option><option value="media" selected>Media</option><option value="alta">Alta</option><option value="critica">Crítica</option></select></label>
        <label class="field incident-description"><span>Descripción *</span><textarea name="descripcion" rows="3" required placeholder="Añade detalles para ayudar a resolverla"></textarea></label>
        <div class="form-actions"><button class="button button-secondary" type="button" id="cancel-incident-form">Cancelar</button><button class="button button-primary" type="submit">Registrar incidencia</button></div>
    </form>
</section>
<div id="incident-page-message" class="notice notice-error" hidden></div>
<section class="panel">
    <div class="section-heading table-heading"><div><p class="eyebrow">REGISTRO</p><h2>Incidencias reportadas</h2></div>
        <label class="select-filter"><span class="sr-only">Filtrar incidencias por estado</span><select id="incident-status-filter"><option value="">Todos los estados</option><option value="abierta">Abierta</option><option value="en_proceso">En proceso</option><option value="resuelta">Resuelta</option></select></label>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Incidencia</th><th>Dispositivo</th><th>Gravedad</th><th>Fecha de reporte</th><th>Estado</th><th>Actualizar</th></tr></thead>
            <tbody id="incident-table"><tr><td colspan="6" class="loading-cell">Cargando incidencias…</td></tr></tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/layout/footer.php'; ?>
