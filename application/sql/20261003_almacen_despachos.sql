CREATE TABLE IF NOT EXISTS almacen_despachos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    asignacion_id INT UNSIGNED NOT NULL,
    cliente_id INT UNSIGNED NOT NULL,
    cliente_codigo VARCHAR(40) NOT NULL,
    cliente_nombre VARCHAR(255) NOT NULL,
    cliente_ruc VARCHAR(11) NOT NULL DEFAULT '',
    cliente_direccion VARCHAR(255) NOT NULL DEFAULT '',
    periodo CHAR(7) NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    creado_en DATETIME NOT NULL,
    origen ENUM('despacho','demo_historico') NOT NULL DEFAULT 'despacho',
    PRIMARY KEY (id),
    UNIQUE KEY uq_despacho_asignacion (asignacion_id),
    KEY idx_despacho_cliente (cliente_id),
    CONSTRAINT fk_despacho_asignacion FOREIGN KEY (asignacion_id) REFERENCES almacen_asignaciones (id),
    CONSTRAINT fk_despacho_cliente FOREIGN KEY (cliente_id) REFERENCES almacen_clientes (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS almacen_despacho_detalles (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    despacho_id INT UNSIGNED NOT NULL,
    producto_id INT UNSIGNED NOT NULL,
    movimiento_id BIGINT UNSIGNED NOT NULL,
    codigo VARCHAR(40) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    unidad VARCHAR(30) NOT NULL,
    marca VARCHAR(100) NOT NULL DEFAULT '',
    cantidad DECIMAL(12,3) NOT NULL,
    stock_anterior DECIMAL(12,3) NOT NULL,
    stock_nuevo DECIMAL(12,3) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_despacho_producto (despacho_id, producto_id),
    UNIQUE KEY uq_despacho_movimiento (movimiento_id),
    CONSTRAINT fk_despacho_detalle FOREIGN KEY (despacho_id) REFERENCES almacen_despachos (id),
    CONSTRAINT fk_despacho_producto FOREIGN KEY (producto_id) REFERENCES almacen_productos (id),
    CONSTRAINT fk_despacho_movimiento FOREIGN KEY (movimiento_id) REFERENCES almacen_movimientos (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
