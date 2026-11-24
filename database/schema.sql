CREATE DATABASE IF NOT EXISTS gestion_redes
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gestion_redes;

CREATE TABLE IF NOT EXISTS dispositivos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(120) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    mac VARCHAR(17) DEFAULT NULL,
    tipo VARCHAR(40) NOT NULL DEFAULT 'Otro',
    ubicacion VARCHAR(120) DEFAULT NULL,
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
    titulo VARCHAR(160) NOT NULL,
    descripcion TEXT NOT NULL,
    gravedad VARCHAR(20) NOT NULL DEFAULT 'media',
    estado VARCHAR(20) NOT NULL DEFAULT 'abierta',
    fecha_reporte TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_incidencias_dispositivo (dispositivo_id),
    KEY idx_incidencias_estado (estado),
    CONSTRAINT fk_incidencias_dispositivo
        FOREIGN KEY (dispositivo_id) REFERENCES dispositivos (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS monitoreos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    dispositivo_id INT UNSIGNED NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'desconocido',
    latencia_ms INT UNSIGNED DEFAULT NULL,
    detalles TEXT DEFAULT NULL,
    fecha_monitoreo TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_monitoreos_dispositivo (dispositivo_id),
    CONSTRAINT fk_monitoreos_dispositivo
        FOREIGN KEY (dispositivo_id) REFERENCES dispositivos (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
