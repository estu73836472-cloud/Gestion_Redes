<?php

use Slim\App;
use App\Controllers\Web\WebController;

return function (App $app) {
    $app->get('/', [WebController::class, 'dashboard']);
    $app->get('/dispositivos', [WebController::class, 'dispositivos']);
    $app->get('/dispositivos/nuevo', [WebController::class, 'nuevoDispositivo']);
    $app->get('/dispositivos/{id:[0-9]+}/editar', [WebController::class, 'editarDispositivo']);
    $app->get('/dispositivos/{id:[0-9]+}', [WebController::class, 'detalleDispositivo']);
    $app->get('/monitoreo', [WebController::class, 'monitoreo']);
    $app->get('/incidencias', [WebController::class, 'incidencias']);
};
