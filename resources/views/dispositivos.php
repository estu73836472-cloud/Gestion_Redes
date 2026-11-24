<?php require __DIR__ . '/layout/header.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Dispositivos</h1>
        <p class="text-secondary mb-0">Registra, actualiza y comprueba equipos de red.</p>
    </div>
    <button id="scanNetwork" class="btn btn-primary" type="button">
        <i class="bi bi-broadcast me-1" aria-hidden="true"></i>Escanear toda la red
    </button>
</div>
<div id="deviceAlerts" aria-live="polite"></div>
<section class="card shadow-sm mb-4">
    <div class="card-body">
        <h2 id="deviceFormTitle" class="h5 mb-3">Registrar dispositivo</h2>
        <form id="deviceForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label" for="deviceName">Nombre</label>
                    <input class="form-control" id="deviceName" name="nombre" maxlength="100" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="deviceIp">IP</label>
                    <input class="form-control" id="deviceIp" name="ip" inputmode="url" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="deviceType">Tipo</label>
                    <input class="form-control" id="deviceType" name="tipo" maxlength="30" value="router" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="deviceLocation">Ubicación</label>
                    <input class="form-control" id="deviceLocation" name="ubicacion" maxlength="100">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button id="saveDevice" class="btn btn-success w-100" type="submit" aria-label="Guardar dispositivo">
                        <i class="bi bi-check-lg" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <button id="cancelDeviceEdit" class="btn btn-sm btn-link px-0 mt-2 d-none" type="button">Cancelar edición</button>
        </form>
    </div>
</section>
<section class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Listado de dispositivos</h2>
            <button id="refreshDevices" class="btn btn-sm btn-outline-secondary" type="button">Actualizar tabla</button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">IP</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Ubicación</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Latencia</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody id="tablaDispositivos">
                <tr><td colspan="8" class="text-center text-secondary py-4">Cargando dispositivos...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php $script = 'dispositivos.js'; require __DIR__ . '/layout/footer.php'; ?>
