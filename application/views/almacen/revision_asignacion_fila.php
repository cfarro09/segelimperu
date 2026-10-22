<?php defined('BASEPATH') OR exit('No direct script access allowed'); $despacho=$a->despacho ?? null; ?>
<tr data-registro-id="<?= (int)$a->id ?>" class="<?= $a->total>$a->presupuesto?'table-danger':'' ?>">
<td><?= html_escape($a->periodo) ?></td><td><?= html_escape($a->nombre) ?></td><td class="text-right"><?= number_format($a->presupuesto,2) ?></td><td class="text-right"><?= number_format($a->total,2) ?></td><td class="text-right"><?= number_format(max(0,$a->total-$a->presupuesto),2) ?><?php if ($a->total>$a->presupuesto): ?> <strong>Excede el máximo</strong><?php endif; ?></td><td class="text-right"><?= number_format($a->saldo_acumulado,2) ?></td>
<td><?php $this->load->view('almacen/estado_asignacion',array('estado'=>$a->estado)); ?><?php if ($despacho): ?> <span class="badge badge-info">Despachada</span><?php endif; ?></td>
<td><div class="almacen-fila-acciones"><a class="btn btn-sm btn-outline-primary" href="<?= site_url('almacen/revisar_asignacion/'.$a->id) ?>"><?= $a->estado==='publicado'?'Revisar':'Ver detalle' ?></a><?php $this->load->view('almacen/despacho_acciones',array('asignacion'=>$a,'despacho'=>$despacho)); ?></div></td>
</tr>
