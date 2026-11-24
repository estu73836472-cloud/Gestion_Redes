<?php
$activePage = $activePage ?? '';
$pageEyebrow = $pageEyebrow ?? 'CONSOLA DE RED · SEDE PRINCIPAL';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titulo ?? 'Optisys', ENT_QUOTES, 'UTF-8') ?> · Optisys</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="/assets/js/main.js" defer></script>
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="/" aria-label="Optisys, inicio">
            <span class="brand-mark">↗</span><span>Optisys</span>
        </a>
        <nav class="primary-nav" aria-label="Navegación principal">
            <a class="<?= $activePage === 'inicio' ? 'active' : '' ?>" href="/"><span>⌂</span> Inicio</a>
            <a class="<?= $activePage === 'dispositivos' ? 'active' : '' ?>" href="/dispositivos"><span>▤</span> Dispositivos</a>
            <a class="<?= $activePage === 'monitoreo' ? 'active' : '' ?>" href="/monitoreo"><span>⌁</span> Monitoreo</a>
            <a class="<?= $activePage === 'incidencias' ? 'active' : '' ?>" href="/incidencias"><span>⚑</span> Incidencias</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="sidebar-network"><span class="live-dot"></span> Red corporativa</div>
            <span>NEXO · CONSOLA DE RED</span>
        </div>
    </aside>
    <main class="main-shell">
        <div class="prototype-bar">
            <span><?= htmlspecialchars($pageEyebrow, ENT_QUOTES, 'UTF-8') ?></span>
            <span>GESTIÓN DE INFRAESTRUCTURA</span>
        </div>
        <header class="topbar">
            <div class="workspace-label">Espacio de trabajo <strong>Sede principal</strong></div>
            <div class="profile">
                <span class="sample-label">Vista conectada a datos del sistema</span>
                <span class="avatar">AM</span>
                <span><strong>Administración de red</strong><small>Operador</small></span>
            </div>
        </header>
        <div class="page-content">
