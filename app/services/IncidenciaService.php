<?php

namespace App\Services;

use App\Repositories\DispositivoRepository;
use App\Repositories\IncidenciaRepository;
use InvalidArgumentException;

class IncidenciaService
{
    private IncidenciaRepository $incidenciaRepo;
    private DispositivoRepository $dispositivoRepo;

    public function __construct(
        ?IncidenciaRepository $incidenciaRepo = null,
        ?DispositivoRepository $dispositivoRepo = null
    ) {
        $this->incidenciaRepo = $incidenciaRepo ?? new IncidenciaRepository();
        $this->dispositivoRepo = $dispositivoRepo ?? new DispositivoRepository();
    }

    public function obtenerTodas(): array
    {
        return $this->incidenciaRepo->findAll();
    }

    public function obtenerPorId(string $id): ?array
    {
        $incidencia = $this->incidenciaRepo->findById($id);
        if (!$incidencia) {
            throw new InvalidArgumentException("La incidencia con ID {$id} no fue encontrada.");
        }
        return $incidencia;
    }

    public function obtenerPorDispositivo(string $dispositivoId): array
    {
        return $this->incidenciaRepo->findPorDispositivo($dispositivoId);
    }

    /**
     * HU4: Registro de Incidencias
     */
    public function registrar(array $data): array
    {
        if (empty($data['dispositivo_id'])) {
            throw new InvalidArgumentException("Debe seleccionar un dispositivo afectado.");
        }

        $dispId = (string)$data['dispositivo_id'];
        $dispositivo = $this->dispositivoRepo->findById($dispId);
        if (!$dispositivo) {
            throw new InvalidArgumentException("El dispositivo seleccionado (ID: {$dispId}) no existe.");
        }

        if (empty(trim($data['titulo'] ?? ''))) {
            throw new InvalidArgumentException("El título de la incidencia es obligatorio.");
        }

        if (empty(trim($data['descripcion'] ?? ''))) {
            throw new InvalidArgumentException("La descripción de la incidencia es obligatoria.");
        }

        $gravedadesPermitidas = ['baja', 'media', 'alta'];
        $gravedad = strtolower(trim($data['gravedad'] ?? 'media'));
        if (!in_array($gravedad, $gravedadesPermitidas, true)) {
            throw new InvalidArgumentException('La gravedad debe ser baja, media o alta.');
        }
        $data['gravedad'] = $gravedad;

        if (empty($data['estado'])) {
            $data['estado'] = 'abierta';
        }

        $id = $this->incidenciaRepo->create($data);
        return $this->incidenciaRepo->findById($id);
    }

    public function actualizarEstado(string $id, string $estado): array
    {
        $this->obtenerPorId($id);

        $estadosValidos = ['abierta', 'en_proceso', 'resuelta'];
        if (!in_array($estado, $estadosValidos, true)) {
            throw new InvalidArgumentException("Estado de incidencia inválido. Permitidos: " . implode(', ', $estadosValidos));
        }

        $this->incidenciaRepo->cambiarEstado($id, $estado);
        return $this->incidenciaRepo->findById($id);
    }

    public function obtenerMetricas(): array
    {
        return $this->incidenciaRepo->getMetricas();
    }
}
