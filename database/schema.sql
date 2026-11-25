CREATE DATABASE IF NOT EXISTS gestion_redes
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gestion_redes;

CREATE TABLE IF NOT EXISTS dispositivos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    mac VARCHAR(17) DEFAULT NULL,
    tipo VARCHAR(30) NOT NULL DEFAULT 'Otro',
    ubicacion VARCHAR(100) DEFAULT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'desconocido',
    ultimo_monitoreo DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_dispositivos_ip (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS incidencias (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    dispositivo_id INT UNSIGNED NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    gravedad ENUM('baja', 'media', 'alta') NOT NULL DEFAULT 'media',
    estado ENUM('abierta', 'en_proceso', 'resuelta') NOT NULL DEFAULT 'abierta',
    fecha_reporte TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_incidencias_dispositivo (dispositivo_id),
    KEY idx_incidencias_estado (estado),
    CONSTRAINT fk_incidencias_dispositivo
        FOREIGN KEY (dispositivo_id) REFERENCES dispositivos (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS monitoreos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    dispositivo_id INT UNSIGNED NOT NULL,
    estado VARCHAR(20) NOT NULL,
    latencia_ms INT UNSIGNED DEFAULT NULL,
    detalles TEXT DEFAULT NULL,
    uso_cpu DECIMAL(5, 2) DEFAULT NULL,
    uso_memoria DECIMAL(5, 2) DEFAULT NULL,
    fecha_monitoreo TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_monitoreos_dispositivo_fecha (dispositivo_id, fecha_monitoreo),
    CONSTRAINT fk_monitoreos_dispositivo
        FOREIGN KEY (dispositivo_id) REFERENCES dispositivos (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
