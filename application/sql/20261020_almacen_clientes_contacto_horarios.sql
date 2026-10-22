-- Clientes: contacto separado (persona, teléfono, correo), servicio con/sin material y horarios estructurados.
-- "grupo" se muestra como "Unidad asignada". Los campos de texto hora_ingreso, hora_salida y dias_servicio quedan
-- como respaldo histórico (ya no se editan); los horarios viven en almacen_cliente_horarios.
ALTER TABLE almacen_clientes
  ADD COLUMN telefono VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' AFTER contacto,
  ADD COLUMN correo VARCHAR(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' AFTER telefono,
  ADD COLUMN con_material TINYINT(1) NOT NULL DEFAULT 1 AFTER modalidad;
CREATE TABLE almacen_cliente_horarios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cliente_id INT UNSIGNED NOT NULL,
  dias SET('lun','mar','mie','jue','vie','sab','dom') NOT NULL,
  hora_ingreso TIME NOT NULL,
  hora_salida TIME NOT NULL,
  detalle VARCHAR(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  orden TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_almacen_horario_cliente (cliente_id, orden),
  CONSTRAINT fk_almacen_horario_cliente FOREIGN KEY (cliente_id) REFERENCES almacen_clientes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
