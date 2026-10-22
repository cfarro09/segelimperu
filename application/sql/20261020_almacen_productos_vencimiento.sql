-- Fecha de vencimiento (fin de mes) para productos que la tienen, p. ej. venenos de fumigación.
ALTER TABLE almacen_productos ADD COLUMN vencimiento DATE NULL AFTER unidad;
