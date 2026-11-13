# Estado común de Rijaya

Fecha: 2026-11-11. Revisión de la aplicación inspeccionada: `70a8889`. Alcance: Control Operativo, revisión estática de solo lectura.

## Decisiones y arquitectura

- El agente principal coordina y conserva este registro. Se definen cuatro perfiles nativos por proyecto. La prueba delegó tres revisiones primero y QA al liberarse un cupo. La herramienta de esta sesión solo permite dar nombre e instrucciones al subagente; la selección de un perfil TOML por nombre queda pendiente de probar en un chat que la exponga.
- El flujo verificado es ruta web autenticada → ControlOperativoController → Eloquent → Blade. Los cambios de base se preparan como SQL manual para phpMyAdmin según AGENTS.md. El esquema efectivo queda fuera de esta prueba.

## Contrato observado

| Frontera | Contrato confirmado en el código | Por confirmar |
| --- | --- | --- |
| Backend → Blade | GET `/control-operativo` entrega `pedidos`, `pedidosActivos` y `avanceGlobal` a `control-operativo.index`. | Experiencia en navegador y reglas de acceso por rol. |
| Blade → backend | POST `/control-operativo/exportar` envía `pedido_ids` de filas visibles. Laravel valida un arreglo de 1 a 5000 enteros distintos. La vista espera XLSX o JSON de error. | Exportación con IDs ajenos y comportamiento con cuentas de distintos roles. |
| Backend → MySQL | Eloquent espera un control y varias actividades por pedido, y carga partidas, despiece y procesos. | DDL, índices, unicidad y claves foráneas efectivos. |

## Informes de la prueba

| Rol | Evidencia confirmada | Riesgo o pendiente |
| --- | --- | --- |
| Frontend | La exportación usa las filas visibles y el modal semanal maneja Escape y devuelve el foco. [Vista](../../resources/views/control-operativo/index.blade.php#L3281), [teclado](../../resources/views/control-operativo/index.blade.php#L5418). | Verificar que Tab permanezca en el diálogo. Las preferencias usan claves fijas; probar aislamiento entre cuentas en el mismo navegador. |
| Backend | Las rutas PATCH de comentario y responsable referencian métodos ausentes del controlador. [Comentario](../../routes/web.php#L219), [responsable](../../routes/web.php#L241), [controlador](../../app/Http/Controllers/ControlOperativoController.php#L22). | Reproducir el fallo y definir el comportamiento esperado antes de corregirlo. |
| Database | Se cargan pedidos sin paginar con orden numérico por CAST y se repite la consulta de procesos activos por pedido. [Controlador](../../app/Http/Controllers/ControlOperativoController.php#L22), [procesos](../../app/Http/Controllers/ControlOperativoController.php#L599), [relaciones](../../app/Models/Pedido.php#L120). | Medir impacto con una copia aislada y verificar unicidad de `control_operativo.pedido_id` antes de cambiar las operaciones de creación. |
| QA | Las rutas revisadas requieren autenticación. La exportación selecciona por ID sin filtrar por usuario y el método de desactivación carece de autorización por recurso. No se localizaron pruebas específicas bajo `tests/`. [Rutas](../../routes/web.php#L131), [exportación](../../app/Http/Controllers/ControlOperativoController.php#L159), [desactivación](../../app/Http/Controllers/ControlOperativoController.php#L384). | Contrastar con la matriz de negocio antes de concluir que hay acceso indebido. |

## Avance, tareas y pruebas

- **Verificado · coordinador:** cuatro informes recibidos y consolidados. Los especialistas no ejecutaron Laravel, no consultaron bases ni escribieron archivos.
- **Pendiente · backend → QA:** confirmar expectativa para comentario y responsable y reproducir las dos rutas en un entorno aislado.
- **Pendiente · QA + backend:** conseguir la matriz de permisos y probar invitado, rol autorizado, rol denegado e ID fuera de alcance.
- **Pendiente · database:** revisar esquema y plan de consulta en una copia autorizada; depende de disponer de un volcado o entorno aislado.
- **Pendiente · frontend + QA:** comprobar el foco del modal y las preferencias con teclado y cuentas de prueba.
- **Pendiente · coordinador:** probar selección de los cuatro perfiles TOML por nombre en un cliente que exponga esa opción. El puente entre chats es una propuesta de fase 2.
