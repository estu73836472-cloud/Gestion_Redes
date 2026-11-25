<?php

namespace App\Repositories;

use Config\Database;
use PDO;

class IncidenciaRepository
{
    private PDO $db;

    private const GRAVEDAD_A_CODIGO = [
        'alta'    => 'AL',
        'critica' => 'CR',
        'media'   => 'MD',
        'baja'    => 'BJ',
    ];

    private const CODIGO_A_GRAVEDAD = [
        'AL' => 'alta',
        'CR' => 'critica',
        'MD' => 'media',
        'BJ' => 'baja',
    ];

    private const ESTADO_A_CODIGO = [
        'abierta'    => 'A',
        'en_proceso' => 'P',
        'resuelta'   => 'R',
    ];

    private const CODIGO_A_ESTADO = [
        'A' => 'abierta',
        'P' => 'en_proceso',
        'R' => 'resuelta',
    ];

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT i.incid_id, i.dis_id, i.incid_titulo, i.incid_desc, i.incid_tipo,
                       i.incid_prioridad, i.incid_stat, i.incid_origen, i.incid_feing, i.incid_fesol,
                       d.dis_nombre as dispositivo_nombre, d.dis_ip as dispositivo_ip, d.dis_tipo as dispositivo_tipo 
                FROM incidencias i 
                INNER JOIN dispositivos d ON d.dis_id = i.dis_id 
                ORDER BY i.incid_id DESC";
        $stmt = $this->db->query($sql);
        return array_map([$this, 'mapear'], $stmt->fetchAll());
    }

    public function findById(string $id): ?array
    {
        $sql = "SELECT i.incid_id, i.dis_id, i.incid_titulo, i.incid_desc, i.incid_tipo,
                       i.incid_prioridad, i.incid_stat, i.incid_origen, i.incid_feing, i.incid_fesol,
                       d.dis_nombre as dispositivo_nombre, d.dis_ip as dispositivo_ip 
                FROM incidencias i 
                INNER JOIN dispositivos d ON d.dis_id = i.dis_id 
                WHERE i.incid_id = :id 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ? $this->mapear($result) : null;
    }

    public function findPorDispositivo(string $dispositivoId): array
    {
        $sql = "SELECT * FROM incidencias 
                WHERE dis_id = :dis_id 
                ORDER BY incid_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['dis_id' => $dispositivoId]);
        return array_map([$this, 'mapear'], $stmt->fetchAll());
    }

    public function create(array $data): string
    {
        $id = $this->siguienteId();

        $sql = "INSERT INTO incidencias 
                    (incid_id, dis_id, incid_titulo, incid_desc, incid_tipo, incid_prioridad, incid_stat, incid_origen, incid_feing) 
                VALUES 
                    (:incid_id, :dis_id, :incid_titulo, :incid_desc, :incid_tipo, :incid_prioridad, :incid_stat, :incid_origen, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'incid_id'        => $id,
            'dis_id'          => $data['dispositivo_id'],
            'incid_titulo'    => $data['titulo'],
            'incid_desc'      => $data['descripcion'],
            'incid_tipo'      => $data['tipo'] ?? 'OT',
            'incid_prioridad' => $this->codificarGravedad($data['gravedad'] ?? 'media'),
            'incid_stat'      => $this->codificarEstado($data['estado'] ?? 'abierta'),
            'incid_origen'    => 'U',
        ]);

        return $id;
    }

    public function update(string $id, array $data): bool
    {
        $sql = "UPDATE incidencias 
                SET incid_titulo = :titulo, 
                    incid_desc = :descripcion, 
                    incid_prioridad = :gravedad, 
                    incid_stat = :estado,
                    incid_fesol = :fecha_cierre 
                WHERE incid_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'           => $id,
            'titulo'       => $data['titulo'],
            'descripcion'  => $data['descripcion'],
            'gravedad'     => $this->codificarGravedad($data['gravedad'] ?? 'media'),
            'estado'       => $this->codificarEstado($data['estado'] ?? 'abierta'),
            'fecha_cierre' => $data['fecha_cierre'] ?? null,
        ]);
    }

    public function cambiarEstado(string $id, string $estado): bool
    {
        $fechaCierre = ($estado === 'resuelta') ? date('Y-m-d H:i:s') : null;
        $sql = "UPDATE incidencias 
                SET incid_stat = :estado, incid_fesol = :fecha_cierre 
                WHERE incid_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'           => $id,
            'estado'       => $this->codificarEstado($estado),
            'fecha_cierre' => $fechaCierre
        ]);
    }

    public function getMetricas(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN incid_stat = 'A' THEN 1 ELSE 0 END) as abiertas,
                    SUM(CASE WHEN incid_stat = 'P' THEN 1 ELSE 0 END) as en_proceso,
                    SUM(CASE WHEN incid_stat = 'R' THEN 1 ELSE 0 END) as resueltas
                FROM incidencias";
        $stmt = $this->db->query($sql);
        $res = $stmt->fetch();
        return [
            'total'      => (int)($res['total'] ?? 0),
            'abiertas'   => (int)($res['abiertas'] ?? 0),
            'en_proceso' => (int)($res['en_proceso'] ?? 0),
            'resueltas'  => (int)($res['resueltas'] ?? 0),
        ];
    }

    private function siguienteId(): string
    {
        $stmt = $this->db->query("SELECT MAX(incid_id) FROM incidencias WHERE incid_id LIKE 'INC%'");
        $max = $stmt->fetchColumn();
        if ($max) {
            $num = (int)substr($max, 3) + 1;
        } else {
            $num = 1;
        }
        return 'INC' . str_pad((string)$num, 7, '0', STR_PAD_LEFT);
    }

    private function mapear(array $row): array
    {
        return [
            'id'                 => $row['incid_id'],
            'dispositivo_id'     => $row['dis_id'],
            'titulo'             => $row['incid_titulo'],
            'descripcion'        => $row['incid_desc'],
            'tipo'               => $row['incid_tipo'] ?? 'OT',
            'gravedad'           => $this->decodificarGravedad($row['incid_prioridad']),
            'estado'             => $this->decodificarEstado($row['incid_stat']),
            'fecha_reporte'      => $row['incid_feing'],
            'fecha_cierre'       => $row['incid_fesol'],
            'dispositivo_nombre' => $row['dispositivo_nombre'] ?? null,
            'dispositivo_ip'     => $row['dispositivo_ip'] ?? null,
            'dispositivo_tipo'   => $row['dispositivo_tipo'] ?? null,
        ];
    }

    private function codificarGravedad(string $gravedad): string
    {
        return self::GRAVEDAD_A_CODIGO[strtolower(trim($gravedad))] ?? 'MD';
    }

    private function decodificarGravedad(?string $codigo): string
    {
        return self::CODIGO_A_GRAVEDAD[strtoupper(trim((string)$codigo))] ?? 'media';
    }

    private function codificarEstado(string $estado): string
    {
        return self::ESTADO_A_CODIGO[strtolower(trim($estado))] ?? 'A';
    }

    private function decodificarEstado(?string $codigo): string
    {
        return self::CODIGO_A_ESTADO[strtoupper(trim((string)$codigo))] ?? 'abierta';
    }
}
