<?php
$activePage = 'dispositivos';
$pageEyebrow = 'HU01 + HU02 · INVENTARIO DE DISPOSITIVOS';
require __DIR__ . '/layout/header.php';
?>
<section class="page-heading">
    <div><p class="eyebrow">INVENTARIO DE RED</p><h1>Dispositivos</h1><p class="page-lead">Tu infraestructura de red, organizada en un solo lugar.</p></div>
    <button class="button button-primary" type="button" id="open-device-dialog"><span>＋</span> Registrar dispositivo</button>
</section>
<div id="device-success" class="notice notice-success" hidden></div>
<dialog id="device-dialog" class="modal-dialog" aria-labelledby="device-dialog-title">
    <div class="modal-heading">
        <div><p class="eyebrow">ALTA DE EQUIPO</p><h2 id="device-dialog-title">Registrar dispositivo</h2></div>
        <button class="icon-button" type="button" id="close-device-dialog" aria-label="Cerrar formulario">×</button>
    </div>
    <div id="device-dialog-message" class="notice notice-error" role="alert" hidden></div>
    <form id="device-quick-form" class="form-grid">
        <label class="field"><span>Nombre *</span><input name="nombre" required maxlength="120" placeholder="Ej. Router principal"></label>
        <label class="field"><span>Dirección IP *</span><input name="ip" required maxlength="45" placeholder="192.168.1.1" inputmode="decimal"></label>
        <label class="field"><span>Tipo *</span><select name="tipo" required><option value="">Seleccionar tipo</option><option value="router">Router</option><option value="switch">Switch</option><option value="firewall">Firewall</option><option value="punto de acceso">Punto de acceso</option><option value="servidor">Servidor</option><option value="otro">Otro</option></select></label>
        <label class="field"><span>Ubicación</span><input name="ubicacion" maxlength="120" placeholder="Ej. Sala de servidores"></label>
        <div class="modal-actions"><a class="text-link" href="/dispositivos/nuevo">Más opciones</a><button class="button button-secondary" type="button" id="cancel-device-dialog">Cancelar</button><button class="button button-primary" type="submit">＋ Registrar dispositivo</button></div>
    </form>
</dialog>
<section class="panel inventory-toolbar">
    <label class="search-field"><span aria-hidden="true">⌕</span><input id="device-search" type="search" placeholder="Código, nombre o dirección IP" autocomplete="off"></label>
    <label class="select-filter"><span class="sr-only">Filtrar por tipo</span>
        <select id="device-type-filter"><option value="">Todos los tipos</option></select>
    </label>
    <label class="select-filter"><span class="sr-only">Filtrar por estado</span>
        <select id="device-status-filter"><option value="">Todos los estados</option><option value="en_linea">Activo</option><option value="advertencia">Advertencia</option><option value="fuera_de_linea">En falla</option><option value="desconocido">Sin monitorear</option></select>
    </label>
    <span class="toolbar-count" id="device-count">Cargando inventario…</span>
</section>
<div id="devices-error" class="notice notice-error" hidden></div>
<section id="device-grid" class="device-grid" aria-live="polite">
    <div class="panel empty-state"><span class="loader"></span><p>Cargando dispositivos…</p></div>
</section>
<?php require __DIR__ . '/layout/footer.php'; ?>
