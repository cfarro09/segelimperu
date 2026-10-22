CREATE TABLE IF NOT EXISTS almacen_clientes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    codigo VARCHAR(40) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    ruc VARCHAR(11) NOT NULL DEFAULT '',
    grupo VARCHAR(80) NOT NULL DEFAULT '',
    supervisor VARCHAR(150) NOT NULL DEFAULT '',
    contacto VARCHAR(150) NOT NULL DEFAULT '',
    direccion VARCHAR(255) NOT NULL DEFAULT '',
    presupuesto DECIMAL(11,2) NOT NULL DEFAULT 0,
    saldo_inicial DECIMAL(11,2) NOT NULL DEFAULT 0,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    documento_baja VARCHAR(255) NOT NULL DEFAULT '',
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id), UNIQUE KEY uk_almacen_cliente_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS almacen_asignaciones (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    cliente_id INT UNSIGNED NOT NULL,
    periodo CHAR(7) NOT NULL,
    presupuesto DECIMAL(11,2) NOT NULL,
    total DECIMAL(15,2) NOT NULL DEFAULT 0,
    observaciones VARCHAR(1000) NOT NULL DEFAULT '',
    revision INT UNSIGNED NOT NULL DEFAULT 1,
    usuario_id INT UNSIGNED NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id), UNIQUE KEY uk_almacen_cliente_periodo (cliente_id, periodo),
    KEY idx_almacen_asignacion_periodo (periodo),
    CONSTRAINT fk_almacen_asignacion_cliente FOREIGN KEY (cliente_id) REFERENCES almacen_clientes (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS almacen_asignacion_detalles (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    asignacion_id INT UNSIGNED NOT NULL,
    producto_id INT UNSIGNED NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    categoria VARCHAR(80) NOT NULL,
    unidad VARCHAR(30) NOT NULL,
    marca VARCHAR(100) NOT NULL,
    cantidad DECIMAL(12,3) NOT NULL,
    precio_real DECIMAL(11,2) NOT NULL,
    precio_valorado DECIMAL(11,2) NOT NULL,
    total DECIMAL(15,2) NOT NULL,
    PRIMARY KEY (id), UNIQUE KEY uk_almacen_asignacion_producto (asignacion_id, producto_id),
    CONSTRAINT fk_almacen_detalle_asignacion FOREIGN KEY (asignacion_id) REFERENCES almacen_asignaciones (id),
    CONSTRAINT fk_almacen_detalle_producto FOREIGN KEY (producto_id) REFERENCES almacen_productos (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
