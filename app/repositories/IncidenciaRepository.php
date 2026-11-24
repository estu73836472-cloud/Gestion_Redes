<?php

namespace App\Repositories;

use Config\Database;
use PDO;

class IncidenciaRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT i.*, d.nombre as dispositivo_nombre, d.ip as dispositivo_ip, d.tipo as dispositivo_tipo 
                FROM incidencias i 
                INNER JOIN dispositivos d ON d.id = i.dispositivo_id 
                ORDER BY i.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT i.*, d.nombre as dispositivo_nombre, d.ip as dispositivo_ip 
                FROM incidencias i 
                INNER JOIN dispositivos d ON d.id = i.dispositivo_id 
                WHERE i.id = :id 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findPorDispositivo(int $dispositivoId): array
    {
        $sql = "SELECT * FROM incidencias 
                WHERE dispositivo_id = :dispositivo_id 
                ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['dispositivo_id' => $dispositivoId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO incidencias (dispositivo_id, titulo, descripcion, gravedad, estado) 
                VALUES (:dispositivo_id, :titulo, :descripcion, :gravedad, :estado)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'dispositivo_id' => $data['dispositivo_id'],
            'titulo'         => $data['titulo'],
            'descripcion'    => $data['descripcion'],
            'gravedad'       => $data['gravedad'] ?? 'media',
            'estado'         => $data['estado'] ?? 'abierta'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE incidencias 
                SET titulo = :titulo, 
                    descripcion = :descripcion, 
                    gravedad = :gravedad, 
                    estado = :estado,
                    fecha_cierre = :fecha_cierre 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'           => $id,
            'titulo'       => $data['titulo'],
            'descripcion'  => $data['descripcion'],
            'gravedad'     => $data['gravedad'],
            'estado'       => $data['estado'],
            'fecha_cierre' => $data['fecha_cierre'] ?? null
        ]);
    }

    public function cambiarEstado(int $id, string $estado): bool
    {
        $fechaCierre = ($estado === 'resuelta') ? date('Y-m-d H:i:s') : null;
        $sql = "UPDATE incidencias 
                SET estado = :estado, fecha_cierre = :fecha_cierre 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'           => $id,
            'estado'       => $estado,
            'fecha_cierre' => $fechaCierre
        ]);
    }

    public function getMetricas(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    COALESCE(SUM(CASE WHEN estado = 'abierta' THEN 1 ELSE 0 END), 0) as abiertas,
                    COALESCE(SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END), 0) as en_proceso,
                    COALESCE(SUM(CASE WHEN estado = 'resuelta' THEN 1 ELSE 0 END), 0) as resueltas
                FROM incidencias";
        $stmt = $this->db->query($sql);
        return $stmt->fetch() ?: [
            'total' => 0,
            'abiertas' => 0,
            'en_proceso' => 0,
            'resueltas' => 0
        ];
    }
}
