-- Varias órdenes de compra por mes: cada una consolida las asignaciones aprobadas nuevas desde la anterior.
-- numero es correlativo dentro del período (OC-202610-01, OC-202610-02, ...).
ALTER TABLE almacen_compras
  ADD COLUMN numero SMALLINT UNSIGNED NOT NULL DEFAULT 1 AFTER periodo,
  DROP INDEX uk_almacen_compra_periodo,
  ADD UNIQUE KEY uk_almacen_compra_periodo_numero (periodo, numero);
