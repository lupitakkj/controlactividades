# Coordinación y contexto de Rijaya

El agente principal del chat actúa como coordinador. Los perfiles de `.codex/agents/` especializan a sus subagentes en frontend, backend, database y qa. Abre un chat en la raíz del proyecto y pide, por ejemplo, «Usa rijaya-agent-coordinator y delega las revisiones independientes en los perfiles del proyecto». Si Codex pide confiar en la carpeta, verifica su origen antes de hacerlo.

## Protocolo

1. Lee AGENTS.md y el estado común. Identifica revisión Git, objetivo, entregable, permisos y dependencias. Registra cuáles tareas están listas para ejecutarse en paralelo. Elige solo los perfiles que aporten al encargo.
2. Envía a cada agente el ID de tarea, revisión, objetivo, archivos de lectura y escritura, contrato aplicable, límites y comprobaciones esperadas. Cada agente responde con estado, archivos tocados, evidencia por archivo/línea, pruebas y resultado, riesgos y bloqueos. Escala cambios de alcance o contrato al coordinador antes de editar.
3. Reserva un único editor por archivo, incluidos archivos compartidos y generados. Si hay solapamiento, el coordinador reasigna o serializa. Las investigaciones independientes pueden leer los mismos archivos. Entre chats que escriban a la vez, acuerda propietarios y usa worktrees separados; el archivo de estado es un registro, no un bloqueo automático.
4. Antes de implementar tareas dependientes, frontend y backend acuerdan ruta y método, campos y tipos, respuesta, errores, validación y permisos. Backend y database acuerdan tablas y columnas, tipos, nulabilidad, claves, relaciones, índices, transacciones y compatibilidad/reversión del SQL. Registra el contrato y sus criterios de aceptación con QA. Si cambia, pausa los dependientes y confirma la nueva versión.
5. El coordinador contrasta los informes y ejecuta o consolida las verificaciones seguras. Actualiza el estado común solo cuando se autorice escribir documentación. Distingue evidencia, hipótesis y pendientes.

## Estado persistente

[Estado común](estado.md) registra decisiones y su motivo, contratos, avance y tareas con responsable/dependencias, riesgos y pruebas con resultado. Añade revisión Git, fecha y fuentes a cada entrega para que otros chats puedan revalidarla. Marca contratos como propuestos o confirmados, y tareas como pendientes, en curso, bloqueadas o verificadas. El coordinador es el único editor por encargo; en worktrees separados se comparte mediante un traspaso explícito de la revisión y del documento.

## Fase 2 propuesta: conversaciones independientes

Los subagentes nativos pertenecen al árbol de la sesión coordinadora. Los chats independientes conservan su propio historial; el intercambio de este proyecto se hace mediante archivos compartidos y un traspaso explícito. No hay un puente automático instalado.

Para automatizar ese traspaso, un proceso local autorizado podría usar [Codex app-server](https://learn.chatgpt.com/docs/app-server) por stdio. Inicializaría el protocolo, crearía o retomaría solo los chats administrados por el proceso con `thread/start` y `thread/resume`, enviaría trabajo con `turn/start` y recogería resultados y estado de cierre con las notificaciones del turno. Mantendría localmente la relación entre ID de tarea, ID de chat, revisión y versión del contrato. El coordinador entregaría solo el contexto mínimo del repositorio y revisaría la evidencia antes de publicar un nuevo estado común.

El primer piloto sería de solo lectura con dos tareas independientes. Debe demostrar reanudación, correlación de informes y detección de entregas duplicadas o desfasadas. Para habilitar escrituras después, hacen falta worktrees separados, reserva exclusiva de archivos y revisión humana de integración. Ante un timeout o una respuesta parcial, marcar la tarea bloqueada y reconciliar su estado antes de reenviar. No registrar credenciales ni dar acceso a producción.
