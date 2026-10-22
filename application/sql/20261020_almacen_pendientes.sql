-- Pendientes de almacén (antes en WhatsApp/PDF): uniformes, materiales, implementos, recojos y otros, con su seguimiento.
CREATE TABLE almacen_pendientes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  fecha DATE NOT NULL,
  tipo ENUM('uniformes','materiales','implementos','recojo','otros') NOT NULL DEFAULT 'otros',
  cliente_id INT UNSIGNED NULL,
  sede VARCHAR(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  detalle TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  solicitante VARCHAR(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  prioridad ENUM('normal','urgente') NOT NULL DEFAULT 'normal',
  estado ENUM('pendiente','en_proceso','atendido') NOT NULL DEFAULT 'pendiente',
  observaciones VARCHAR(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  creado_por INT UNSIGNED NOT NULL,
  atendido_por INT UNSIGNED NULL,
  atendido_en DATETIME NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_almacen_pendiente_estado (estado, fecha),
  KEY idx_almacen_pendiente_creador (creado_por),
  CONSTRAINT fk_almacen_pendiente_cliente FOREIGN KEY (cliente_id) REFERENCES almacen_clientes (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
