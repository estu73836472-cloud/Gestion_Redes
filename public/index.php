<?php

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

// Autoloader generado por Composer
$autoloadFile = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($autoloadFile)) {
    http_response_code(500);
    echo "<h1>Error: Dependencias no instaladas</h1>";
    echo "<p>Por favor ejecuta <code>composer install</code> en la raíz del proyecto para descargar Slim 4 y las dependencias.</p>";
    exit;
}

require $autoloadFile;

// Cargar variables de entorno desde .env si existe
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

// Crear la instancia de la aplicación Slim 4
$app = AppFactory::create();

// Detectar y configurar el BasePath automáticamente (para entornos como XAMPP)
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = rtrim($scriptDir, '/');
if (!empty($basePath)) {
    $app->setBasePath($basePath);
}

// Middleware para parsear cuerpos de peticiones JSON y formularios (POST / PUT)
$app->addBodyParsingMiddleware();

// Middleware de Enrutamiento
$app->addRoutingMiddleware();

// Middleware de Manejo de Errores
$displayErrors = filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN);
$errorMiddleware = $app->addErrorMiddleware($displayErrors, true, true);

// Habilitar cabeceras CORS para facilitar pruebas con APIs
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Registrar Rutas Web (Vistas con Bootstrap 5) y Rutas API (REST)
(require __DIR__ . '/../routes/web.php')($app);
(require __DIR__ . '/../routes/api.php')($app);

// Ejecutar la aplicación
$app->run();
