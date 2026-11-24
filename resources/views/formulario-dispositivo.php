<?php
$activePage = 'dispositivos';
$isEditing = is_array($dispositivo ?? null);
$pageEyebrow = $isEditing ? 'HU02 · ACTUALIZAR DISPOSITIVO' : 'HU01 · REGISTRAR DISPOSITIVO';
$id = $isEditing ? (int)$dispositivo['id'] : null;
$value = static fn(string $key): string => htmlspecialchars((string)($dispositivo[$key] ?? ''), ENT_QUOTES, 'UTF-8');
require __DIR__ . '/layout/header.php';
?>
<a class="back-link" href="<?= $isEditing ? '/dispositivos/' . $id : '/dispositivos' ?>">← <?= $isEditing ? 'Volver al detalle' : 'Volver a dispositivos' ?></a>
<section class="page-heading compact-heading">
    <div><p class="eyebrow"><?= $isEditing ? 'ACTUALIZACIÓN DE INVENTARIO' : 'ALTA DE EQUIPO' ?></p><h1><?= $isEditing ? 'Editar dispositivo' : 'Registrar dispositivo' ?></h1><p class="page-lead"><?= $isEditing ? 'Revisa la información y guarda los cambios.' : 'Añade un equipo al inventario y centraliza su información técnica.' ?></p></div>
</section>
<div id="device-form-message" class="notice" role="alert" hidden></div>
<form id="device-form" class="form-layout" data-device-id="<?= $id ?? '' ?>">
    <div class="form-main">
        <section class="panel form-section">
            <div class="form-section-heading"><h2>Identificación y red</h2><p>Los campos con * son obligatorios.</p></div>
            <div class="form-grid">
                <label class="field"><span>Nombre / identificador *</span><input name="nombre" required maxlength="120" value="<?= $value('nombre') ?>" placeholder="Ej. Punto de acceso — Sala 3"><small>Nombre fácil de reconocer en el inventario.</small></label>
                <label class="field"><span>Tipo de dispositivo *</span><select name="tipo" required><option value="">Seleccionar tipo</option>
                    <?php foreach (['Switch', 'Router', 'Firewall', 'Punto de acceso', 'Servidor', 'Otro'] as $tipo): ?>
                    <option value="<?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?>" <?= ($dispositivo['tipo'] ?? '') === $tipo ? 'selected' : '' ?>><?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select></label>
                <label class="field"><span>Dirección IPv4 *</span><input name="ip" required maxlength="45" value="<?= $value('ip') ?>" placeholder="Ej. 192.168.1.21" inputmode="decimal"><small>Usa una dirección IP válida y disponible.</small></label>
                <label class="field"><span>Dirección MAC</span><input name="mac" maxlength="17" value="<?= $value('mac') ?>" placeholder="00:1A:2B:3C:4D:5E"><small>Opcional. Formato hexadecimal.</small></label>
            </div>
        </section>
        <section class="panel form-section">
            <div class="form-section-heading"><h2>Contexto y asignación</h2><p>Ubica el dispositivo y define su estado operativo.</p></div>
            <div class="form-grid">
                <label class="field"><span>Ubicación</span><input name="ubicacion" maxlength="120" value="<?= $value('ubicacion') ?>" placeholder="Ej. Sala de servidores"></label>
                <label class="field"><span>Estado operativo</span><select name="estado"><option value="desconocido" <?= ($dispositivo['estado'] ?? 'desconocido') === 'desconocido' ? 'selected' : '' ?>>Sin monitorear</option><option value="en_linea" <?= ($dispositivo['estado'] ?? '') === 'en_linea' ? 'selected' : '' ?>>Activo</option><option value="advertencia" <?= ($dispositivo['estado'] ?? '') === 'advertencia' ? 'selected' : '' ?>>Advertencia</option><option value="fuera_de_linea" <?= ($dispositivo['estado'] ?? '') === 'fuera_de_linea' ? 'selected' : '' ?>>En falla</option></select><small>Se actualizará al ejecutar un monitoreo.</small></label>
            </div>
        </section>
        <div class="form-actions"><span class="muted">Revisa los datos antes de guardar.</span><a class="button button-secondary" href="<?= $isEditing ? '/dispositivos/' . $id : '/dispositivos' ?>">Cancelar</a><button class="button button-primary" type="submit"><?= $isEditing ? '✓ Guardar cambios' : '＋ Registrar dispositivo' ?></button></div>
    </div>
    <aside class="form-aside">
        <section class="panel tip-card"><h2>Antes de <?= $isEditing ? 'guardar' : 'registrar' ?></h2><p><?= $isEditing ? 'La dirección IP debe ser única dentro del inventario.' : 'Cada equipo necesita un nombre y una IP únicos dentro del inventario.' ?></p><ul><li>Completa los campos obligatorios.</li><li>Usa una dirección IPv4 válida.</li><li>Verifica que la IP esté disponible.</li></ul></section>
        <section class="info-card"><strong>Un registro, varias vistas</strong><p>El equipo quedará disponible en inventario, monitoreo e incidencias.</p></section>
    </aside>
</form>
<?php require __DIR__ . '/layout/footer.php'; ?>
