<?php
$titulo = $titulo ?? 'Gestión de Redes';

// Detectar ruta base para entornos en subcarpetas (como XAMPP)
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = rtrim($scriptDir, '/');

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if (!empty($baseUrl) && str_starts_with($uri, $baseUrl)) {
    $rutaActual = substr($uri, strlen($baseUrl)) ?: '/';
} else {
    $rutaActual = $uri;
}

$seccionActiva = match ($rutaActual) {
    '/dispositivos' => 'dispositivos',
    '/monitoreo' => 'monitoreo',
    '/incidencias' => 'incidencias',
    default => 'dashboard',
};
$enlacesNavegacion = [
    ['id' => 'dashboard', 'href' => ($baseUrl ?: '') . '/', 'icono' => 'bi-grid-1x2', 'texto' => 'Inicio'],
    ['id' => 'dispositivos', 'href' => $baseUrl . '/dispositivos', 'icono' => 'bi-hdd-network', 'texto' => 'Dispositivos'],
    ['id' => 'monitoreo', 'href' => $baseUrl . '/monitoreo', 'icono' => 'bi-activity', 'texto' => 'Monitoreo'],
    ['id' => 'incidencias', 'href' => $baseUrl . '/incidencias', 'icono' => 'bi-exclamation-triangle', 'texto' => 'Incidencias'],
];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>/assets/css/app.css?v=<?= time() ?>" rel="stylesheet">
    <script>window.BASE_URL = "<?= $baseUrl ?>";</script>
</head>
<body class="app-body">
<div class="context-strip">
    <span><strong>GESTIÓN DE REDES</strong><span class="context-separator">/</span> CONSOLA DE OPERACIÓN</span>
    <span class="context-environment">ENTORNO LOCAL <span class="environment-dot"></span> ACTIVO</span>
</div>

<div class="app-shell">
    <aside class="app-sidebar" id="appSidebar" aria-label="Navegación principal">
        <a class="sidebar-brand" href="<?= $baseUrl ?: '/' ?>" aria-label="Gestión de Redes, inicio">
            <span class="brand-mark"><i class="bi bi-diagram-3-fill" aria-hidden="true"></i></span>
            <span class="brand-copy"><strong>Redes</strong><small>CONSOLE</small></span>
        </a>

        <div class="sidebar-caption">ESPACIO DE TRABAJO</div>
        <nav class="sidebar-navigation">
            <?php foreach ($enlacesNavegacion as $enlace): ?>
                <a class="sidebar-link<?= $seccionActiva === $enlace['id'] ? ' active' : '' ?>"
                   href="<?= htmlspecialchars($enlace['href'], ENT_QUOTES, 'UTF-8') ?>"
                   <?= $seccionActiva === $enlace['id'] ? 'aria-current="page"' : '' ?>
                   title="<?= htmlspecialchars($enlace['texto'], ENT_QUOTES, 'UTF-8') ?>">
                    <i class="bi <?= htmlspecialchars($enlace['icono'], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></i>
                    <span><?= htmlspecialchars($enlace['texto'], ENT_QUOTES, 'UTF-8') ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="sidebar-bottom">
            <div class="sidebar-network">
                <span class="network-icon"><i class="bi bi-diagram-3" aria-hidden="true"></i></span>
                <span><strong>Red corporativa</strong><small>Panel de administración</small></span>
            </div>
            <div class="sidebar-version">GESTIÓN DE INFRAESTRUCTURA <span>v1.0</span></div>
        </div>
    </aside>

    <button class="sidebar-backdrop" id="sidebarBackdrop" type="button" aria-label="Cerrar menú"></button>

    <div class="workspace">
        <header class="workspace-topbar">
            <div class="topbar-leading">
                <button class="sidebar-toggle" id="sidebarToggle" type="button"
                        aria-label="Contraer menú" aria-controls="appSidebar" aria-expanded="true">
                    <i class="bi bi-layout-sidebar-inset" aria-hidden="true"></i>
                </button>
                <div class="workspace-location">
                    <span>Espacio de trabajo</span>
                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                    <strong>Red principal</strong>
                </div>
            </div>
            <div class="topbar-trailing">
                <span id="apiHealthBadge" class="badge text-bg-secondary" role="status">Comprobando API...</span>
                <span class="topbar-divider" aria-hidden="true"></span>
                <div class="user-summary">
                    <span class="user-avatar">AM</span>
                    <span class="user-copy"><strong>Administrador</strong><small>Red local</small></span>
                </div>
            </div>
        </header>

        <main class="main-content">
