<?php
$titulo = $titulo ?? 'Gestión de Redes';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="/">Gestión de Redes</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Abrir navegación">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavigation">
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="/">Dashboard</a>
                <a class="nav-link" href="/dispositivos">Dispositivos</a>
                <a class="nav-link" href="/incidencias">Incidencias</a>
                <a class="nav-link" href="/monitoreo">Monitoreo</a>
            </div>
            <span id="apiHealthBadge" class="badge text-bg-secondary" role="status">Comprobando API...</span>
        </div>
    </div>
</nav>
<main class="container py-4">
