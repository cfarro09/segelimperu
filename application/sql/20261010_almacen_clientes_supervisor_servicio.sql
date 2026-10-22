-- Liga cada cliente a un usuario Supervisor (SP) y agrega los datos del servicio (uso del área comercial).
ALTER TABLE almacen_clientes
  ADD COLUMN supervisor_id INT NULL AFTER grupo,
  ADD COLUMN modalidad VARCHAR(40) NOT NULL DEFAULT '' AFTER direccion,
  ADD COLUMN hora_ingreso VARCHAR(40) NOT NULL DEFAULT '' AFTER modalidad,
  ADD COLUMN hora_salida VARCHAR(40) NOT NULL DEFAULT '' AFTER hora_ingreso,
  ADD COLUMN dias_servicio VARCHAR(80) NOT NULL DEFAULT '' AFTER hora_salida,
  ADD COLUMN observaciones_servicio VARCHAR(255) NOT NULL DEFAULT '' AFTER dias_servicio,
  ADD KEY idx_almacen_cliente_supervisor (supervisor_id),
  ADD CONSTRAINT fk_almacen_cliente_supervisor FOREIGN KEY (supervisor_id) REFERENCES Usuarios (id_usu) ON DELETE SET NULL;
ALTER TABLE almacen_clientes DROP COLUMN supervisor;
