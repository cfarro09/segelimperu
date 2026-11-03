-- Órdenes de compra manuales (reposición: uniformes, EPP, etc.), además de las consolidadas desde asignaciones.
-- Usan la misma aprobación de Administración y la misma recepción. El precio de cada línea es editable en borrador.
ALTER TABLE almacen_compras
  ADD COLUMN tipo ENUM('consolidada','manual') NOT NULL DEFAULT 'consolidada' AFTER numero,
  ADD COLUMN descripcion VARCHAR(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' AFTER tipo;
