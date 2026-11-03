<?php
$hora = (int) date('G');
$saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
$nombre = preg_split('/\s+/', trim((string) $this->session->userdata('nombre_usu')))[0];
$dias = array('domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado');
$meses = array(1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
$hoy = ucfirst($dias[(int) date('w')]).' '.date('j').' de '.$meses[(int) date('n')].' de '.date('Y');
$indicador = function ($url, $tono, $icono, $n, $texto, $alerta = '') {
    return '<a class="inicio-indicador tono-'.$tono.($n ? '' : ' en-cero').'" href="'.site_url($url).'"><i class="mdi '.$icono.'" aria-hidden="true"></i><div><strong>'.(int) $n.'</strong><span>'.html_escape($texto).'</span>'.($alerta !== '' ? '<small>'.html_escape($alerta).'</small>' : '').'</div></a>';
};
?>
<div class="content inicio">
    <div class="container-fluid">
        <div class="inicio-saludo">
            <div>
                <h2><?= html_escape($saludo.($nombre !== '' ? ', '.$nombre : '')) ?></h2>
                <p><?= html_escape($hoy) ?></p>
            </div>
        </div>

        <?php if (!empty($es_admin)): $i = $indicadores; ?>
        <h5 class="inicio-titulo">Por atender en almacén</h5>
        <div class="inicio-indicadores">
            <?= $indicador('almacen/revision_asignaciones?periodo='.$i['revisar_periodo'], 'azul', 'mdi-calendar', $i['revisar'], $i['revisar'] === 1 ? 'Asignación por revisar' : 'Asignaciones por revisar') ?>
            <?= $indicador('almacen/revision_asignaciones?periodo='.$i['aprobar_periodo'].'&vista=compra', 'ambar', 'mdi-cart', $i['aprobar'], $i['aprobar'] === 1 ? 'Orden de compra por aprobar' : 'Órdenes de compra por aprobar') ?>
            <?= $indicador('almacen/pendientes', 'rojo', 'mdi-clipboard-alert', $i['pendientes'], $i['pendientes'] === 1 ? 'Pendiente abierto' : 'Pendientes abiertos', $i['urgentes'] ? $i['urgentes'].($i['urgentes'] === 1 ? ' urgente' : ' urgentes') : '') ?>
            <?= $indicador('almacen/cargos?vista=pendientes', 'verde', 'mdi-file-check', $i['cargos_subir'], $i['cargos_subir'] === 1 ? 'Guía sin cargo' : 'Guías sin cargo', $i['cargos_verificar'] ? $i['cargos_verificar'].' por verificar' : '') ?>
        </div>

        <h5 class="inicio-titulo">Accesos rápidos</h5>
        <div class="inicio-accesos">
            <a class="inicio-acceso" href="<?= site_url('almacen/productos') ?>"><i class="mdi mdi-package-variant" aria-hidden="true"></i>Productos e inventario</a>
            <a class="inicio-acceso" href="<?= site_url('almacen/clientes') ?>"><i class="mdi mdi-account-multiple" aria-hidden="true"></i>Clientes</a>
            <a class="inicio-acceso" href="<?= site_url('almacen/asignacion') ?>"><i class="mdi mdi-calendar" aria-hidden="true"></i>Asignación mensual</a>
            <a class="inicio-acceso" href="<?= site_url('almacen/revision_asignaciones') ?>"><i class="mdi mdi-check-circle" aria-hidden="true"></i>Administración de almacén</a>
            <a class="inicio-acceso" href="<?= site_url('contratos') ?>"><i class="mdi mdi-file-document-box" aria-hidden="true"></i>Contratos</a>
            <a class="inicio-acceso" href="<?= site_url('personal/asistencia') ?>"><i class="mdi mdi-account-card-details" aria-hidden="true"></i>Control de asistencias</a>
            <a class="inicio-acceso" href="<?= site_url('cecon') ?>"><i class="mdi mdi-factory" aria-hidden="true"></i>Operaciones (CECON)</a>
        </div>
        <?php else: ?>
        <div class="card-box"><p class="mb-0">Elige una opción del menú lateral para empezar.</p></div>
        <?php endif; ?>
    </div>
</div>
