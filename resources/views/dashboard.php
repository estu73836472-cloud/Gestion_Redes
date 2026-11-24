<?php require __DIR__ . '/layout/header.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Panel principal</h1>
        <p class="text-secondary mb-0">Resumen del estado de la red e incidencias.</p>
    </div>
    <button id="refreshDashboard" class="btn btn-outline-primary" type="button">
        <i class="bi bi-arrow-clockwise me-1" aria-hidden="true"></i>Actualizar
    </button>
</div>
<div id="dashboardAlerts" aria-live="polite"></div>
<section class="row g-3" aria-label="Métricas de red">
    <div class="col-sm-6 col-xl-3">
        <article class="card h-100 shadow-sm">
            <div class="card-body">
                <p class="text-secondary mb-1">Total de dispositivos</p>
                <p id="deviceTotal" class="display-6 fw-semibold mb-0">—</p>
            </div>
        </article>
    </div>
    <div class="col-sm-6 col-xl-3">
        <article class="card h-100 shadow-sm">
            <div class="card-body">
                <p class="text-secondary mb-1">En línea</p>
                <p id="deviceOnline" class="display-6 fw-semibold text-success mb-0">—</p>
            </div>
        </article>
    </div>
    <div class="col-sm-6 col-xl-3">
        <article class="card h-100 shadow-sm">
            <div class="card-body">
                <p class="text-secondary mb-1">Fuera de línea</p>
                <p id="deviceOffline" class="display-6 fw-semibold text-danger mb-0">—</p>
            </div>
        </article>
    </div>
    <div class="col-sm-6 col-xl-3">
        <article class="card h-100 shadow-sm">
            <div class="card-body">
                <p class="text-secondary mb-1">Incidencias abiertas</p>
                <p id="openIncidents" class="display-6 fw-semibold text-warning mb-0">—</p>
            </div>
        </article>
    </div>
</section>
<section class="card shadow-sm mt-4">
    <div class="card-body d-flex flex-wrap justify-content-between gap-2">
        <div>
            <h2 class="h5 mb-1">Último escaneo</h2>
            <p id="lastScanAt" class="text-secondary mb-0">Todavía no hay registros de monitoreo.</p>
        </div>
        <a href="/monitoreo" class="btn btn-sm btn-outline-secondary align-self-center">Ver historial</a>
    </div>
</section>
<?php $script = 'dashboard.js'; require __DIR__ . '/layout/footer.php'; ?>
