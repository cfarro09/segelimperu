<?php defined('BASEPATH') OR exit('No direct script access allowed');
$estado=Cecon_model::ESTADOS[$t->status] ?? array($t->status, 'badge-secondary'); ?>
<tr data-registro-id="<?= (int)$t->id ?>">
    <td><?= html_escape($t->ticket_code) ?></td>
    <td data-order="<?= html_escape($t->created_at) ?>"><?= html_escape(Cecon_model::fecha($t->created_at)) ?></td>
    <td><?= $t->cliente!==null && $t->cliente!==''?html_escape($t->cliente):'—' ?></td>
    <td><?= html_escape($t->categoria) ?></td>
    <td><?= html_escape($t->area) ?></td>
    <td><?= html_escape($t->supervisor) ?></td>
    <td><span class="badge <?= html_escape($estado[1]) ?>"><?= html_escape($estado[0]) ?></span></td>
    <td class="pendiente-detalle"><?= trim((string)$t->supervisor_comment)!==''?nl2br(html_escape($t->supervisor_comment)):'—' ?></td>
    <td class="pendiente-detalle"><?php if($t->descargo!==null): ?><?= nl2br(html_escape($t->descargo)) ?><div class="small text-muted"><?= html_escape(trim(($t->descargo_por ?? '').' · '.Cecon_model::fecha($t->descargo_en), ' ·')) ?></div><?php else: ?>—<?php endif; ?></td>
    <td data-order="<?= html_escape((string)$t->closed_at) ?>"><?= $t->closed_at?html_escape(Cecon_model::fecha($t->closed_at)):'—' ?></td>
</tr>
