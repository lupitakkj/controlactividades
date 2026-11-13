# RIJAYA — Bitácora de cambios

**Fecha de inicio:** 13 de noviembre de 2026

Este archivo registra las modificaciones realizadas a partir de su creación.

El historial anterior se encuentra en `RIJAYA_HISTORIAL_MAESTRO.md`.

## Reglas

- Agregar al final una entrada por intervención con cambios. Omitir las consultas de solo lectura.
- No borrar cambios anteriores.
- No registrar despliegues sin evidencia.
- Separar propuestas de modificaciones realizadas.
- Documentar pruebas y errores pendientes.

## Registro inicial

### 2026-11-13 — Creación de memoria para Codex

**Módulo:** Documentación general.

**Estado:** DOCUMENTADO.

**Cambio:** Se estableció una fuente de información histórica para RIJAYA y una skill para su mantenimiento.

**Despliegue:** Pendiente de incorporar al repositorio.

---

## Plantilla de nuevas entradas

### AAAA-MM-DD — Nombre del cambio

**Módulo:**

**Estado:**

**Solicitud:**

**Comportamiento anterior:**

**Problema identificado:**

**Solución implementada:**

**Archivos modificados:**

**Cambios en base de datos:**

**Pruebas realizadas:**

**Resultado:**

**Commit de Git:**

**Despliegue a producción:**

**Errores pendientes:**

**Próximos pasos:**

---
### 2026-11-13 11:04 (Ciudad de México) — Verificación inicial de la memoria de RIJAYA

**Módulo:** Documentación general.

**Estado:** DOCUMENTADO LOCALMENTE.

**Solicitud:** Consultar la memoria de RIJAYA, comprobar las reglas contra el código y registrar las intervenciones en la bitácora.

**Hallazgo:** El historial maestro contenía el procedimiento de mantenimiento de la memoria. La plantilla de entorno usa SQLite y el código admite MySQL.

**Solución:** Agregar al historial referencias verificadas sobre dependencias, configuración de base de datos, permisos y avance.

**Archivos modificados:** `docs/RIJAYA_HISTORIAL_MAESTRO.md` y `docs/RIJAYA_BITACORA_CAMBIOS.md`.

**Cambios en base de datos:** Ninguno.

**Verificación:** Inspección estática de dependencias, configuración, rutas, cálculos de avance y scripts SQL. Comprobación documental de conservación de las entradas previas y de las referencias agregadas.

**Despliegue a producción:** No realizado.

**Pendiente:** Confirmar la configuración efectiva de InfinityFree cuando una intervención dependa de ella.

### 2026-11-13 12:51 (Ciudad de México) — SLICE 0 — Corrección de configuración y documentación de RIJAYA

**Módulo y estado:** Coordinación y configuración local · implementación del slice autorizado.
**Solicitud y solución:** Retirar dos llamadas automáticas a migraciones de Composer, documentar la activación manual de auditoría y alinear el alcance de los slices y las reglas de registro.
**Archivos:** `composer.json`, `AGENTS.md`, `docs/bitacora-interna.md`, `docs/RIJAYA_HISTORIAL_MAESTRO.md`, `docs/especialidades/FRONTEND.md` y `docs/RIJAYA_BITACORA_CAMBIOS.md`.
**Pruebas y evidencia:** Comprobación estática del JSON, de los comandos conservados, del enlace SQL, de las reglas documentales, del alcance y de la preservación de cambios previos. Captura `rijaya-slice-0-verificacion.png` adjunta al cierre del chat de Coordinación.
**Git, SQL y producción:** Rama desarrollo, base c86149f. Cambios locales sin commit. Se referenció el SQL existente de auditoría; no se generó ni ejecutó SQL. Sin push ni despliegue.
**Riesgo y pendiente:** Una instalación nueva requiere aprovisionar manualmente su esquema. Pendiente de revisión por el usuario.
