---
name: rijaya-memoria
description: Consulta y actualización del historial, decisiones y cambios del sistema Laravel RIJAYA.
---

# Skill: Memoria permanente de RIJAYA

## Objetivo

Mantener una fuente documental centralizada y actualizada del proyecto RIJAYA.

## Instrucciones

Antes de realizar cualquier modificación:

1. Leer `AGENTS.md`.
2. Leer `docs/RIJAYA_HISTORIAL_MAESTRO.md`.
3. Leer `docs/RIJAYA_BITACORA_CAMBIOS.md`.
4. Revisar el código real relacionado con la solicitud.
5. Identificar módulos, dependencias y riesgos.
6. Distinguir información confirmada de propuestas.

## Al realizar cambios

- Trabajar en el módulo solicitado.
- Respetar las reglas existentes.
- Conservar compatibilidad cuando sea posible.
- No modificar la base de datos de producción sin autorización.
- No desplegar cambios automáticamente.
- Ejecutar pruebas disponibles y documentar sus resultados.

## Registro obligatorio

Después de cada intervención, agregar un registro al final de `docs/RIJAYA_BITACORA_CAMBIOS.md`.

Registrar:

- Fecha y hora.
- Módulo.
- Solicitud original.
- Problema encontrado.
- Solución aplicada.
- Archivos modificados.
- Cambios SQL.
- Pruebas realizadas.
- Estado de despliegue.
- Pendientes.

Nunca eliminar entradas anteriores.

Si únicamente se propone una solución, marcarla como PROPUESTA.

Si se modifican archivos locales, no indicar que están publicados en producción.

## Actualización del historial maestro

Cuando una regla de negocio, decisión arquitectónica o funcionalidad quede confirmada, actualizar el historial maestro con la fecha y su estado.

## Finalización de cada tarea

Entregar un resumen que incluya:

1. Qué cambió.
2. Qué archivos se modificaron.
3. Qué pruebas se realizaron.
4. Si se actualizó la bitácora.
5. Si está pendiente subir a GitHub.
6. Si está pendiente publicar en producción.

Si no se puede escribir en los archivos, entregar el registro listo para copiar y explicar que no se guardó.
## Reglas del Checador verificadas en el repositorio

### 2026-11-13 — Entorno, permisos y avance

**Estado:** Verificado por inspección estática del repositorio.

- `composer.json` declara Laravel `^12.0` y PHP `^8.2`.
- `config/database.php` define la conexión MySQL y lee `DB_CONNECTION`. La plantilla `.env.example` y el valor de respaldo de la configuración usan SQLite. MySQL es el motor indicado por `AGENTS.md`. La configuración efectiva de producción queda por verificar cuando un cambio dependa de ella.
- `routes/web.php` contiene un grupo protegido con `auth` y acciones con permisos `can:`, entre ellas la importación de pedidos y los tickets de calidad. Al intervenir una ruta o acción, revisar sus controles de acceso.
- El avance se calcula en `app/Models/Pedido.php` y en puntos específicos como `app/Http/Controllers/DespieceController.php`, `app/Http/Controllers/RutaClaveController.php` y `app/Services/AsignarRutaPorClave.php`. Preservar estos cálculos al intervenir otros módulos.
- `database/sql/` contiene scripts SQL manuales. `AGENTS.md` exige preparar el SQL para MySQL y solicitar autorización explícita para crear o ejecutar migraciones de Laravel.
- InfinityFree es el alojamiento indicado en `AGENTS.md`. La configuración del alojamiento no se verificó en esta revisión. Cualquier cambio en producción requiere autorización explícita.
