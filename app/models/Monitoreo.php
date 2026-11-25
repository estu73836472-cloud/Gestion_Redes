<?php

namespace App\Models;

class Monitoreo
{
    public ?string $id;
    public string $dispositivo_id;
    public string $estado;
    public ?int $latencia_ms;
    public ?string $detalles;
    public ?float $uso_cpu;
    public ?float $uso_memoria;
    public ?string $fecha_monitoreo;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (string)$data['id'] : null;
        $this->dispositivo_id = (string)($data['dispositivo_id'] ?? '');
        $this->estado = $data['estado'] ?? 'desconocido';
        $this->latencia_ms = isset($data['latencia_ms']) ? (int)$data['latencia_ms'] : null;
        $this->detalles = $data['detalles'] ?? null;
        $this->uso_cpu = isset($data['uso_cpu']) ? (float)$data['uso_cpu'] : null;
        $this->uso_memoria = isset($data['uso_memoria']) ? (float)$data['uso_memoria'] : null;
        $this->fecha_monitoreo = $data['fecha_monitoreo'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'dispositivo_id' => $this->dispositivo_id,
            'estado' => $this->estado,
            'latencia_ms' => $this->latencia_ms,
            'detalles' => $this->detalles,
            'uso_cpu' => $this->uso_cpu,
            'uso_memoria' => $this->uso_memoria,
            'fecha_monitoreo' => $this->fecha_monitoreo,
        ];
    }
}
