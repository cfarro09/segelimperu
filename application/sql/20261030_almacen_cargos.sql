-- Cargos de guías: foto/PDF de la guía firmada y sellada por el cliente, y su verificación.
-- Cada despacho del sistema tiene su cargo (pendiente hasta que se sube); también se registran guías manuales (talonario 0001).
CREATE TABLE almacen_cargos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  despacho_id INT UNSIGNED NULL,
  cliente_id INT UNSIGNED NULL,
  sede VARCHAR(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  numero_guia VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  fecha_guia DATE NOT NULL,
  archivo VARCHAR(120) COLLATE utf8mb4_unicode_ci NULL,
  archivo_tipo VARCHAR(60) COLLATE utf8mb4_unicode_ci NULL,
  subido_por INT UNSIGNED NULL,
  subido_en DATETIME NULL,
  verificado_por INT UNSIGNED NULL,
  verificado_en DATETIME NULL,
  observaciones VARCHAR(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  creado_por INT UNSIGNED NOT NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_almacen_cargo_despacho (despacho_id),
  UNIQUE KEY uk_almacen_cargo_guia (numero_guia),
  KEY idx_almacen_cargo_fecha (fecha_guia),
  CONSTRAINT fk_almacen_cargo_despacho FOREIGN KEY (despacho_id) REFERENCES almacen_despachos (id) ON DELETE CASCADE,
  CONSTRAINT fk_almacen_cargo_cliente FOREIGN KEY (cliente_id) REFERENCES almacen_clientes (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Despachos existentes: su cargo queda pendiente de subir.
INSERT INTO almacen_cargos (despacho_id, cliente_id, sede, numero_guia, fecha_guia, creado_por)
SELECT s.id, s.cliente_id, s.cliente_nombre, CONCAT('0002-', LPAD(s.id, 6, '0')), DATE(s.creado_en), s.usuario_id FROM almacen_despachos s;
