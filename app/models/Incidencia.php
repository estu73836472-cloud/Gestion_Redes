<?php

namespace App\Models;

class Incidencia
{
    public ?string $id;
    public string $dispositivo_id;
    public string $titulo;
    public string $descripcion;
    public string $gravedad;
    public string $estado;
    public ?string $fecha_reporte;
    public ?string $fecha_cierre;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (string)$data['id'] : null;
        $this->dispositivo_id = (string)($data['dispositivo_id'] ?? '');
        $this->titulo = trim($data['titulo'] ?? '');
        $this->descripcion = trim($data['descripcion'] ?? '');
        $this->gravedad = $data['gravedad'] ?? 'media';
        $this->estado = $data['estado'] ?? 'abierta';
        $this->fecha_reporte = $data['fecha_reporte'] ?? null;
        $this->fecha_cierre = $data['fecha_cierre'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'dispositivo_id' => $this->dispositivo_id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'gravedad' => $this->gravedad,
            'estado' => $this->estado,
            'fecha_reporte' => $this->fecha_reporte,
            'fecha_cierre' => $this->fecha_cierre,
        ];
    }
}
