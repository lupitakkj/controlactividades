-- Ejecutar en la base del checador desde MySQL o phpMyAdmin.
-- No borra ni modifica registros existentes.
CREATE TABLE IF NOT EXISTS `auditoria_eventos` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `solicitud_id` CHAR(36) NOT NULL,
    `usuario_id` BIGINT UNSIGNED NULL,
    `usuario_nombre` VARCHAR(255) NULL,
    `accion` VARCHAR(30) NOT NULL,
    `ruta` VARCHAR(255) NULL,
    `metodo` VARCHAR(10) NOT NULL,
    `url` TEXT NOT NULL,
    `estado_http` SMALLINT UNSIGNED NULL,
    `modelo` VARCHAR(255) NULL,
    `registro_id` VARCHAR(255) NULL,
    `antes` JSON NULL,
    `despues` JSON NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `auditoria_eventos_solicitud_id_index` (`solicitud_id`),
    KEY `auditoria_eventos_usuario_id_index` (`usuario_id`),
    KEY `auditoria_eventos_accion_index` (`accion`),
    KEY `auditoria_eventos_ruta_index` (`ruta`),
    KEY `auditoria_eventos_registro_id_index` (`registro_id`),
    KEY `auditoria_eventos_created_at_index` (`created_at`),
    KEY `auditoria_eventos_modelo_registro_id_index` (`modelo`, `registro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
