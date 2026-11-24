# Sistema de Gestión de Redes

Aplicación PHP con Slim 4, PDO y MySQL/MariaDB para registrar dispositivos, monitorear su conectividad y gestionar incidencias.

## Preparación

1. Instala PHP 8+, Composer y MySQL/MariaDB.
2. Copia `.env.example` a `.env` y configura las credenciales de la base de datos.
3. Crea las tablas y carga dos dispositivos de prueba:

   ```sh
   mysql -u root -p < database/schema.sql
   mysql -u root -p < database/seed_test_devices.sql
   ```

4. Instala dependencias si aún no existe `vendor/`:

   ```sh
   composer install
   ```

5. Inicia el servidor desde la raíz del proyecto:

   ```sh
   php -S localhost:8000 -t public
   ```

Abre `http://localhost:8000`. La conexión a la base de datos y el resto de parámetros se configuran mediante `.env`.

## Incrementos

| Incremento | Avance |
| --- | --- |
| 1. Listar dispositivos | `GET /api/dispositivos`, semilla SQL y tabla dinámica |
| 2. Registrar dispositivo | `POST /api/dispositivos`, validación de IP y formulario |
| 3. Actualizar/eliminar | `PUT` y `DELETE /api/dispositivos/{id}`, acciones en la tabla |
| 4. Monitoreo en tiempo real | `POST /api/monitoreo/ping/{id}` y `/ping-all`, estado y latencia |
| 5. Incidencias | `GET`/`POST /api/incidencias` y `PATCH /api/incidencias/{id}/estado` |
| 6. Dashboard | Métricas de dispositivos e incidencias, último registro de monitoreo |
| 7. Historial de monitoreo | `GET /api/monitoreo` y tabla con actualización manual |

Las respuestas de la API usan el formato `{ "success": true, "data": ... }`. Los errores incluyen `success: false` y un mensaje.

## Pruebas con Postman

Importa `tests/postman/Gestion_Redes.postman_collection.json`, configura `baseUrl` (por defecto `http://localhost:8000`) y ejecuta las solicitudes en orden. La colección comprueba las respuestas de listado, creación, validación de IP, actualización/eliminación, monitoreo, incidencias e historial.

Las pruebas de ping dependen de que el servidor PHP pueda ejecutar `ping` y de las reglas de red/firewall del entorno. Las columnas de CPU y memoria del historial admiten valores nulos: el ping por sí solo no obtiene métricas de rendimiento del dispositivo.
