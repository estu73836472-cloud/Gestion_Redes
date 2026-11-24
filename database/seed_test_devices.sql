USE gestion_redes;

INSERT IGNORE INTO dispositivos (nombre, ip, tipo, ubicacion, estado)
VALUES
    ('Router de prueba', '127.0.0.1', 'router', 'Laboratorio', 'desconocido'),
    ('Equipo de prueba no enrutable', '192.0.2.1', 'servidor', 'Laboratorio', 'desconocido');
