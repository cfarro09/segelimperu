# Productos e inventario

Primera entrega del almacén. Acceso web: `almacen/productos`, restringido en servidor a los roles `AD` y `SA`.

Instalación aditiva e importación inicial, ejecutable únicamente por consola:

```sh
php application/sql/instalar_almacen.php
```

El instalador crea `almacen_productos` y `almacen_movimientos` con InnoDB y no modifica tablas existentes. Al repetirse omite los códigos ya importados y conserva sus datos editados.

El catálogo proviene del Excel `VALORIZACION_PROVINCIAS_5_-_PAUL_ALMACEN_2026.xlsx` compartido por el usuario. Las cuatro hojas se consolidan por descripción, unidad y marca. Contiene 250 productos, precio valorado con IGV incluido y categorías Materiales/Implementos. Cinco productos no tienen unidad en la fuente y se identifican con `POR DEFINIR`. No se importan clientes, meses ni cantidades solicitadas como existencias.

El precio real se inicializa con el mismo precio valorado y luego puede editarse de forma independiente. Al guardar un precio real vacío se usa el valorado. El instalador también completa los precios reales NULL de importaciones anteriores, conservando los precios reales ya registrados. El stock importado queda por verificar; el primer ajuste registra la cantidad física contada. Los productos creados manualmente aceptan stock inicial y registran su movimiento de apertura, incluso cuando es cero. Editar el catálogo no modifica el stock. Las entradas, salidas y ajustes conservan usuario, motivo, fecha y stock anterior/final; se ejecutan en una transacción con bloqueo de la fila para evitar actualizaciones simultáneas perdidas. Se impiden salidas superiores al stock y movimientos sobre productos inactivos. No hay eliminación física de productos ni movimientos.

Importes admiten hasta dos decimales y existencias hasta tres. Formularios verifican token de sesión y método POST; el token es propio del módulo porque la protección CSRF general está desactivada en el aplicativo actual.

Clientes y asignaciones mensuales ya disponibles. Administración a cargo de los usuarios administradores actuales; el usuario de Paul se incorporará posteriormente.

## Clientes y asignaciones mensuales

Instalación aditiva: `php application/sql/instalar_clientes_asignaciones.php`. Crea `almacen_clientes`, `almacen_asignaciones` y `almacen_asignacion_detalles`, con claves foráneas y una asignación por cliente/período. Carga los cuatro clientes de la plantilla con sus presupuestos y grupos, sin sobrescribir clientes existentes. No importa meses históricos porque los encabezados del Excel mezclan julio, agosto y septiembre.

Rutas: `almacen/clientes` y `almacen/asignaciones`, visibles en el menú para AD/SA. Clientes incluye registro, edición, búsqueda, paginación, presupuesto mensual, saldo inicial, grupo, supervisor, contacto, RUC y dirección. Dar de baja exige la referencia de un documento oficial; se conserva el historial y se impiden nuevas asignaciones y cambios a asignaciones de clientes inactivos.

La asignación presenta el catálogo por Materiales/Implementos con descripción, unidad, marca, cantidad, precio valorado y total. Totales y subtotales se recalculan al ingresar cantidades; permite buscar y ocultar ceros. El presupuesto del mes se copia del cliente y se conserva al editar la asignación. La pantalla de registro no permite elevar ese máximo. El saldo anterior es `saldo_inicial + suma(presupuesto - total)` de las asignaciones aprobadas de períodos anteriores. El saldo acumulado agrega la diferencia del mes actual. No se generan presupuestos o saldos en meses sin asignación. Los excesos muestran alertas y no impiden guardar.

Los detalles conservan descripción, categoría, unidad, marca y ambos precios al guardarse; editar cantidades de líneas existentes mantiene esos precios. Los totales se recalculan en servidor y no se aceptan totales ni precios enviados por el navegador. Solo las asignaciones aprobadas afectan los saldos de meses posteriores. Las asignaciones no descuentan inventario; las salidas físicas se registran en el módulo de inventario. Las revisiones evitan que dos sesiones sobrescriban una asignación sin detectar el conflicto.

Impresión sin productos en cero, con subtotales y resumen; exportación Excel `.xlsx` con formato y valores de la versión guardada. Ambas utilizan la asignación guardada. Validación: `php tests/almacen_asignaciones_test.php`, en tablas temporales. También se verificó por HTTP guardar y editar una asignación que excede el presupuesto, rechazo de revisiones antiguas, plantilla de 250 productos, impresión, la exportación CSV anterior, tokens y roles. Los datos creados para esa prueba se eliminaron al terminar.

Validación: `php tests/almacen_test.php` ejecuta pruebas del modelo usando tablas temporales en la conexión, sin modificar los datos del aplicativo. También se verificaron por HTTP las páginas de catálogo, alta, edición e inventario para AD/SA, la denegación para GE y el rechazo de formularios sin token.

## Apertura de plantilla

La nueva asignación abre directamente el catálogo con el primer cliente activo y el mes actual. El selector de cliente carga automáticamente su plantilla del mes actual, sin botón Mostrar plantilla ni selector de otro mes. Si el cliente/mes ya tiene asignación, abre esa asignación para editarla. Cambiar cliente con cantidades u observaciones modificadas solicita confirmar el descarte para evitar perder datos. No se crea ninguna asignación hasta pulsar Guardar.

## Publicación y revisión administrativa

Instalación: `php application/sql/instalar_flujo_asignaciones.php` (incluido al ejecutar el instalador de clientes). Añade estado, publicación y revisión a la tabla de asignaciones, y crea `almacen_asignacion_eventos` para conservar usuario, fecha y comentario de cada cambio de estado. Los registros preexistentes comienzan como borradores y el instalador posterior conserva los estados existentes.

El menú se llama Clientes. Asignación mensual abre directamente `almacen/asignacion` con la plantilla del mes actual; prioriza un cliente aún sin registro en ese mes. No permite registrar otros meses ni duplicar cliente/período. El presupuesto proviene del cliente y no acepta modificaciones enviadas por el navegador.

El supervisor puede guardar borrador o publicar en la misma operación. Publicar requiere alguna cantidad positiva y bloquea cantidades, observaciones y presupuesto. La administración consulta `almacen/revision_asignaciones`: por defecto todos los enviados del mes actual, con filtro de clientes, estados y meses para consultar el histórico. Los excesos de presupuesto aparecen en rojo. El detalle `almacen/revisar_asignacion/{id}` permite aprobar o rechazar; el rechazo exige comentario. Una asignación rechazada vuelve a ser editable y puede guardarse como borrador o publicarse nuevamente conservando su ID. Las aprobadas quedan bloqueadas. Todas las transiciones usan transacciones y revisiones para impedir decisiones duplicadas o sobrescrituras de otra sesión.

Solo las asignaciones aprobadas de meses anteriores contribuyen al saldo acumulado. Los borradores, publicados pendientes y rechazados no consumen saldo de otros meses. No se descuenta stock durante asignación, publicación o aprobación.

Los dos flujos están disponibles para los administradores actuales AD/SA. Los roles y permisos específicos de supervisor y administrador se definirán posteriormente por instrucción del usuario; no se crearon cuentas nuevas. Se verificaron por HTTP ambos flujos, protección del presupuesto, bloqueo de estados, rechazo con motivo, corrección, republicación y aprobación, y se limpiaron únicamente los datos temporales de prueba.

## Pantalla compacta y compras

La plantilla presenta Volver/Imprimir/Excel en una cabecera común arriba y Guardar borrador/Enviar a administración en la barra inferior. El catálogo usa paginación local de 10 filas (opciones 20/50), conserva todas las cantidades al buscar/cambiar de página y calcula totales sobre todas las líneas. Se retiró el contenedor de scroll vertical. Observaciones es desplegable para ahorrar espacio. Se verificaron la paginación, cantidades ocultas, totales y filtros ejecutando el JavaScript generado.

El menú Administración de almacén reúne las asignaciones enviadas del mes y su aprobación/rechazo. Permite consolidar todas las aprobadas en una sola orden mensual, independientemente del filtro de cliente mostrado. Órdenes de compra permite consultar el documento por mes, proveedor, cantidades, importe a precio real e impresión.

Instalación: `php application/sql/instalar_compras.php`, también incluido en el instalador de clientes. Crea `almacen_compras`, `almacen_compra_asignaciones` y `almacen_compra_detalles`. La orden es única por período y conserva la relación de asignaciones incluidas. Agrupa cantidades aprobadas por producto y resta existencias confirmadas para sugerir la compra. Los stocks por verificar se señalan y no se descuentan del cálculo. Actualizar consolidado incorpora aprobaciones posteriores mientras la orden sea borrador; conserva cantidades ajustadas manualmente.

Guardar cantidades modifica la orden sin afectar inventario. Registrar recepción guarda las cantidades y suma las unidades adquiridas al stock mediante movimientos de entrada en una misma transacción. La orden recibida se bloquea, evitando recepción duplicada y cambios posteriores. Una recepción no confirma automáticamente el inventario preexistente que estaba por verificar; las unidades compradas se suman y el conteo sigue pendiente. Se comprueban productos activos, unidades, límites, revisión y correspondencia con las aprobadas del mes. Si una línea falla, se revierte la operación completa. La implementación registra recepción completa de las cantidades indicadas; entregas parciales requerirían ampliar ese flujo.

Pruebas: `php tests/almacen_compras_test.php` usa tablas temporales y comprueba consolidación de varios clientes, descuento de stock, ajustes manuales, una orden por mes, recepción, bloqueo de duplicados, rollback de recepción fallida y stock por verificar. También se verificó por HTTP crear la orden, ajustar cantidades, imprimir y recibir, usando exclusivamente productos/asignaciones temporales de prueba que se eliminaron al terminar.

Los listados y la asignación mensual reutilizan DataTables para paginar. La configuración compartida `assets/js/datatables-segelim.js` deja controles en español, selector al pie y exportaciones nativas Excel/PDF con formato en las tablas. Los documentos de asignación y compra usan PHP_XLSXWriter 0.39 y conservan datos históricos y resumen. Ver la sección 17 de `docs/ALMACEN_IMPLEMENTACION.md` para alcance y pruebas.

Productos y Clientes muestran activos e inactivos sin un filtro de estado adicional. Nuevo/Editar reutilizan modales de Bootstrap y guardan desde el listado, manteniendo búsqueda y página de DataTables. Formularios y modelos conservan permisos, token y validaciones. Ver la sección 18 de `docs/ALMACEN_IMPLEMENTACION.md`; pruebas aisladas: `php tests/almacen_modal_test.php` y `node tests/almacen_modal_browser_test.cjs`.

Productos tiene acciones separadas de Movimiento e Historial, ambas en modal: la primera permite registrar con revisión previa y la segunda abre directamente los registros, sin controles de guardado. Se conserva la página y búsqueda. Las cabeceras y tablas usan espacios compactos, buscador flexible con placeholder y etiqueta accesible oculta, y exportaciones centradas; se conserva el tamaño táctil en teléfonos. Ver la sección 19 del documento y las mismas pruebas de modales, además de `php tests/almacen_test.php` para el modelo con tablas temporales.

Productos incluye el interruptor Solo con stock, combinado con búsqueda y exportación de DataTables. Filtra stock confirmado mayor que cero, con controles adaptados a teléfonos. Movimiento e Historial se consultan desde Productos. Ver la sección 20 del documento.

Los valores del almacén se consultan en Productos e inventario: tarjetas a precio real y a precio valorado, y columna Valor del stock S/ (`precio_valorado × stock confirmado`). Los importes siguen búsqueda y Solo con stock, excluyen inventario pendiente y se actualizan al guardar precios o movimientos; la columna se exporta como moneda. Ver la sección 28 del documento. No se reactiva la ruta independiente retirada ni se requiere una migración.
