CREATE TABLE IF NOT EXISTS resumen_semanal_vistas (
    usuario_id BIGINT UNSIGNED NOT NULL,
    inicio_semana DATE NOT NULL,
    visto_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id, inicio_semana),
    CONSTRAINT resumen_semanal_vistas_usuario_fk
        FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
