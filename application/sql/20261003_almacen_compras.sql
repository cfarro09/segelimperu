CREATE TABLE IF NOT EXISTS almacen_compras (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT, periodo CHAR(7) NOT NULL,
    estado ENUM('borrador','recibida') NOT NULL DEFAULT 'borrador',
    proveedor VARCHAR(200) NOT NULL DEFAULT '', observaciones VARCHAR(1000) NOT NULL DEFAULT '',
    total DECIMAL(15,2) NOT NULL DEFAULT 0, revision INT UNSIGNED NOT NULL DEFAULT 1,
    usuario_id INT UNSIGNED NOT NULL, recibido_por INT UNSIGNED NULL, recibido_en DATETIME NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(id), UNIQUE KEY uk_almacen_compra_periodo(periodo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS almacen_compra_asignaciones (
    compra_id INT UNSIGNED NOT NULL, asignacion_id INT UNSIGNED NOT NULL,
    PRIMARY KEY(compra_id,asignacion_id), UNIQUE KEY uk_almacen_compra_asignacion(asignacion_id),
    CONSTRAINT fk_almacen_ca_compra FOREIGN KEY(compra_id) REFERENCES almacen_compras(id),
    CONSTRAINT fk_almacen_ca_asignacion FOREIGN KEY(asignacion_id) REFERENCES almacen_asignaciones(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS almacen_compra_detalles (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT, compra_id INT UNSIGNED NOT NULL, producto_id INT UNSIGNED NOT NULL,
    descripcion VARCHAR(255) NOT NULL, unidad VARCHAR(30) NOT NULL, marca VARCHAR(100) NOT NULL,
    solicitado DECIMAL(12,3) NOT NULL, stock_disponible DECIMAL(12,3) NOT NULL,
    stock_confirmado TINYINT(1) NOT NULL,
    cantidad DECIMAL(12,3) NOT NULL, precio_real DECIMAL(11,2) NOT NULL, total DECIMAL(15,2) NOT NULL,
    ajustado TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY(id), UNIQUE KEY uk_almacen_compra_producto(compra_id,producto_id),
    CONSTRAINT fk_almacen_cd_compra FOREIGN KEY(compra_id) REFERENCES almacen_compras(id),
    CONSTRAINT fk_almacen_cd_producto FOREIGN KEY(producto_id) REFERENCES almacen_productos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
