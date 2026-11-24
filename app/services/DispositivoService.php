<?php

namespace App\Services;

use App\Repositories\DispositivoRepository;
use InvalidArgumentException;

class DispositivoService
{
    private DispositivoRepository $repository;

    public function __construct(?DispositivoRepository $repository = null)
    {
        $this->repository = $repository ?? new DispositivoRepository();
    }

    public function obtenerTodos(): array
    {
        return $this->repository->findAll();
    }

    public function obtenerPorId(int $id): ?array
    {
        $dispositivo = $this->repository->findById($id);
        if (!$dispositivo) {
            throw new InvalidArgumentException("El dispositivo con ID {$id} no fue encontrado.");
        }
        return $dispositivo;
    }

    /**
     * HU1: Registrar Dispositivo
     */
    public function registrar(array $data): array
    {
        $this->validarDatos($data);

        // Validar unicidad de IP
        $existente = $this->repository->findByIp($data['ip']);
        if ($existente) {
            throw new InvalidArgumentException("La dirección IP '{$data['ip']}' ya está asignada al dispositivo '{$existente['nombre']}'.");
        }

        // Asignar estado por defecto si no viene
        if (empty($data['estado'])) {
            $data['estado'] = 'desconocido';
        }

        $id = $this->repository->create($data);
        return $this->repository->findById($id);
    }

    /**
     * HU2: Actualizar Dispositivo
     */
    public function actualizar(int $id, array $data): array
    {
        $dispositivoActual = $this->obtenerPorId($id);
        $this->validarDatos($data);

        // Si la IP cambió, verificar que la nueva IP no esté tomada por otro dispositivo
        if ($data['ip'] !== $dispositivoActual['ip']) {
            $existente = $this->repository->findByIp($data['ip']);
            if ($existente && (int)$existente['id'] !== $id) {
                throw new InvalidArgumentException("La dirección IP '{$data['ip']}' ya está en uso por otro dispositivo.");
            }
        }

        // Mantener estado actual si no se proporciona nuevo
        if (empty($data['estado'])) {
            $data['estado'] = $dispositivoActual['estado'];
        }
        if (!array_key_exists('mac', $data)) {
            $data['mac'] = $dispositivoActual['mac'];
        }

        $this->repository->update($id, $data);
        return $this->repository->findById($id);
    }

    public function eliminar(int $id): bool
    {
        $this->obtenerPorId($id);
        return $this->repository->delete($id);
    }

    public function obtenerMetricas(): array
    {
        return $this->repository->getMetricas();
    }

    private function validarDatos(array $data): void
    {
        if (empty(trim($data['nombre'] ?? ''))) {
            throw new InvalidArgumentException("El nombre del dispositivo es obligatorio.");
        }

        if (empty(trim($data['ip'] ?? ''))) {
            throw new InvalidArgumentException("La dirección IP es obligatoria.");
        }

        $ip = trim($data['ip']);
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException("La dirección IP '{$ip}' no tiene un formato válido (IPv4 o IPv6).");
        }

        if (!empty($data['mac'])) {
            $mac = trim($data['mac']);
            if (!filter_var($mac, FILTER_VALIDATE_MAC)) {
                throw new InvalidArgumentException("La dirección MAC '{$mac}' no tiene un formato válido (ej: 00:1A:2B:3C:4D:5E).");
            }
        }
    }
}
