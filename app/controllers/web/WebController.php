<?php

namespace App\Controllers\Web;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class WebController
{
    /**
     * Vista de Dashboard general
     */
    public function dashboard(Request $request, Response $response): Response
    {
        return $this->render($response, 'dashboard', [
            'titulo' => 'Panel Principal - Gestión de Red'
        ]);
    }

    /**
     * Vista para HU1 y HU2: Registrar y Actualizar Dispositivos
     */
    public function dispositivos(Request $request, Response $response): Response
    {
        return $this->render($response, 'dispositivos', [
            'titulo' => 'Gestión de Dispositivos de Red'
        ]);
    }

    /**
     * Vista para HU3: Monitoreo en Tiempo Real
     */
    public function monitoreo(Request $request, Response $response): Response
    {
        return $this->render($response, 'monitoreo', [
            'titulo' => 'Historial de Monitoreo'
        ]);
    }

    /**
     * Vista para HU4: Registro y Control de Incidencias
     */
    public function incidencias(Request $request, Response $response): Response
    {
        return $this->render($response, 'incidencias', [
            'titulo' => 'Registro de Incidencias'
        ]);
    }

    /**
     * Renderizador simple de plantillas PHP
     */
    private function render(Response $response, string $vista, array $datos = []): Response
    {
        $archivoVista = __DIR__ . "/../../../resources/views/{$vista}.php";

        if (!file_exists($archivoVista)) {
            $response->getBody()->write("Error: Vista '{$vista}' no encontrada.");
            return $response->withStatus(404);
        }

        // Extraer variables para la vista
        extract($datos);

        ob_start();
        require $archivoVista;
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
    }
}
