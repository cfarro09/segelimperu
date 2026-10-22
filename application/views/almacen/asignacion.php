<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$puede_editar_asignacion = $puede_editar_asignacion ?? true;
$volver_asignaciones = $volver_asignaciones ?? false;
$editable = $puede_editar_asignacion && $asignacion && $cliente && $cliente->activo && in_array($asignacion->estado, array('borrador','rechazado'), true) && $asignacion->periodo === date('Y-m');
$estados = array('sin_registrar'=>'Sin registrar','borrador'=>'Borrador','publicado'=>'Pendiente de revisión','aprobado'=>'Aprobada','rechazado'=>'Necesita corrección');
$meses = array(1=>'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
$mes = $meses[(int)date('n')].' de '.date('Y');
?>
<div class="content <?= $editable?'asignacion-editable':'' ?>"><div class="container-fluid">
    <?php
    $acciones_cabecera=$volver_asignaciones ? array(array('texto'=>'Volver','icono'=>'mdi-arrow-left','clase'=>'btn-light','aria'=>'Volver a asignaciones','url'=>site_url('almacen/asignaciones'))) : array();
    if($asignacion && $asignacion->id) {
        $acciones_cabecera[]=array('texto'=>'Imprimir','icono'=>'mdi-printer','url'=>site_url('almacen/imprimir_asignacion/'.$asignacion->id),'documento'=>true,'nueva_pestana'=>true);
        $acciones_cabecera[]=array('texto'=>'Excel','icono'=>'mdi-file-excel','url'=>site_url('almacen/exportar_asignacion/'.$asignacion->id),'documento'=>true,'aria'=>'Exportar asignación a Excel');
    }
    $this->load->view('almacen/cabecera', array('cabecera_titulo'=>'Asignación mensual · '.$mes, 'cabecera_acciones'=>$acciones_cabecera, 'cabecera_asignacion'=>true, 'cabecera_estado'=>$asignacion?$asignacion->estado:null, 'cabecera_solo_lectura'=>!$puede_editar_asignacion));
    ?>
    <?php if($mensaje=$this->session->flashdata('almacen_ok')): ?><div class="alert alert-success" role="status"><?= html_escape($mensaje) ?></div><?php endif; ?>
    <?php if($errores): ?>
    <div class="alert alert-danger" role="alert"><strong>Revisa estos campos:</strong><ul class="mb-0">
        <?php foreach($errores as $error): ?><li><?= html_escape($error) ?></li><?php endforeach; ?>
    </ul><?php foreach($errores_cantidades as $pid=>$error): if(!isset($productos[$pid])) continue; ?>
        <a class="d-block mt-2" href="#cant-<?= (int)$pid ?>" data-error-producto="<?= (int)$pid ?>">Corregir <?= html_escape($productos[$pid]->descripcion) ?></a>
    <?php endforeach; ?></div>
    <?php endif; ?>
    <div class="card-box asignacion-cliente">
        <form id="contexto-asignacion" method="get" action="<?= site_url('almacen/asignacion') ?>" class="asignacion-contexto">
            <?php if($volver_asignaciones): ?><input type="hidden" name="origen" value="asignaciones"><?php endif; ?>
            <label for="elegir_cliente">Cliente<?php if($cliente && $cliente->grupo): ?> <span class="text-muted">· <?= html_escape($cliente->grupo) ?></span><?php endif; ?></label>
            <select class="form-control" id="elegir_cliente" name="cliente_id" required data-select-buscable>
                <?php if(!$cliente): ?><option value="">Selecciona un cliente</option><?php endif; ?>
                <?php if($cliente && !$cliente->activo): ?><option value="<?= (int)$cliente->id ?>" selected><?= html_escape($cliente->nombre) ?> (inactivo)</option><?php endif; ?>
                <?php foreach($clientes as $c): ?><option value="<?= (int)$c->id ?>" <?= $cliente && $cliente->id==$c->id?'selected':'' ?>><?= html_escape($c->nombre) ?> · <?= html_escape($estados[$c->estado_mes] ?? 'Sin registrar') ?></option><?php endforeach; ?>
            </select>
            <noscript><button type="submit" class="btn btn-secondary mt-2">Abrir cliente</button></noscript>
        </form>
    </div>
    <?php if($asignacion && $cliente): ?>
    <?php if($asignacion->estado==='rechazado' && $asignacion->comentario_revision!==''): ?><div class="alert alert-warning"><strong>Motivo de devolución:</strong> <?= html_escape($asignacion->comentario_revision) ?></div><?php endif; ?>
    <?= form_open('almacen/guardar_asignacion', array('id'=>'form-asignacion','data-guardar-formulario'=>$editable?'1':'0','data-error-inicial'=>$errores?'1':'0','data-cliente'=>$cliente->nombre,'data-periodo'=>$mes,'data-saldo-anterior'=>(string)$saldo_anterior)) ?>
        <input type="hidden" name="token" value="<?= html_escape($token) ?>">
        <?php if($volver_asignaciones): ?><input type="hidden" name="origen" value="asignaciones"><?php endif; ?>
        <input type="hidden" name="id" value="<?= (int)$asignacion->id ?>">
        <input type="hidden" name="revision" value="<?= (int)$asignacion->revision ?>">
        <input type="hidden" name="cliente_id" value="<?= (int)$cliente->id ?>">
        <input type="hidden" name="periodo" value="<?= html_escape($asignacion->periodo) ?>">
        <input type="hidden" id="presupuesto_mes" name="presupuesto" value="<?= html_escape($asignacion->presupuesto) ?>">
        <div class="card-box">
            <details class="asignacion-finanzas">
                <summary>Presupuesto: <strong>S/ <?= number_format($asignacion->presupuesto,2) ?></strong></summary>
                <div class="asignacion-resumen" aria-live="polite">
                <div class="asignacion-cifras">
                    <div><span class="asignacion-cifra-etiqueta">Presupuesto mensual</span><strong>S/ <?= number_format($asignacion->presupuesto,2) ?></strong></div>
                    <div><span class="asignacion-cifra-etiqueta">Total solicitado</span><strong>S/ <span id="total-general">0.00</span></strong></div>
                    <div><span class="asignacion-cifra-etiqueta">Saldo anterior</span><strong>S/ <?= number_format($saldo_anterior,2) ?></strong></div>
                    <div><span class="asignacion-cifra-etiqueta" id="diferencia-etiqueta">Disponible del mes</span><strong>S/ <span id="diferencia-mes">0.00</span></strong></div>
                    <div><span class="asignacion-cifra-etiqueta">Saldo acumulado</span><strong>S/ <span id="saldo-acumulado">0.00</span></strong></div>
                </div>
                </div>
            </details>
            <div id="alerta-presupuesto" class="alert alert-warning mb-3" hidden>Presupuesto mensual excedido.</div>
            <div id="alerta-saldo" class="alert alert-warning mb-3" hidden>Saldo acumulado negativo.</div>
            <div class="asignacion-filtros">
                <div class="asignacion-busqueda"><label for="buscar-producto" class="sr-only">Buscar producto</label><input type="search" id="buscar-producto" class="form-control" placeholder="Buscar producto, unidad o marca" autocomplete="off"></div>
                <label class="asignacion-solicitados"><input id="solo-solicitados" type="checkbox" <?= !$editable?'checked':'' ?>> Solo solicitados (<span id="solicitados-cantidad">0</span>)</label>
            </div>
            <div class="table-responsive asignacion-tabla-contenedor">
                <table class="table table-bordered asignacion-tabla mb-0" aria-label="Productos de la asignación mensual">
                    <thead><tr><th>Producto</th><th>Unidad</th><th>Marca</th><th>Cantidad</th><th>Precio S/</th><th>Importe S/</th></tr></thead><tbody>
                    <?php $categoria=null; foreach($productos as $p): ?>
                    <?php if($categoria!==$p->categoria): $categoria=$p->categoria; ?>
                    <tr class="asignacion-categoria" data-categoria="<?= html_escape($categoria) ?>"><td colspan="6"><?= html_escape($categoria) ?><span class="asignacion-categoria-subtotal">Subtotal solicitado: S/ <span class="subtotal-categoria" data-categoria="<?= html_escape($categoria) ?>">0.00</span></span></td></tr>
                    <?php endif; $error_cantidad=$errores_cantidades[$p->id] ?? ''; ?>
                    <tr class="asignacion-producto <?= $error_cantidad?'tiene-error':'' ?>" data-producto-id="<?= (int)$p->id ?>" data-categoria="<?= html_escape($p->categoria) ?>" data-precio="<?= html_escape($p->precio_valorado) ?>" data-busqueda="<?= html_escape($p->descripcion.' '.$p->unidad.' '.$p->marca) ?>">
                        <td class="asignacion-descripcion"><?= html_escape($p->descripcion) ?></td>
                        <td class="asignacion-unidad"><span class="asignacion-etiqueta-movil" aria-hidden="true">Unidad</span> <?= html_escape($p->unidad) ?></td>
                        <td class="asignacion-marca"><span class="asignacion-etiqueta-movil" aria-hidden="true">Marca</span> <?= html_escape($p->marca ?: 'Sin marca') ?></td>
                        <td class="asignacion-cantidad text-right">
                            <label class="sr-only" for="cant-<?= (int)$p->id ?>">Cantidad de <?= html_escape($p->descripcion.' '.$p->unidad.' '.$p->marca) ?></label>
                            <div class="input-group asignacion-cantidad-control <?= !$editable?'asignacion-cantidad-lectura':'' ?>">
                            <?php if($editable): ?><div class="input-group-prepend"><button type="button" class="btn btn-outline-secondary" data-cantidad-paso="-1" aria-controls="cant-<?= (int)$p->id ?>" aria-label="Disminuir en uno la cantidad de <?= html_escape($p->descripcion.' '.$p->unidad.' '.$p->marca) ?>" disabled><i class="mdi mdi-minus" aria-hidden="true"></i></button></div><?php endif; ?>
                            <input id="cant-<?= (int)$p->id ?>" name="cantidades[<?= (int)$p->id ?>]" type="text" inputmode="decimal" pattern="[0-9]{1,9}([.,][0-9]{1,3})?" maxlength="13" class="form-control cantidad <?= $error_cantidad?'is-invalid':'' ?>" value="<?= html_escape($p->cantidad) ?>" aria-describedby="error-cant-<?= (int)$p->id ?>" <?= $error_cantidad?'aria-invalid="true"':'' ?> <?= !$editable?'disabled':'' ?> autocomplete="off">
                            <?php if($editable): ?><div class="input-group-append"><button type="button" class="btn btn-outline-secondary" data-cantidad-paso="1" aria-controls="cant-<?= (int)$p->id ?>" aria-label="Aumentar en uno la cantidad de <?= html_escape($p->descripcion.' '.$p->unidad.' '.$p->marca) ?>" disabled><i class="mdi mdi-plus" aria-hidden="true"></i></button></div><?php endif; ?>
                            </div>
                            <div class="invalid-feedback" id="error-cant-<?= (int)$p->id ?>"><?= html_escape($error_cantidad ?: 'Usa una cantidad positiva o cero, con hasta 3 decimales. Ejemplo: 1,5.') ?></div>
                        </td>
                        <td class="monto asignacion-precio"><span class="asignacion-etiqueta-movil" aria-hidden="true">Precio S/</span><span class="asignacion-precio-valor"><?= number_format($p->precio_valorado,2) ?></span></td>
                        <td class="monto asignacion-importe"><span class="asignacion-etiqueta-movil" aria-hidden="true">Importe S/</span><strong class="total-linea">0.00</strong></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="productos-vacio" class="asignacion-vacio" role="status" hidden><p id="productos-vacio-texto">No se encontraron productos.</p><button type="button" id="productos-vacio-limpiar" class="btn btn-outline-secondary">Mostrar todos los productos</button></div>
            <details class="asignacion-observaciones" <?= $asignacion->observaciones!==''?'open':'' ?>><summary>Observaciones <?= $editable?'(opcional)':'' ?></summary><label for="observaciones" class="sr-only">Observaciones de la solicitud</label><textarea id="observaciones" name="observaciones" class="form-control" maxlength="1000" rows="3" <?= !$editable?'readonly':'' ?>><?= html_escape($asignacion->observaciones) ?></textarea></details>
        </div>
        <div class="asignacion-barra">
            <div class="asignacion-barra-total"><span>Total: S/ <span id="total-barra">0.00</span></span><span id="estado-guardado" class="asignacion-guardado" role="status"><?= $asignacion->id?'Versión guardada':'Borrador sin guardar' ?></span></div>
            <?php if($editable): ?><div class="asignacion-acciones"><button type="submit" name="accion" value="borrador" class="btn btn-secondary">Guardar borrador</button><button type="submit" name="accion" value="publicar" class="btn btn-primary">Enviar a administración</button></div><?php endif; ?>
        </div>
    <?= form_close() ?>
    <?php else: ?><div class="alert alert-info">Sin cliente activo. <?php if($this->session->userdata('tipo_usu')!=='SP'): ?><a href="<?= site_url('almacen/cliente') ?>">Nuevo cliente</a><?php endif; ?></div><?php endif; ?>
</div></div>

<?php $this->load->view('almacen/datatables',array('datatable_documentos'=>false)); ?>
