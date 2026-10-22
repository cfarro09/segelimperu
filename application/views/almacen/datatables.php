<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<link href="<?= base_url() ?>assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet">
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<?php if($datatable_documentos ?? true): ?>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/jszip.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/pdfmake.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/vfs_fonts.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/buttons.html5.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/buttons.colVis.min.js"></script>
<?php endif; ?>
<script src="<?= base_url() ?>assets/js/datatables-segelim.js?v=<?= filemtime(FCPATH.'assets/js/datatables-segelim.js') ?>"></script>
<script>
jQuery(function($) {
    $('table[data-almacen-tabla]').each(function() {
        var elemento=this, control=$(elemento).closest('.card-box').find('[data-stock-filtro]'), interruptor=control.find('input')[0];
        if($.fn.tooltip) {
            var tablaAyudas=$(elemento), modalEditor=$('#almacen-editor, #almacen-asignaciones');
            function cerrarAyudas() {
                tablaAyudas.find('[data-almacen-tooltip]').each(function() {
                    if($(this).data('bs.tooltip')) {
                        $(this).tooltip('hide');
                        $(this).tooltip('dispose');
                    }
                });
            }
            tablaAyudas.tooltip({selector:'[data-almacen-tooltip]',container:'body',boundary:'window',trigger:'hover focus',animation:false});
            tablaAyudas.on('preDraw.dt.almacenTooltip',cerrarAyudas);
            tablaAyudas.on('click.almacenTooltip','[data-almacen-tooltip]',cerrarAyudas);
            tablaAyudas.on('keydown.almacenTooltip',function(evento) { if(evento.key==='Escape') cerrarAyudas(); });
            modalEditor.on('show.bs.modal',cerrarAyudas);
            tablaAyudas.one('destroy.dt',function() {
                cerrarAyudas(); tablaAyudas.tooltip('dispose'); tablaAyudas.off('.almacenTooltip');
                modalEditor.off('show.bs.modal',cerrarAyudas);
            });
        }
        var controlFumi=$(elemento).closest('.card-box').find('[data-fumi-filtro]'), fumi=controlFumi.find('input')[0];
        if(fumi && elemento.hasAttribute('data-filtrar-fumi')) {
            var filtroFumi=function(configuracion,datos,indice) {
                if(configuracion.nTable!==elemento || !fumi.checked) return true;
                return configuracion.aoData[indice].nTr.dataset.categoria==='VENENOS';
            };
            $.fn.dataTable.ext.search.push(filtroFumi);
            $(elemento).one('destroy.dt',function() { var i=$.fn.dataTable.ext.search.indexOf(filtroFumi); if(i!==-1) $.fn.dataTable.ext.search.splice(i,1); });
        }
        if(interruptor && elemento.hasAttribute('data-filtrar-stock')) {
            var filtroStock=function(configuracion,datos,indice) {
                if(configuracion.nTable!==elemento || !interruptor.checked) return true;
                var fila=configuracion.aoData[indice].nTr;
                return fila.dataset.stockConfirmado==='1' && Number(fila.dataset.stock)>0;
            };
            $.fn.dataTable.ext.search.push(filtroStock);
            $(elemento).one('destroy.dt',function() { var indice=$.fn.dataTable.ext.search.indexOf(filtroStock); if(indice!==-1) $.fn.dataTable.ext.search.splice(indice,1); });
        }
        $(this).DataTable({buttons:['excel','pdf','colvis'], order:[], pageLength:25, autoWidth:false, search:{search:this.dataset.buscar || ''}, columnDefs:elemento.hasAttribute('data-valores-stock')?[{targets:8,type:'num-fmt'}]:[],
            initComplete:function() {
                var tabla=$(this), contenedor=tabla.closest('.table-responsive'), controles=tabla.closest('.dataTables_wrapper');
                contenedor.before(controles); tabla.wrap('<div class="table-responsive"></div>'); contenedor.remove();
                if(elemento.hasAttribute('data-filtros-columna')) SegelimDataTables.filtrosColumna(this.api());
                if(fumi) {
                    controles.find('.dt-controles').append(controlFumi);
                    fumi.setAttribute('aria-controls',elemento.id);
                    $(fumi).on('change',function() { tabla.DataTable().draw(); });
                }
                if(interruptor) {
                    controles.find('.dt-controles').append(control);
                    interruptor.setAttribute('aria-controls',elemento.id);
                    $(interruptor).on('change',function() { tabla.DataTable().draw(); });
                }
            },
            drawCallback:function() {
                if(!elemento.hasAttribute('data-valores-stock')) return;
                var resumen=elemento.closest('.content').querySelector('[data-resumen-stock]');
                if(!resumen) return;
                var exacto=typeof BigInt==='function', real=exacto?BigInt(0):0, valorado=real, pendientes=0;
                function centimos(valor) {
                    if(!exacto) return Math.round(Number(valor || 0)*100);
                    var partes=String(valor || '0').split('.');
                    return BigInt(partes[0])*BigInt(100)+BigInt((partes[1] || '').padEnd(2,'0').slice(0,2));
                }
                function dinero(valor) {
                    if(!exacto) return (valor/100).toLocaleString('es-PE',{minimumFractionDigits:2,maximumFractionDigits:2});
                    return (valor/BigInt(100)).toLocaleString('es-PE')+'.'+(valor%BigInt(100)).toString().padStart(2,'0');
                }
                this.api().rows({search:'applied'}).nodes().toArray().forEach(function(fila) {
                    if(fila.dataset.stockConfirmado!=='1') { pendientes++; return; }
                    real+=centimos(fila.dataset.valorStockReal); valorado+=centimos(fila.dataset.valorStockValorado);
                });
                resumen.querySelector('#productos-valor-real').textContent=dinero(real);
                resumen.querySelector('#productos-valor-valorado').textContent=dinero(valorado);
                var aviso=resumen.querySelector('#productos-conteo-pendiente'); aviso.hidden=pendientes===0; aviso.querySelector('span').textContent=pendientes;
            }
        });
    });
});
</script>
