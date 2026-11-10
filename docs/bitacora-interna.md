# Bitácora interna

Los eventos se guardan en `auditoria_eventos`, sin agregar una pantalla pública.
La bitácora existente de actividades se conserva.

## Activación

Ejecutar en cada entorno antes de utilizar la versión nueva:

```sh
php artisan migrate --path=database/migrations/2026_11_10_150000_create_auditoria_eventos_table.php --force
```

## Información registrada

- Usuario y nombre al momento de la acción, fecha y hora del servidor.
- Identificador de solicitud para relacionar una acción con sus modificaciones.
- Método, URL sin parámetros sensibles y nombre de ruta (módulo/operación).
- Altas, modificaciones y eliminaciones de modelos de la aplicación: clase,
  identificador y valores antes/después. Las modificaciones incluyen los campos
  cambiados, omitiendo `updated_at`.
- Los procesos de despiece conservan `partida_id`, `proceso_id`, cantidades y
  porcentajes cuando esos campos se crean o cambian.
- Solicitudes de usuarios autenticados, incluidas consultas, exportaciones,
  inicio y cierre de sesión exitosos. El evento de solicitud contiene su código
  HTTP; no representa por sí solo una modificación exitosa.

No se guardan cuerpos de solicitudes, cookies, cabeceras de autenticación,
contraseñas, tokens, secretos ni atributos ocultos de modelos. El registro
conserva la identidad histórica aunque se elimine al usuario. Los cambios de
modelos dentro de transacciones solo se registran después del commit.

## Consulta interna

```sql
SELECT id, usuario_id, usuario_nombre, accion, ruta, modelo, registro_id,
       antes, despues, estado_http, solicitud_id, created_at
FROM auditoria_eventos
ORDER BY id DESC
LIMIT 100;
```

También se puede consultar con `App\Models\AuditoriaEvento` y filtrar por
`usuario_id`, `modelo`, `registro_id` o `solicitud_id`.

## Alcance

Empieza a registrar desde su instalación; no reconstruye acciones anteriores.
Las consultas directas, actualizaciones/eliminaciones masivas, operaciones
pivot y SQL externo no emiten eventos Eloquent: se registra la solicitud del
usuario, pero no valores antes/después de cada fila. Tareas de consola o de
cola fuera de una solicitud web no se atribuyen a usuarios ni se auditan con
este mecanismo. No se realiza limpieza automática del historial.

La tabla debe estar disponible: los errores de escritura de auditoría se
propagan, no se ocultan. Esto no convierte en atómicas las operaciones de
negocio que originalmente no usan transacciones.

## Pruebas aisladas

```sh
php -d extension=pdo_sqlite -d extension=sqlite3 vendor/bin/pest tests/Unit/AuditoriaSistemaTest.php
```

Las pruebas crean su propio esquema SQLite en memoria y no modifican MySQL.
