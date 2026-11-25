<?php

namespace App\Repositories;

use Config\Database;
use PDO;

class MonitoreoRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function registrar(string $dispositivoId, string $estado, ?int $latenciaMs, ?string $detalles = null): string
    {
        $monId = $this->siguienteId();

        $sql = "INSERT INTO monitoreo (mon_id, dis_id, mon_conexion, mon_latencia, mon_fec)
                VALUES (:mon_id, :dis_id, :mon_conexion, :mon_latencia, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'mon_id'       => $monId,
            'dis_id'       => $dispositivoId,
            'mon_conexion' => $estado,
            'mon_latencia' => $latenciaMs,
        ]);

        return $monId;
    }

    public function findUltimos(int $limit = 20): array
    {
        $sql = "SELECT m.mon_id, m.dis_id, m.mon_conexion, m.mon_latencia, m.mon_ucpu, m.mon_umem, m.mon_fec,
                       d.dis_nombre AS dispositivo_nombre, d.dis_ip AS dispositivo_ip, d.dis_tipo AS dispositivo_tipo
                FROM monitoreo m
                INNER JOIN dispositivos d ON d.dis_id = m.dis_id
                ORDER BY m.mon_fec DESC, m.mon_id DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map([$this, 'mapear'], $stmt->fetchAll());
    }

    public function findPorDispositivo(string $dispositivoId, int $limit = 10): array
    {
        $sql = "SELECT mon_id, dis_id, mon_conexion, mon_latencia, mon_ucpu, mon_umem, mon_fec
                FROM monitoreo
                WHERE dis_id = :dis_id
                ORDER BY mon_fec DESC, mon_id DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':dis_id', $dispositivoId, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map([$this, 'mapear'], $stmt->fetchAll());
    }

    private function siguienteId(): string
    {
        $stmt = $this->db->query("SELECT MAX(mon_id) FROM monitoreo WHERE mon_id LIKE 'M%'");
        $max = $stmt->fetchColumn();
        if ($max) {
            $num = (int)substr($max, 1) + 1;
        } else {
            $num = 1;
        }
        return 'M' . str_pad((string)$num, 7, '0', STR_PAD_LEFT);
    }

    private function mapear(array $row): array
    {
        return [
            'id'                 => $row['mon_id'],
            'dispositivo_id'     => $row['dis_id'],
            'estado'             => $row['mon_conexion'] ?? 'desconocido',
            'latencia_ms'        => $row['mon_latencia'] !== null ? (int)round((float)$row['mon_latencia']) : null,
            'detalles'           => "Comprobación de conectividad. Estado: " . ($row['mon_conexion'] ?? 'N/A'),
            'uso_cpu'            => isset($row['mon_ucpu']) && $row['mon_ucpu'] !== null ? round((float)$row['mon_ucpu'], 2) : null,
            'uso_memoria'        => isset($row['mon_umem']) && $row['mon_umem'] !== null ? round((float)$row['mon_umem'], 2) : null,
            'fecha_monitoreo'    => $row['mon_fec'],
            'dispositivo_nombre' => $row['dispositivo_nombre'] ?? null,
            'dispositivo_ip'     => $row['dispositivo_ip'] ?? null,
            'dispositivo_tipo'   => $row['dispositivo_tipo'] ?? null,
        ];
    }
}
