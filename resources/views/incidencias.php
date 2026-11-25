<?php require __DIR__ . '/layout/header.php'; ?>
<div class="mb-4">
    <h1 class="h2 mb-1">Incidencias</h1>
    <p class="text-secondary mb-0">Reporta fallas y da seguimiento a su resolución.</p>
</div>
<div id="incidentAlerts" aria-live="polite"></div>
<section class="card shadow-sm mb-4">
    <div class="card-body">
        <h2 class="h5 mb-3">Reportar incidencia</h2>
        <form id="incidentForm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="incidentDevice">Dispositivo</label>
                    <select class="form-select" id="incidentDevice" name="dispositivo_id" required>
                        <option value="">Cargando dispositivos...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="incidentTitle">Título</label>
                    <input class="form-control" id="incidentTitle" name="titulo" maxlength="150" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="incidentSeverity">Gravedad</label>
                    <select class="form-select" id="incidentSeverity" name="gravedad" required>
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label" for="incidentDescription">Descripción</label>
                    <textarea class="form-control" id="incidentDescription" name="descripcion" rows="3" required></textarea>
                </div>
                <div class="col-12">
                    <button class="btn btn-success" type="submit">Registrar incidencia</button>
                </div>
            </div>
        </form>
    </div>
</section>
<section class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Incidencias registradas</h2>
            <button id="refreshIncidents" class="btn btn-sm btn-outline-secondary" type="button">Actualizar</button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Dispositivo</th>
                    <th scope="col">Título</th>
                    <th scope="col">Gravedad</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Reportada</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody id="tablaIncidencias">
                <tr><td colspan="7" class="text-center text-secondary py-4">Cargando incidencias...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php $script = 'incidencias.js'; require __DIR__ . '/layout/footer.php'; ?>
