-- La orden de compra requiere aprobación de Administración antes de que Almacén registre la recepción.
-- Flujo: borrador -> aprobada (AD/SA) -> recibida (AL confirma).
ALTER TABLE almacen_compras
  MODIFY estado ENUM('borrador','aprobada','recibida') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrador',
  ADD COLUMN aprobado_por INT UNSIGNED NULL AFTER usuario_id,
  ADD COLUMN aprobado_en DATETIME NULL AFTER aprobado_por;
