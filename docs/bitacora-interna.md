# BitÃ¡cora interna

Los eventos se guardan en `auditoria_eventos`, sin agregar una pantalla pÃºblica.
La bitÃ¡cora existente de actividades se conserva.

## ActivaciÃ³n

Ejecutar en cada entorno antes de utilizar la versiÃ³n nueva:

```sh
php artisan migrate --path=database/migrations/2026_11_10_150000_create_auditoria_eventos_table.php --force
```

## InformaciÃ³n registrada

- Usuario y nombre al momento de la acciÃ³n, fecha y hora del servidor.
- Identificador de solicitud para relacionar una acciÃ³n con sus modificaciones.
- MÃ©todo, URL sin parÃ¡metros sensibles y nombre de ruta (mÃ³dulo/operaciÃ³n).
- Altas, modificaciones y eliminaciones de modelos de la aplicaciÃ³n: clase,
  identificador y valores antes/despuÃ©s. Las modificaciones incluyen los campos
  cambiados, omitiendo `updated_at`.
- Los procesos de despiece conservan `partida_id`, `proceso_id`, cantidades y
  porcentajes cuando esos campos se crean o cambian.
- Solicitudes de usuarios autenticados, incluidas consultas, exportaciones,
  inicio y cierre de sesiÃ³n exitosos. El evento de solicitud contiene su cÃ³digo
  HTTP; no representa por sÃ­ solo una modificaciÃ³n exitosa.

No se guardan cuerpos de solicitudes, cookies, cabeceras de autenticaciÃ³n,
contraseÃ±as, tokens, secretos ni atributos ocultos de modelos. El registro
conserva la identidad histÃ³rica aunque se elimine al usuario. Los cambios de
modelos dentro de transacciones solo se registran despuÃ©s del commit.

## Consulta interna

```sql
SELECT id, usuario_id, usuario_nombre, accion, ruta, modelo, registro_id,
       antes, despues, estado_http, solicitud_id, created_at
FROM auditoria_eventos
ORDER BY id DESC
LIMIT 100;
```

TambiÃ©n se puede consultar con `App\Models\AuditoriaEvento` y filtrar por
`usuario_id`, `modelo`, `registro_id` o `solicitud_id`.

## Alcance

Empieza a registrar desde su instalaciÃ³n; no reconstruye acciones anteriores.
Las consultas directas, actualizaciones/eliminaciones masivas, operaciones
pivot y SQL externo no emiten eventos Eloquent: se registra la solicitud del
usuario, pero no valores antes/despuÃ©s de cada fila. Tareas de consola o de
cola fuera de una solicitud web no se atribuyen a usuarios ni se auditan con
este mecanismo. No se realiza limpieza automÃ¡tica del historial.

La tabla debe estar disponible: los errores de escritura de auditorÃ­a se
propagan, no se ocultan. Esto no convierte en atÃ³micas las operaciones de
negocio que originalmente no usan transacciones.

## Pruebas aisladas

```sh
php -d extension=pdo_sqlite -d extension=sqlite3 vendor/bin/pest tests/Unit/AuditoriaSistemaTest.php
```

Las pruebas crean su propio esquema SQLite en memoria y no modifican MySQL.
