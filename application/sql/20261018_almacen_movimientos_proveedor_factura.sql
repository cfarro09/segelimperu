-- Entradas de inventario: proveedor y número de factura (opcionales).
ALTER TABLE almacen_movimientos
  ADD COLUMN proveedor VARCHAR(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' AFTER motivo,
  ADD COLUMN factura VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' AFTER proveedor;
