<?php

namespace App\Repositories;

use Config\Database;
use PDO;

class DispositivoRepository
{
    private PDO $db;

    private const ESTADO_A_CODIGO = [
        'en_linea'       => '1',
        'fuera_de_linea' => '0',
        'advertencia'    => 'W',
        'desconocido'    => 'D',
    ];

    private const TIPO_A_CODIGO = [
        'router'          => 'RT',
        'enrutador'       => 'RT',
        'switch'          => 'SW',
        'servidor'        => 'SV',
        'firewall'        => 'FW',
        'access point'    => 'AP',
        'punto de acceso' => 'AP',
        'accesspoint'     => 'AP',
        'ap'              => 'AP',
        'impresora'       => 'IM',
        'laptop'          => 'LP',
        'pc'              => 'PC',
        'otro'            => 'OT',
    ];

    private const CODIGO_A_TIPO = [
        'RT' => 'Router',
        'SW' => 'Switch',
        'SV' => 'Servidor',
        'FW' => 'Firewall',
        'AP' => 'Access Point',
        'IM' => 'Impresora',
        'LP' => 'Laptop',
        'PC' => 'PC',
        'OT' => 'Otro',
    ];

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT dis_id, dis_nombre, dis_tipo, dis_ip, dis_mac, dis_sisop,
                       dis_obs, dis_stat, dis_feing, dis_femod
                FROM dispositivos
                ORDER BY dis_id DESC";
        $stmt = $this->db->query($sql);

        return array_map([$this, 'mapear'], $stmt->fetchAll());
    }

    public function findById(string $id): ?array
    {
        $sql = "SELECT dis_id, dis_nombre, dis_tipo, dis_ip, dis_mac, dis_sisop,
                       dis_obs, dis_stat, dis_feing, dis_femod
                FROM dispositivos
                WHERE dis_id = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        return $result ? $this->mapear($result) : null;
    }

    public function findByIp(string $ip): ?array
    {
        $sql = "SELECT dis_id, dis_nombre, dis_tipo, dis_ip, dis_mac, dis_sisop,
                       dis_obs, dis_stat, dis_feing, dis_femod
                FROM dispositivos
                WHERE dis_ip = :ip
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ip' => $ip]);
        $result = $stmt->fetch();

        return $result ? $this->mapear($result) : null;
    }

    public function create(array $data): string
    {
        $id = $this->siguienteId();

        $sql = "INSERT INTO dispositivos
                    (dis_id, dis_nombre, dis_tipo, dis_ip, dis_mac, dis_sisop, dis_obs, dis_stat, dis_feing, dis_femod)
                VALUES
                    (:dis_id, :dis_nombre, :dis_tipo, :dis_ip, :dis_mac, :dis_sisop, :dis_obs, :dis_stat, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'dis_id'     => $id,
            'dis_nombre' => $data['nombre'],
            'dis_tipo'   => $this->codificarTipo($data['tipo'] ?? null),
            'dis_ip'     => $data['ip'],
            'dis_mac'    => $data['mac'] ?? null,
            'dis_sisop'  => $data['sistema_operativo'] ?? null,
            'dis_obs'    => $data['ubicacion'] ?? null,
            'dis_stat'   => $this->codificarEstado($data['estado'] ?? 'desconocido'),
        ]);

        return $id;
    }

    public function update(string $id, array $data): bool
    {
        $sql = "UPDATE dispositivos
                SET dis_nombre = :dis_nombre,
                    dis_tipo   = :dis_tipo,
                    dis_ip     = :dis_ip,
                    dis_mac    = :dis_mac,
                    dis_sisop  = :dis_sisop,
                    dis_obs    = :dis_obs,
                    dis_stat   = :dis_stat,
                    dis_femod  = NOW()
                WHERE dis_id = :dis_id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'dis_id'     => $id,
            'dis_nombre' => $data['nombre'],
            'dis_tipo'   => $this->codificarTipo($data['tipo'] ?? null),
            'dis_ip'     => $data['ip'],
            'dis_mac'    => $data['mac'] ?? null,
            'dis_sisop'  => $data['sistema_operativo'] ?? null,
            'dis_obs'    => $data['ubicacion'] ?? null,
            'dis_stat'   => $this->codificarEstado($data['estado'] ?? 'desconocido'),
        ]);
    }

    public function updateEstado(string $id, string $estado): bool
    {
        $sql = "UPDATE dispositivos
                SET dis_stat = :dis_stat, dis_femod = NOW()
                WHERE dis_id = :dis_id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'dis_id'   => $id,
            'dis_stat' => $this->codificarEstado($estado),
        ]);
    }

    public function delete(string $id): bool
    {
        $sql = "DELETE FROM dispositivos WHERE dis_id = :dis_id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['dis_id' => $id]);
    }

    public function getMetricas(): array
    {
        $sql = "SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN dis_stat IN ('1', 'A') THEN 1 ELSE 0 END) AS en_linea,
                    SUM(CASE WHEN dis_stat IN ('0', 'F') THEN 1 ELSE 0 END) AS fuera_de_linea,
                    SUM(CASE WHEN dis_stat = 'W' THEN 1 ELSE 0 END) AS advertencia,
                    SUM(CASE WHEN dis_stat NOT IN ('1', 'A', '0', 'F', 'W') OR dis_stat IS NULL THEN 1 ELSE 0 END) AS desconocido
                FROM dispositivos";
        $stmt = $this->db->query($sql);
        $resultado = $stmt->fetch();

        return [
            'total'          => (int)($resultado['total'] ?? 0),
            'en_linea'       => (int)($resultado['en_linea'] ?? 0),
            'fuera_de_linea' => (int)($resultado['fuera_de_linea'] ?? 0),
            'advertencia'    => (int)($resultado['advertencia'] ?? 0),
            'desconocido'    => (int)($resultado['desconocido'] ?? 0),
        ];
    }

    private function siguienteId(): string
    {
        $stmt = $this->db->query("SELECT MAX(dis_id) FROM dispositivos WHERE dis_id LIKE 'D%'");
        $max = $stmt->fetchColumn();
        if ($max) {
            $num = (int)substr($max, 1) + 1;
        } else {
            $num = 1;
        }
        return 'D' . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }

    private function mapear(array $row): array
    {
        return [
            'id'                 => $row['dis_id'],
            'nombre'             => $row['dis_nombre'],
            'tipo'               => $this->decodificarTipo($row['dis_tipo']),
            'tipo_codigo'        => $row['dis_tipo'],
            'ip'                 => $row['dis_ip'],
            'mac'                => $row['dis_mac'],
            'sistema_operativo'  => $row['dis_sisop'],
            'ubicacion'          => $row['dis_obs'],
            'estado'             => $this->decodificarEstado($row['dis_stat']),
            'ultimo_monitoreo'   => $row['dis_femod'] ?? $row['dis_feing'],
            'created_at'         => $row['dis_feing'],
            'updated_at'         => $row['dis_femod'],
        ];
    }

    private function codificarEstado(string $estado): string
    {
        return self::ESTADO_A_CODIGO[$estado] ?? '1';
    }

    private function decodificarEstado(?string $codigo): string
    {
        if ($codigo === null || trim($codigo) === '') {
            return 'desconocido';
        }
        $codigo = strtoupper(trim($codigo));
        if ($codigo === '1' || $codigo === 'A') return 'en_linea';
        if ($codigo === '0' || $codigo === 'F') return 'fuera_de_linea';
        if ($codigo === 'W') return 'advertencia';
        return 'desconocido';
    }

    private function codificarTipo(?string $tipo): ?string
    {
        if ($tipo === null || trim($tipo) === '') {
            return 'OT';
        }
        $valor = strtoupper(trim($tipo));
        if (isset(self::CODIGO_A_TIPO[$valor])) {
            return $valor;
        }
        return self::TIPO_A_CODIGO[strtolower(trim($tipo))] ?? 'OT';
    }

    private function decodificarTipo(?string $codigo): ?string
    {
        if ($codigo === null || trim($codigo) === '') {
            return 'Otro';
        }
        return self::CODIGO_A_TIPO[strtoupper($codigo)] ?? $codigo;
    }
}
