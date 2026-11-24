<?php
$activePage = 'dispositivos';
$pageEyebrow = 'HU02 · FICHA DEL DISPOSITIVO';
$id = (int)$dispositivo['id'];
$code = 'DEV-' . str_pad((string)$id, 3, '0', STR_PAD_LEFT);
$escape = static fn($value): string => htmlspecialchars((string)($value ?? '—'), ENT_QUOTES, 'UTF-8');
$statusPresentation = [
    'en_linea' => ['Activo', 'online'],
    'fuera_de_linea' => ['En falla', 'offline'],
    'advertencia' => ['Advertencia', 'warning'],
    'desconocido' => ['Sin monitorear', 'unknown']
][$dispositivo['estado']] ?? ['Desconocido', 'unknown'];
require __DIR__ . '/layout/header.php';
?>
<a class="back-link" href="/dispositivos">← Volver a dispositivos</a>
<?php if (isset($_GET['actualizado'])): ?><div class="notice notice-success">Los cambios del dispositivo se guardaron correctamente.</div><?php endif; ?>
<section class="page-heading">
    <div><p class="eyebrow"><?= $escape($code) ?> · FICHA DEL DISPOSITIVO</p><h1><?= $escape($dispositivo['nombre']) ?></h1><p class="page-lead">Información de identificación, conectividad y asignación del equipo.</p></div>
    <a class="button button-primary" href="/dispositivos/<?= $id ?>/editar">✎ Editar dispositivo</a>
</section>
<section class="detail-grid">
    <article class="panel detail-card">
        <p class="eyebrow">INFORMACIÓN DEL DISPOSITIVO</p><h2>Identificación y red</h2>
        <dl class="detail-list">
            <div><dt>Código</dt><dd><?= $escape($code) ?></dd></div><div><dt>Nombre / identificador</dt><dd><?= $escape($dispositivo['nombre']) ?></dd></div>
            <div><dt>Dirección IPv4</dt><dd class="mono"><?= $escape($dispositivo['ip']) ?></dd></div><div><dt>Tipo de dispositivo</dt><dd><?= $escape($dispositivo['tipo']) ?></dd></div>
            <div><dt>Ubicación</dt><dd><?= $escape($dispositivo['ubicacion']) ?></dd></div><div><dt>Dirección MAC</dt><dd class="mono"><?= $escape($dispositivo['mac']) ?></dd></div>
        </dl>
    </article>
    <aside class="panel status-card"><p class="eyebrow">ESTADO OPERATIVO</p><h2>Estado actual</h2><span class="status-badge <?= $statusPresentation[1] ?>" data-status="<?= $escape($dispositivo['estado']) ?>"><?= $escape($statusPresentation[0]) ?></span><p>El estado refleja el último valor registrado para este equipo.</p><div class="ip-block"><small>IP consultada</small><strong class="mono"><?= $escape($dispositivo['ip']) ?></strong></div><button class="button button-secondary button-full" data-ping-device="<?= $id ?>">⌁ Consultar en monitoreo</button><div id="detail-ping-result" class="small-result" aria-live="polite"></div></aside>
</section>
<section class="section-block">
    <div class="section-heading"><div><p class="eyebrow">OPERACIÓN</p><h2>Gestión del dispositivo</h2></div></div>
    <div class="quick-link-grid">
        <a class="quick-link-card" href="/monitoreo?codigo=<?= rawurlencode($code) ?>"><span class="metric-icon blue">⌁</span><strong>Monitoreo</strong><small>Consultar estado y latencia</small><span class="text-link">Consultar estado →</span></a>
        <a class="quick-link-card" href="/incidencias?dispositivo_id=<?= $id ?>"><span class="metric-icon amber">⚑</span><strong>Incidencias</strong><small>Registrar y revisar anomalías</small><span class="text-link">Ver incidencias →</span></a>
        <a class="quick-link-card" href="/monitoreo?codigo=<?= rawurlencode($code) ?>"><span class="metric-icon blue">◷</span><strong>Historial</strong><small>Registro de comprobaciones</small><span class="text-link">Ver historial →</span></a>
    </div>
</section>
<?php require __DIR__ . '/layout/footer.php'; ?>
