<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="almacen-asignaciones" class="modal almacen-editor almacen-asignaciones-modal" tabindex="-1" role="dialog" aria-labelledby="almacen-asignaciones-titulo">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="almacen-asignaciones-titulo">Asignaciones</h5><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
            <div class="modal-body" id="almacen-asignaciones-contenido" aria-busy="false"></div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button></div>
        </div>
    </div>
</div>
<script src="<?= base_url() ?>assets/js/almacen-clientes-asignaciones.js?v=<?= filemtime(FCPATH.'assets/js/almacen-clientes-asignaciones.js') ?>"></script>
