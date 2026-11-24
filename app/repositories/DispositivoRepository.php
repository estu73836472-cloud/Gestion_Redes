<?php

namespace App\Repositories;

use Config\Database;
use PDO;

class DispositivoRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM dispositivos ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM dispositivos WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findByIp(string $ip): ?array
    {
        $sql = "SELECT * FROM dispositivos WHERE ip = :ip LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ip' => $ip]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO dispositivos (nombre, ip, mac, tipo, ubicacion, estado) 
                VALUES (:nombre, :ip, :mac, :tipo, :ubicacion, :estado)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nombre'    => $data['nombre'],
            'ip'        => $data['ip'],
            'mac'       => $data['mac'] ?? null,
            'tipo'      => $data['tipo'] ?? 'Otro',
            'ubicacion' => $data['ubicacion'] ?? null,
            'estado'    => $data['estado'] ?? 'desconocido'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE dispositivos 
                SET nombre = :nombre, 
                    ip = :ip, 
                    mac = :mac, 
                    tipo = :tipo, 
                    ubicacion = :ubicacion,
                    estado = :estado 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'        => $id,
            'nombre'    => $data['nombre'],
            'ip'        => $data['ip'],
            'mac'       => $data['mac'] ?? null,
            'tipo'      => $data['tipo'] ?? 'Otro',
            'ubicacion' => $data['ubicacion'] ?? null,
            'estado'    => $data['estado'] ?? 'desconocido'
        ]);
    }

    public function updateEstado(int $id, string $estado, ?string $ultimoMonitoreo = null): bool
    {
        $ultimoMonitoreo = $ultimoMonitoreo ?? date('Y-m-d H:i:s');
        $sql = "UPDATE dispositivos 
                SET estado = :estado, ultimo_monitoreo = :ultimo_monitoreo 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'               => $id,
            'estado'           => $estado,
            'ultimo_monitoreo' => $ultimoMonitoreo
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM dispositivos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function getMetricas(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    COALESCE(SUM(CASE WHEN estado = 'en_linea' THEN 1 ELSE 0 END), 0) as en_linea,
                    COALESCE(SUM(CASE WHEN estado = 'fuera_de_linea' THEN 1 ELSE 0 END), 0) as fuera_de_linea,
                    COALESCE(SUM(CASE WHEN estado = 'advertencia' THEN 1 ELSE 0 END), 0) as advertencia,
                    COALESCE(SUM(CASE WHEN estado = 'desconocido' THEN 1 ELSE 0 END), 0) as desconocido
                FROM dispositivos";
        $stmt = $this->db->query($sql);
        return $stmt->fetch() ?: [
            'total' => 0,
            'en_linea' => 0,
            'fuera_de_linea' => 0,
            'advertencia' => 0,
            'desconocido' => 0
        ];
    }
}
