CREATE TABLE IF NOT EXISTS almacen_productos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    codigo VARCHAR(40) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    categoria VARCHAR(80) NOT NULL,
    marca VARCHAR(100) NOT NULL DEFAULT '',
    unidad VARCHAR(30) NOT NULL,
    precio_real DECIMAL(11,2) NULL,
    precio_valorado DECIMAL(11,2) NOT NULL,
    stock DECIMAL(12,3) NOT NULL DEFAULT 0,
    stock_confirmado TINYINT(1) NOT NULL DEFAULT 0,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_almacen_producto_codigo (codigo),
    KEY idx_almacen_producto_estado (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS almacen_movimientos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    producto_id INT UNSIGNED NOT NULL,
    tipo ENUM('inicial','entrada','salida','ajuste') NOT NULL,
    cantidad DECIMAL(12,3) NOT NULL,
    stock_anterior DECIMAL(12,3) NOT NULL,
    stock_nuevo DECIMAL(12,3) NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_almacen_movimiento_producto (producto_id, id),
    CONSTRAINT fk_almacen_movimiento_producto FOREIGN KEY (producto_id) REFERENCES almacen_productos (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
