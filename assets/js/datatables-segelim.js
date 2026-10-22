(function ($) {
    'use strict';
    if (!$ || !$.fn.dataTable) return;
    var dt = $.fn.dataTable;
    var dom = "<'row dt-controles'<'dt-exportar'B><'dt-buscar'f>>rt<'row dt-pie'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'i><'col-sm-12 col-md-4'p>>";
    var language = {
        emptyTable: 'No hay registros.', zeroRecords: 'No se encontraron coincidencias.',
        info: '<span class="sr-only">Registros </span>_START_–_END_ de _TOTAL_', infoEmpty: '0 registros',
        infoFiltered: ' <span class="dt-filtrado">(de _MAX_ en total)</span>', lengthMenu: '<span class="sr-only">Filas por página</span> _MENU_',
        loadingRecords: 'Cargando…', processing: 'Procesando…', search: '<span class="sr-only">Buscar en la tabla</span>',
        searchPlaceholder: 'Buscar en la tabla', thousands: ',',
        paginate: {first: 'Primera', last: 'Última', next: 'Siguiente', previous: 'Anterior'},
        aria: {sortAscending: ': ordenar de menor a mayor', sortDescending: ': ordenar de mayor a menor'},
        buttons: {
            excel: '<i class="mdi mdi-file-excel" aria-hidden="true"></i> Excel',
            pdf: '<i class="mdi mdi-file-pdf" aria-hidden="true"></i> PDF',
            print: '<i class="mdi mdi-printer" aria-hidden="true"></i> Imprimir',
            colvis: '<i class="mdi mdi-view-column" aria-hidden="true"></i> Columnas',
            copy: 'Copiar', copyTitle: 'Datos copiados', copySuccess: {1: 'Se copió una fila', _: 'Se copiaron %d filas'}
        }
    };
    $.extend(true, dt.defaults, {language: language, dom: dom, pageLength: 25, lengthMenu: [10,25,50,100]});
    ['string','html'].forEach(function(tipo) {
        var original=dt.ext.type.search[tipo];
        dt.ext.type.search[tipo]=function(data) {
            var texto=original(data);
            return typeof texto==='string' ? texto+' '+texto.normalize('NFD').replace(/[\u0300-\u036f]/g,'') : texto;
        };
    });
    $('table thead th').each(function () {
        if (/^(acci[oó]n(?:es)?|actions?|ajustes|opciones)$/i.test($(this).text().trim())) $(this).addClass('no-export');
        if (/^actions?$/i.test($(this).text().trim())) $(this).text('Acciones');
    });
    function texto(data) {
        var template = document.createElement('template'); template.innerHTML = String(data == null ? '' : data);
        return template.content.textContent.trim();
    }
    function titulo() {
        var h = document.querySelector('.almacen-cabecera h4, .content .header-title, .content .page-title');
        return h ? h.textContent.trim() : 'Segelim Perú';
    }
    var opciones = {columns: ':visible:not(.no-export)', modifier: {search: 'applied', order: 'applied', page: 'all'}, format: {body: texto}};

    // Style the XLSX that Buttons creates; keep the installed export engine.
    function formatoExcel(xlsx) {
        var ns = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
        var styles = xlsx.xl['styles.xml'], sheet = xlsx.xl.worksheets['sheet1.xml'];
        function elemento(doc, tag, attrs, parent) {
            var node = doc.createElementNS(ns, tag);
            Object.keys(attrs || {}).forEach(function (key) { node.setAttribute(key, attrs[key]); });
            if (parent) parent.appendChild(node); return node;
        }
        function agregar(tag, node) {
            var parent = styles.getElementsByTagName(tag)[0], index = parent.children.length;
            parent.appendChild(node); parent.setAttribute('count', parent.children.length); return index;
        }
        // The installed Buttons 1.2.2 template leaves its second fill empty.
        // Complete the reserved fill so independent XLSX readers accept it.
        Array.from(styles.getElementsByTagName('fills')[0].children).forEach(function(fill,index) {
            if(!fill.children.length) elemento(styles,'patternFill',{patternType:index===1?'gray125':'none'},fill);
        });
        function relleno(color) {
            var fill = elemento(styles, 'fill'), pattern = elemento(styles, 'patternFill', {patternType:'solid'}, fill);
            elemento(styles,'fgColor',{rgb:color},pattern); elemento(styles,'bgColor',{indexed:64},pattern);
            return agregar('fills', fill);
        }
        var font = elemento(styles,'font'); elemento(styles,'b',{},font); elemento(styles,'sz',{val:11},font);
        elemento(styles,'color',{rgb:'FFFFFFFF'},font); elemento(styles,'name',{val:'Calibri'},font);
        var fuente = agregar('fonts',font), azul = relleno('FF225D97'), gris = relleno('FFF3F6FA');
        var formats = styles.getElementsByTagName('numFmts')[0];
        if (!formats) { formats = elemento(styles,'numFmts',{count:0}); styles.documentElement.insertBefore(formats,styles.documentElement.firstChild); }
        var monedaId = 164;
        Array.from(formats.children).forEach(function (f) { monedaId = Math.max(monedaId, Number(f.getAttribute('numFmtId'))+1); });
        elemento(styles,'numFmt',{numFmtId:monedaId,formatCode:'"S/ " #,##0.00;[Red]-"S/ " #,##0.00'},formats);
        elemento(styles,'numFmt',{numFmtId:monedaId+1,formatCode:'#,##0.000'},formats); formats.setAttribute('count',formats.children.length);
        function estilo(fill,fontId,numFmt) {
            var xf = elemento(styles,'xf',{numFmtId:numFmt || 0,fontId:fontId || 0,fillId:fill || 0,borderId:0,xfId:0,applyFill:1,applyFont:1,applyAlignment:1,applyNumberFormat:1});
            elemento(styles,'alignment',{vertical:'center',wrapText:1},xf); return agregar('cellXfs',xf);
        }
        var cabecera = estilo(azul,fuente), normal = estilo(0,0), alterno = estilo(gris,0);
        var monetarios = [estilo(0,0,monedaId),estilo(gris,0,monedaId)];
        var cantidades = [estilo(0,0,monedaId+1),estilo(gris,0,monedaId+1)];
        var filas = Array.from(sheet.getElementsByTagName('row')), tipos = [];
        if (!filas.length) return;
        Array.from(filas[0].children).forEach(function (cell, index) {
            var header = cell.textContent.trim();
            tipos[index] = /S\/|precio|presupuesto|importe|monto|saldo|total|valor|exceso|diferencia/i.test(header) ? 'moneda' : /cantidad|stock|solicitado|sugerido|adquirir/i.test(header) ? 'cantidad' : '';
            cell.setAttribute('s',cabecera);
        });
        var anchos=Array.from(sheet.getElementsByTagName('col')).map(function(col,index) {
            var ancho=Math.max(tipos[index]==='moneda'?22:tipos[index]==='cantidad'?18:12,Math.min(55,Number(col.getAttribute('width'))+2));
            col.setAttribute('width',ancho); return ancho;
        });
        filas.forEach(function (fila,index) {
            fila.setAttribute('ht',index ? 28 : 32); fila.setAttribute('customHeight',1);
            if (!index) return;
            var alto=28;
            Array.from(fila.children).forEach(function (cell,col) {
                var esNumero = cell.getAttribute('t') === 'n', valor = cell.textContent.trim(), limpio=valor.replace(/^S\/\s*/i,'');
                alto=Math.max(alto,Math.ceil(valor.length/(anchos[col] || 12))*15);
                if (tipos[col] && /^-?\d[\d,]*(?:\.\d+)?$/.test(limpio)) {
                    var numeric = Number(limpio.replace(/,/g,''));
                    cell.textContent = ''; cell.setAttribute('t','n'); elemento(sheet,'v',{},cell).textContent = numeric; esNumero = true;
                }
                cell.setAttribute('s',esNumero && tipos[col] ? (tipos[col] === 'moneda' ? monetarios : cantidades)[index%2] : (index%2 ? alterno : normal));
            });
            fila.setAttribute('ht',alto);
        });
        var last = filas[0].lastElementChild.getAttribute('r').replace(/\d+$/,'');
        var root = sheet.documentElement, views = elemento(sheet,'sheetViews');
        var view = elemento(sheet,'sheetView',{workbookViewId:0},views);
        elemento(sheet,'pane',{ySplit:1,topLeftCell:'A2',activePane:'bottomLeft',state:'frozen'},view);
        root.insertBefore(views,root.firstChild);
        var filter = elemento(sheet,'autoFilter',{ref:'A1:'+last+filas.length});
        var after = sheet.getElementsByTagName('sheetData')[0].nextSibling;
        root.insertBefore(filter,after);
    }
    function formatoPdf(doc) {
        doc.pageMargins = [24,32,24,32]; doc.defaultStyle.fontSize = 9;
        doc.styles.tableHeader.fillColor = '#225d97'; doc.styles.tableHeader.fontSize = 10;
        doc.styles.tableBodyOdd.fillColor = '#f3f6fa'; doc.styles.title.color = '#225d97';
        doc.footer = function (pagina,paginas) { return {text:'Página '+pagina+' de '+paginas,alignment:'right',margin:[24,0,24,0],fontSize:8}; };
    }
    var botones = dt.ext.buttons || {};
    if (botones.excelHtml5) $.extend(true,botones.excelHtml5,{filename:titulo,sheetName:'Datos',exportOptions:opciones,customize:formatoExcel});
    if (botones.pdfHtml5) $.extend(true,botones.pdfHtml5,{title:titulo,exportOptions:opciones,orientation:'landscape',customize:formatoPdf});
    if (botones.print) $.extend(true,botones.print,{title:titulo,exportOptions:opciones});
    if (botones.colvis) botones.colvis.text = language.buttons.colvis;
    var VACIO='__vacio__';
    // Botón "Filtros" en la barra: abre un panel con un select por cada <th data-filtro="lista">.
    // Los filtros activos se muestran como etiquetas removibles; sin filtros no ocupa espacio.
    function filtrosColumna(api) {
        var tabla=api.table().node(), wrapper=$(tabla).closest('.dataTables_wrapper'), filtros={}, columnas=[];
        api.columns().every(function(indice) {
            if(this.header().getAttribute('data-filtro')==='lista') columnas.push({indice:indice, nombre:this.header().textContent.trim()});
        });
        if(!columnas.length) return;
        var id='dt-filtros-'+(tabla.id || Math.random().toString(36).slice(2));
        var grupo=$('<div class="dt-filtro-menu"></div>');
        var boton=$('<button type="button" class="btn btn-secondary" aria-haspopup="true" aria-expanded="false"><span><i class="mdi mdi-filter-variant" aria-hidden="true"></i> Filtros <span class="badge badge-primary dt-filtro-total" hidden></span></span></button>').attr('aria-controls',id);
        var panel=$('<div class="dt-filtro-panel" role="dialog" aria-label="Filtros" hidden></div>').attr('id',id);
        columnas.forEach(function(c) {
            var campo=$('<label class="dt-filtro-campo"></label>').text(c.nombre);
            c.select=$('<select class="form-control form-control-sm"></select>').on('change',function() { aplicar(c.indice,this.value); });
            panel.append(campo.append(c.select));
        });
        panel.append($('<button type="button" class="btn btn-link btn-sm dt-filtro-limpiar">Limpiar filtros</button>').on('click',limpiar));
        grupo.append(boton,panel);
        var exportar=wrapper.find('.dt-exportar .dt-buttons');
        (exportar.length ? exportar : wrapper.find('.dt-controles').first()).append(grupo);
        var activos=$('<div class="dt-filtros-activos" aria-live="polite" hidden></div>');
        wrapper.find('.dt-controles').first().after(activos);

        function abrir(abierto) {
            panel.prop('hidden',!abierto); boton.attr('aria-expanded',String(abierto));
            if(abierto) panel.find('select').first().trigger('focus');
        }
        boton.on('click',function() { abrir(panel.prop('hidden')); });
        $(document).on('mousedown.'+id+' focusin.'+id,function(e) { if(!grupo[0].contains(e.target)) abrir(false); });
        grupo.on('keydown',function(e) { if(e.key==='Escape' && !panel.prop('hidden')) { abrir(false); boton.trigger('focus'); } });

        function aplicar(indice,valor) { if(valor) filtros[indice]=valor; else delete filtros[indice]; api.draw(); }
        function limpiar() { filtros={}; columnas.forEach(function(c) { c.select.val(''); }); api.draw(); }
        function resumen() {
            var total=0; activos.empty();
            columnas.forEach(function(c) {
                var valor=filtros[c.indice]; if(!valor) return; total++;
                var texto=c.nombre+': '+(valor===VACIO?'Sin dato':valor);
                activos.append($('<button type="button" class="dt-filtro-chip"></button>').attr('aria-label','Quitar filtro '+texto)
                    .append($('<span></span>').text(texto),'<i class="mdi mdi-close" aria-hidden="true"></i>')
                    .on('click',function() { c.select.val(''); aplicar(c.indice,''); boton.trigger('focus'); }));
            });
            if(total>1) activos.append($('<button type="button" class="btn btn-link btn-sm p-0">Limpiar todo</button>').on('click',limpiar));
            activos.prop('hidden',!total);
            boton.find('.dt-filtro-total').text(total).prop('hidden',!total);
            boton.toggleClass('dt-filtro-con-valor',total>0);
        }
        function opciones() {
            columnas.forEach(function(c) {
                var valores={}, vacio=false, select=c.select[0];
                api.column(c.indice).nodes().toArray().forEach(function(td) { var t=td.textContent.trim(); if(t) valores[t]=true; else vacio=true; });
                var lista=Object.keys(valores).sort(function(x,y) { return x.localeCompare(y,'es',{sensitivity:'base'}); });
                var firma=lista.join('\n')+(vacio?'\n'+VACIO:'');
                if(select.dataset.firma===firma) return;
                var actual=select.value; select.dataset.firma=firma; select.textContent='';
                select.add(new Option('Todos',''));
                lista.forEach(function(v) { select.add(new Option(v,v)); });
                if(vacio) select.add(new Option('(Sin dato)',VACIO));
                select.value=actual;
                if(select.value!==actual) { delete filtros[c.indice]; select.value=''; }
            });
            resumen();
        }
        var buscar=function(configuracion,datos,fila) {
            if(configuracion.nTable!==tabla) return true;
            return Object.keys(filtros).every(function(indice) {
                var texto=api.cell(fila,Number(indice)).node().textContent.trim();
                return filtros[indice]===VACIO ? texto==='' : texto===filtros[indice];
            });
        };
        dt.ext.search.push(buscar);
        opciones();
        $(tabla).on('draw.dt.filtros',opciones);
        $(tabla).one('destroy.dt',function() {
            var i=dt.ext.search.indexOf(buscar); if(i!==-1) dt.ext.search.splice(i,1);
            $(tabla).off('.filtros'); $(document).off('.'+id); grupo.remove(); activos.remove();
        });
    }
    window.SegelimDataTables = {dom:dom, language:language, filtrosColumna:filtrosColumna};
})(window.jQuery);
