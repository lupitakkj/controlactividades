# Instrucciones para el proyecto Checador

## Entorno

- Mantén la compatibilidad con Laravel 12 y PHP 8.2.
- Usa MySQL como motor de base de datos del proyecto.
- Toma InfinityFree como el alojamiento indicado para producción. Verifica la configuración real del alojamiento cuando el cambio dependa de ella.

## Cambios en la base de datos

- Prepara los cambios de esquema o datos como scripts SQL compatibles con MySQL para su aplicación manual en phpMyAdmin. Aplicar el SQL en producción requiere autorización.
- No crees ni ejecutes migraciones de Laravel sin autorización explícita.

## Alcance y comportamiento

- Respeta los roles y permisos existentes. Conserva los controles de acceso al modificar rutas, acciones o vistas.
- No alteres los cálculos de avance de producción.
- Limita los cambios a los módulos incluidos en la solicitud.

## Pruebas y publicación

- Antes de publicar cambios, propón pruebas concretas e indica qué debe comprobar cada una. Ejecuta las verificaciones apropiadas al cambio y registra sus resultados.
- Adjunta una captura del cambio funcionando o un video breve si ayuda a mostrar la interacción. Para cambios sin interfaz, captura el resultado de la verificación sin exponer datos sensibles. Explica cualquier prueba o evidencia que no puedas obtener.
- Después de verificar el cambio y capturar su evidencia, crea un commit por módulo o vista con los archivos correspondientes.
- No ejecutes despliegues ni hagas push sin autorización. Cuando se autorice el push al destino habitual, sube los commits verificados a `origin/desarrollo`, comprueba el resultado e informa qué commits se subieron o por qué quedó pendiente.


## Coordinación multiagente de Rijaya

- El agente principal coordina. Usa la Skill personal `rijaya-agent-coordinator` si está instalada y sigue el [protocolo y estado compartido](docs/agent-context/README.md).
- Usa los perfiles nativos del proyecto `frontend`, `backend`, `database` y `qa` cuando la herramienta de delegación permita seleccionarlos. Si no permite elegir perfiles, asigna responsabilidades explícitas a los subagentes e informa esa limitación.
- Reparte solo tareas independientes. Antes de implementar cambios dependientes, acuerda el contrato entre Blade/JavaScript, Laravel y MySQL. Asigna un único editor por archivo y escala solapamientos al coordinador.
- La delegación conserva el alcance y los permisos. En revisiones de solo lectura los especialistas no escriben archivos. El coordinador registra los resultados únicamente si el encargo permite documentación.
- No conectes bases reales para pruebas ni introduzcas secretos en el contexto compartido. Limita los cambios de la aplicación al slice autorizado. Aplica la política de SQL manual y reserva al usuario la ejecución de SQL productivo. Los commits, push, merges y despliegues requieren autorización explícita.
