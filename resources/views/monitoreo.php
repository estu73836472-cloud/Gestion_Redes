<?php require __DIR__ . '/layout/header.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Historial de monitoreo</h1>
        <p class="text-secondary mb-0">Resultados recientes de conexión y rendimiento registrados.</p>
    </div>
    <button id="refreshMonitoring" class="btn btn-outline-primary" type="button">
        <i class="bi bi-arrow-clockwise me-1" aria-hidden="true"></i>Actualizar
    </button>
</div>
<div id="monitoringAlerts" aria-live="polite"></div>
<section class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th scope="col">ID Monitoreo</th>
                    <th scope="col">Dispositivo</th>
                    <th scope="col">Conexión</th>
                    <th scope="col">Latencia (ms)</th>
                    <th scope="col">Uso CPU (%)</th>
                    <th scope="col">Uso Memoria (%)</th>
                    <th scope="col">Fecha/Hora</th>
                </tr>
                </thead>
                <tbody id="tablaMonitoreo">
                <tr><td colspan="7" class="text-center text-secondary py-4">Cargando historial...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php $script = 'monitoreo.js'; require __DIR__ . '/layout/footer.php'; ?>
