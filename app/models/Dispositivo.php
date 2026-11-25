<?php

namespace App\Models;

class Dispositivo
{
    public ?string $id;
    public string $nombre;
    public string $ip;
    public ?string $mac;
    public string $tipo;
    public ?string $ubicacion;
    public string $estado;
    public ?string $ultimo_monitoreo;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (string)$data['id'] : null;
        $this->nombre = trim($data['nombre'] ?? '');
        $this->ip = trim($data['ip'] ?? '');
        $this->mac = !empty($data['mac']) ? trim($data['mac']) : null;
        $this->tipo = $data['tipo'] ?? 'Otro';
        $this->ubicacion = !empty($data['ubicacion']) ? trim($data['ubicacion']) : null;
        $this->estado = $data['estado'] ?? 'desconocido';
        $this->ultimo_monitoreo = $data['ultimo_monitoreo'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'ip' => $this->ip,
            'mac' => $this->mac,
            'tipo' => $this->tipo,
            'ubicacion' => $this->ubicacion,
            'estado' => $this->estado,
            'ultimo_monitoreo' => $this->ultimo_monitoreo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
