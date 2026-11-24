# Sistema de Gestión de Dispositivos de Red Local

Sistema web desarrollado bajo arquitectura **MVC por Capas** con **Slim Framework 4**, **PHP**, **PDO**, **MySQL** y frontend reactivo con **Fetch API** y **Bootstrap 5**.

---

## 📋 Alcance: 4 Historias de Usuario

1. **Registrar Dispositivos (HU1)**: Registro de equipos de red con nombre, IP (validada), MAC, tipo, ubicación y estado inicial.
2. **Actualizar Dispositivos (HU2)**: Modificación de los parámetros de cualquier dispositivo registrado con verificación de unicidad de IP.
3. **Monitoreo en tiempo real (HU3)**: Comprobación de conectividad y latencia (ms) de dispositivos individuales o de toda la red, registrando historial de eventos.
4. **Registro de incidencias (HU4)**: Reporte y seguimiento de anomalías/fallas asociadas a dispositivos específicos con niveles de gravedad y control de ciclo de vida (abierta, en proceso, resuelta).

---

## 🛠️ Tecnologías Empleadas

- **Lenguaje:** PHP 8.x
- **Framework backend:** Slim Framework 4 + Slim PSR-7
- **Gestor de dependencias:** Composer
- **Base de datos:** MySQL / MariaDB (Driver PDO)
- **Variables de entorno:** `vlucas/phpdotenv`
- **Frontend:** HTML5, CSS3, JavaScript nativo (Fetch API) y Bootstrap 5 + Bootstrap Icons

---

## 🏛️ Arquitectura del Proyecto (MVC por Capas)

```text
Sistema_Gestion_Redes/
├── app/
│   ├── controllers/
│   │   ├── api/                  # Controladores REST para la API
│   │   │   ├── BaseApiController.php
│   │   │   ├── DispositivoApiController.php
│   │   │   ├── MonitoreoApiController.php
│   │   │   └── IncidenciaApiController.php
│   │   └── web/                  # Controlador para renderizar vistas web
│   │       └── WebController.php
│   ├── models/                   # Entidades de datos (Dispositivo, Monitoreo, Incidencia)
│   ├── repositories/             # Capa de Acceso a Datos (Consultas SQL con PDO)
│   │   ├── DispositivoRepository.php
│   │   ├── MonitoreoRepository.php
│   │   └── IncidenciaRepository.php
│   └── services/                 # Capa de Lógica de Negocio y Validaciones
│       ├── DispositivoService.php
│       ├── MonitoreoService.php
│       └── IncidenciaService.php
├── config/
│   └── Database.php              # Conexión Singleton con PDO y healthcheck
├── database/
│   └── schema.sql                # Script de creación de BD, tablas y datos iniciales
├── public/
│   ├── index.php                 # Front Controller de la aplicación
│   ├── .htaccess                 # Reescritura de URLs para Apache
│   └── assets/                   # Recursos estáticos (CSS, JS, imágenes)
├── resources/
│   └── views/                    # Vistas HTML / PHP con Bootstrap 5
│       ├── layout/               # Header y Footer compartidos
│       ├── dashboard.php         # Panel general de control
│       ├── dispositivos.php      # Gestión de dispositivos (HU1 & HU2)
│       ├── monitoreo.php         # Monitoreo en vivo de red (HU3)
│       └── incidencias.php       # Registro de incidencias (HU4)
├── routes/
│   ├── api.php                   # Endpoints de la API REST
│   └── web.php                   # Rutas de navegación web
├── .env                          # Variables de entorno activas
├── .env.example                  # Plantilla de variables de entorno
└── composer.json                 # Dependencias y autoloading PSR-4
```

---

## 🚀 Endpoints de la API REST

### Diagnóstico
- `GET /api/health`: Estado del servicio y de la conexión a la base de datos.

### Dispositivos (HU1 & HU2)
- `GET /api/dispositivos`: Listar todos los equipos.
- `GET /api/dispositivos/metricas`: Contadores de dispositivos por estado.
- `GET /api/dispositivos/{id}`: Obtener detalle de un dispositivo.
- `POST /api/dispositivos`: Registrar un nuevo dispositivo (**HU1**).
- `PUT /api/dispositivos/{id}`: Actualizar datos de un dispositivo (**HU2**).
- `DELETE /api/dispositivos/{id}`: Eliminar un dispositivo.
- `GET /api/dispositivos/{id}/incidencias`: Incidencias vinculadas a un equipo.

### Monitoreo en Tiempo Real (HU3)
- `POST /api/monitoreo/ping/{id}`: Ejecutar ping y verificación en vivo a un dispositivo (**HU3**).
- `POST /api/monitoreo/ping-all`: Monitorear en tiempo real todos los dispositivos (**HU3**).
- `GET /api/monitoreo/historial`: Listado cronológico de comprobaciones recientes.
- `GET /api/monitoreo/dispositivo/{id}`: Historial específico de un dispositivo.

### Incidencias (HU4)
- `GET /api/incidencias`: Listar todas las incidencias.
- `GET /api/incidencias/metricas`: Métricas de incidencias (abiertas, en proceso, resueltas).
- `GET /api/incidencias/{id}`: Obtener detalle de una incidencia.
- `POST /api/incidencias`: Registrar una nueva incidencia (**HU4**).
- `PATCH /api/incidencias/{id}/estado`: Cambiar estado (abierta, en_proceso, resuelta).

---

## 💻 Puesta en Marcha

### 1. Preparar la Base de Datos

Inicia MySQL desde XAMPP e importa `database/schema.sql` con phpMyAdmin o ejecuta el script desde el cliente MySQL. El esquema crea la base `gestion_redes` y las tablas para dispositivos, incidencias y monitoreos.

Revisa `.env.example` y, si usas credenciales o puerto distintos, crea un archivo `.env` en la raíz y actualiza `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` y `DB_PASS`.

### 2. Instalar dependencias

Desde la raíz del proyecto ejecuta:

```bash
php composer.phar install
```

### 3. Iniciar el Servidor de Desarrollo
Puedes usar el servidor integrado de PHP apuntando a la carpeta `public`:

```bash
php -S localhost:8000 -t public
```

Luego abre tu navegador en:
- Panel Web: [http://localhost:8000](http://localhost:8000)
- Dispositivos: [http://localhost:8000/dispositivos](http://localhost:8000/dispositivos)
- Monitoreo en vivo: [http://localhost:8000/monitoreo](http://localhost:8000/monitoreo)
- Incidencias: [http://localhost:8000/incidencias](http://localhost:8000/incidencias)
- Health API: [http://localhost:8000/api/health](http://localhost:8000/api/health)
