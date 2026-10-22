(function () {
    'use strict';
    var formatoCantidad = /^[0-9]{1,9}(?:[.,][0-9]{1,3})?$/;
    function dinero(n) { return n.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }
    function cantidad(valor) { return formatoCantidad.test(valor) ? Number(valor.replace(',', '.')) : 0; }
    function unidades(n) { return n.toLocaleString('es-PE', {maximumFractionDigits: 3}); }
    function normalizarTexto(s) { return s.toLocaleLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim(); }

    function confirmar(titulo, mensaje, detalles, accion) {
        if (!window.jQuery || !window.jQuery.fn.modal) {
            return Promise.resolve(window.confirm(titulo + '\n\n' + mensaje + '\n\n' + detalles.map(function(d) { return d[0] + ': ' + d[1]; }).join('\n')));
        }
        var modal = document.getElementById('almacen-confirmacion');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'almacen-confirmacion';
            modal.className = 'modal almacen-confirmacion';
            modal.tabIndex = -1;
            modal.setAttribute('role', 'dialog');
            modal.setAttribute('aria-labelledby', 'almacen-confirmacion-titulo');
            modal.setAttribute('aria-describedby', 'almacen-confirmacion-mensaje');
            modal.innerHTML = '<div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="almacen-confirmacion-titulo"></h5><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"><p id="almacen-confirmacion-mensaje"></p><dl id="almacen-confirmacion-detalles" class="mb-0"></dl></div><div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal" id="almacen-confirmacion-cancelar">Volver</button><button type="button" class="btn btn-primary" id="almacen-confirmacion-aceptar"></button></div></div></div>';
            document.body.appendChild(modal);
        }
        document.getElementById('almacen-confirmacion-titulo').textContent = titulo;
        document.getElementById('almacen-confirmacion-mensaje').textContent = mensaje;
        var lista = document.getElementById('almacen-confirmacion-detalles');
        lista.textContent = '';
        detalles.forEach(function (d) {
            var etiqueta = document.createElement('dt'), valor = document.createElement('dd');
            etiqueta.textContent = d[0]; valor.textContent = d[1]; lista.appendChild(etiqueta); lista.appendChild(valor);
        });
        var boton = document.getElementById('almacen-confirmacion-aceptar');
        boton.textContent = accion;
        return new Promise(function (resolve) {
            var aceptado = false, foco = document.activeElement;
            boton.onclick = function () { aceptado = true; window.jQuery(modal).modal('hide'); };
            window.jQuery(modal).one('shown.bs.modal', function () { document.getElementById('almacen-confirmacion-cancelar').focus(); });
            window.jQuery(modal).one('hidden.bs.modal', function () {
                boton.onclick = null;
                if (foco && foco.isConnected) foco.focus();
                resolve(aceptado);
            }).modal('show');
        });
    }

    function proteger(formulario, indicador, camposExtra) {
        var activo = formulario.dataset.guardarFormulario === '1', enviando = false;
        var errorInicial = formulario.dataset.errorInicial === '1';
        function valores() {
            return JSON.stringify(Array.from(new Set(Array.from(formulario.elements).concat(camposExtra || []))).filter(function (e) {
                return e.name && !e.disabled && !e.readOnly && e.type !== 'hidden' && e.type !== 'submit' && !e.closest('.dataTables_length, .dataTables_filter');
            }).sort(function(a,b) { return a.name.localeCompare(b.name); }).map(function (e) { return [e.name, e.type === 'checkbox' ? e.checked : e.value]; }));
        }
        var inicial = valores(), textoInicial = indicador ? indicador.textContent : '';
        function modificado() { return activo && formulario.dataset.guardarFormulario === '1' && !enviando && (errorInicial || inicial !== valores()); }
        function actualizar() {
            if (indicador) {
                indicador.textContent = modificado() ? 'Cambios sin guardar' : textoInicial;
                indicador.classList.toggle('sin-guardar', modificado());
            }
        }
        formulario.addEventListener('input', actualizar);
        formulario.addEventListener('change', actualizar);
        window.addEventListener('beforeunload', function (e) {
            if (modificado()) { e.preventDefault(); e.returnValue = ''; }
        });
        document.addEventListener('click', function (e) {
            var enlace = e.target.closest('a[href]');
            if (!enlace || !modificado() || e.defaultPrevented) return;
            if (enlace.hasAttribute('data-documento-guardado')) {
                e.preventDefault();
                confirmar('Guarda los cambios primero', 'La impresión y la descarga usan la versión guardada. Guarda los cambios para incluir las cantidades que acabas de cambiar.', [], 'Entendido');
                return;
            }
            var url = new URL(enlace.href, window.location.href);
            if (enlace.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey || enlace.hasAttribute('download') || !/^https?:$/.test(url.protocol)) return;
            if (enlace.getAttribute('href').charAt(0) === '#' || enlace.hasAttribute('data-toggle')) return;
            if (url.pathname === location.pathname && url.search === location.search && url.hash) return;
            if (!window.confirm('Hay cambios sin guardar. ¿Quieres salir y descartarlos?')) e.preventDefault();
            else enviando = true;
        });
        // Return navigation protection after a page restored from browser history.
        window.addEventListener('pageshow', function () {
            enviando = false; delete formulario.dataset.envioListo;
            document.querySelectorAll('button[type="submit"]').forEach(function (b) { if (b.form === formulario) b.disabled = false; });
            actualizar();
        });
        actualizar();
        return {
            modificado: modificado,
            permitirSalida: function () { enviando = true; },
            enviar: function () {
                enviando = true;
                if (indicador) indicador.textContent = 'Enviando…';
                // The clicked submit button must stay enabled until its name/value is serialized.
                setTimeout(function () {
                    document.querySelectorAll('button[type="submit"]').forEach(function (b) { if (b.form === formulario) b.disabled = true; });
                }, 0);
            }
        };
    }

    function reenviar(formulario, boton) {
        if (formulario.requestSubmit) formulario.requestSubmit(boton);
        else {
            if (boton && boton.name) {
                var accion = document.createElement('input'); accion.type = 'hidden'; accion.name = boton.name; accion.value = boton.value; formulario.appendChild(accion);
            }
            formulario.dispatchEvent(new Event('submit', {cancelable: true, bubbles: true}));
            // Old browsers: the submit listener validates and uses the bypass flag.
            if (formulario.dataset.envioListo === '1') formulario.submit();
        }
    }

    var paginadores = new WeakMap();
    function paginarTabla(tabla) {
        var filas = Array.from(tabla.tBodies[0].rows);
        if (filas.length <= 10) return null;
        var porPagina = 10, pagina = 0, palabras = [];
        filas.forEach(function (f) { f.dataset.buscar = normalizarTexto(f.textContent.replace(/\s+/g, ' ')); });
        var barra = document.createElement('div'); barra.className = 'tabla-paginada-barra';
        barra.innerHTML = '<label class="sr-only" for="buscar-detalle">Buscar producto</label><span class="tabla-paginada-buscar"><i class="mdi mdi-magnify" aria-hidden="true"></i><input type="search" id="buscar-detalle" class="form-control form-control-sm" placeholder="Buscar producto, marca o categoría" autocomplete="off"></span><span class="tabla-paginada-info" aria-live="polite"></span>';
        var pie = document.createElement('div'); pie.className = 'tabla-paginada-pie';
        pie.innerHTML = '<label>Filas <select class="form-control form-control-sm"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="0">Todas</option></select></label><nav aria-label="Páginas de productos"><ul class="pagination pagination-sm mb-0"></ul></nav>';
        var contenedor = tabla.closest('.table-responsive') || tabla;
        contenedor.parentNode.insertBefore(barra, contenedor); contenedor.parentNode.insertBefore(pie, contenedor.nextSibling);
        var buscar = barra.querySelector('input'), info = barra.querySelector('.tabla-paginada-info'), tamano = pie.querySelector('select'), paginacion = pie.querySelector('ul');
        function coincidentes() { return filas.filter(function (f) { return palabras.every(function (p) { return f.dataset.buscar.indexOf(p) !== -1; }); }); }
        function boton(texto, destino, activo, deshabilitado, etiqueta) {
            var li = document.createElement('li'), b = document.createElement('button');
            li.className = 'page-item' + (activo ? ' active' : '') + (deshabilitado ? ' disabled' : '');
            b.type = 'button'; b.className = 'page-link'; b.textContent = texto; b.disabled = deshabilitado;
            if (etiqueta) b.setAttribute('aria-label', etiqueta);
            if (activo) b.setAttribute('aria-current', 'page');
            b.addEventListener('click', function () { pagina = destino; dibujar(); });
            li.appendChild(b); paginacion.appendChild(li);
        }
        function dibujar() {
            var lista = coincidentes(), paginas = porPagina ? Math.max(1, Math.ceil(lista.length / porPagina)) : 1;
            pagina = Math.max(0, Math.min(pagina, paginas - 1));
            var inicio = porPagina ? pagina * porPagina : 0, fin = porPagina ? inicio + porPagina : lista.length, visibles = lista.slice(inicio, fin);
            filas.forEach(function (f) { f.hidden = true; }); visibles.forEach(function (f) { f.hidden = false; });
            info.textContent = !lista.length ? 'Sin coincidencias' : (inicio + 1) + '–' + (inicio + visibles.length) + ' de ' + lista.length + (palabras.length ? ' (de ' + filas.length + ')' : '');
            paginacion.textContent = '';
            if (paginas > 1) {
                boton('‹', pagina - 1, false, pagina === 0, 'Página anterior');
                for (var i = 0; i < paginas; i++) {
                    if (paginas > 7 && i > 0 && i < paginas - 1 && Math.abs(i - pagina) > 1) { if (Math.abs(i - pagina) === 2) { var li = document.createElement('li'); li.className = 'page-item disabled'; li.innerHTML = '<span class="page-link">…</span>'; paginacion.appendChild(li); } continue; }
                    boton(String(i + 1), i, i === pagina, false, 'Página ' + (i + 1));
                }
                boton('›', pagina + 1, false, pagina === paginas - 1, 'Página siguiente');
            }
            pie.hidden = lista.length <= 10 && porPagina === 10;
        }
        buscar.addEventListener('input', function (e) { e.stopPropagation(); palabras = normalizarTexto(buscar.value).split(/\s+/).filter(Boolean); pagina = 0; dibujar(); });
        // Enter en el buscador no debe enviar el formulario de cantidades.
        buscar.addEventListener('keydown', function (e) { if (e.key === 'Enter') e.preventDefault(); });
        tamano.addEventListener('change', function (e) { e.stopPropagation(); porPagina = Number(tamano.value); pagina = 0; dibujar(); });
        dibujar();
        var api = {
            mostrar: function (fila) {
                if (palabras.length && coincidentes().indexOf(fila) === -1) { buscar.value = ''; palabras = []; }
                var lista = coincidentes(); pagina = porPagina ? Math.floor(lista.indexOf(fila) / porPagina) : 0; dibujar();
            }
        };
        paginadores.set(tabla, api);
        return api;
    }

    function iniciarRevision(formulario) {
        var guardia = proteger(formulario, document.getElementById('estado-revision'));
        var campos = Array.from(formulario.querySelectorAll('.revision-cantidad'));
        function actualizar() {
            var centimos = 0;
            campos.forEach(function (campo) {
                var valido = campo.value === '' || formatoCantidad.test(campo.value);
                campo.setCustomValidity(valido ? '' : 'Ingresa una cantidad positiva o cero, con hasta 3 decimales.');
                campo.classList.toggle('is-invalid', !valido);
                var fila = campo.closest('tr'), milesimas = Math.round(cantidad(campo.value) * 1000), precio = Math.round(Number(fila.dataset.precio) * 100);
                var importe = typeof BigInt === 'function' ? Number((BigInt(milesimas) * BigInt(precio) + BigInt(500)) / BigInt(1000)) : Math.round(milesimas * precio / 1000);
                centimos += importe; fila.querySelector('[data-revision-importe]').textContent = dinero(importe / 100);
            });
            var total = centimos / 100, presupuesto = Number(formulario.dataset.presupuesto);
            document.getElementById('revision-total').textContent = dinero(total);
            var saldo = Number(formulario.dataset.saldoAnterior) + presupuesto - total, campoSaldo = document.getElementById('revision-saldo');
            campoSaldo.textContent = dinero(saldo); campoSaldo.classList.toggle('text-danger', saldo < 0);
            var exceso = document.getElementById('revision-exceso'); exceso.hidden = total <= presupuesto;
            exceso.querySelector('span').textContent = dinero(Math.max(0, total - presupuesto));
        }
        formulario.addEventListener('input', actualizar); actualizar();
        formulario.addEventListener('submit', function (e) {
            actualizar();
            var invalido = campos.find(function (campo) { return !campo.checkValidity(); }), paginador = paginadores.get(formulario.querySelector('table'));
            if (invalido && paginador && invalido.closest('tr').hidden) paginador.mostrar(invalido.closest('tr'));
            if (!formulario.reportValidity()) { e.preventDefault(); return; }
            if (!campos.some(function (campo) { return cantidad(campo.value) > 0; })) {
                e.preventDefault(); confirmar('Revisa las cantidades', 'Conserva al menos un producto con cantidad mayor a cero.', [], 'Entendido'); return;
            }
            formulario.dataset.envioListo = '1'; guardia.enviar();
        });
        function guardarPrimero(e) {
            if (!guardia.modificado()) return false;
            e.preventDefault(); e.stopImmediatePropagation();
            confirmar('Guarda las cantidades primero', 'Pulsa Guardar antes de aprobar, rechazar o despachar la asignación.', [], 'Entendido');
            return true;
        }
        // Capture before the shared modal handles its dispatch link.
        document.addEventListener('click', function (e) {
            if (e.target.closest('[data-almacen-formulario="despacho"]')) guardarPrimero(e);
        }, true);
        var decision = document.getElementById('form-decision-revision');
        if (decision) decision.addEventListener('submit', function (e) { guardarPrimero(e); });
        document.addEventListener('almacen:despachada', function (e) {
            if (String(e.detail.id) !== formulario.elements.id.value) return;
            formulario.dataset.guardarFormulario = '0';
            campos.forEach(function (campo) { campo.readOnly = true; });
            document.querySelectorAll('button[form="form-revision-cantidades"]').forEach(function (boton) { boton.disabled = true; });
            document.getElementById('estado-revision').textContent = 'Despachada · cantidades bloqueadas';
        });
    }

    // Aprobar o rechazar desde la cabecera: el motivo solo es obligatorio al rechazar; aprobar con exceso pide confirmación.
    function iniciarDecision(formulario) {
        var comentario = document.getElementById('comentario'), confirmado = false;
        formulario.addEventListener('submit', function (e) {
            var decision = e.submitter ? e.submitter.value : '';
            if (comentario) comentario.required = decision === 'rechazado';
            if (decision === 'rechazado' && comentario && !comentario.value.trim()) { e.preventDefault(); comentario.value = ''; comentario.reportValidity(); return; }
            var exceso = Number(formulario.dataset.exceso);
            if (decision !== 'aprobado' || !exceso || confirmado) return;
            e.preventDefault();
            confirmar('Aprobar con exceso de presupuesto', 'El total solicitado supera el presupuesto máximo del cliente.', [['Exceso', 'S/ ' + dinero(exceso)]], 'Aprobar asignación').then(function (ok) {
                if (!ok) return;
                confirmado = true; formulario.requestSubmit(e.submitter);
            });
        });
        if (window.jQuery && comentario) window.jQuery('#modal-rechazo').on('shown.bs.modal', function () { comentario.focus(); });
    }

    function iniciarAsignacion(formulario) {
        var filas = Array.from(formulario.querySelectorAll('.asignacion-producto'));
        var buscar = document.getElementById('buscar-producto'), solo = document.getElementById('solo-solicitados');
        var tabla, coincidencias = null, aceptado = false, confirmando = false, subtotalesCategoria = {};
        var presupuesto = Number(document.getElementById('presupuesto_mes').value), saldoAnterior = Number(formulario.dataset.saldoAnterior);
        var total = 0, solicitados = 0;
        var guardia = proteger(formulario, document.getElementById('estado-guardado'), filas.map(function(f) { return f.querySelector('.cantidad'); }));
        formulario.noValidate = true;
        var barra = formulario.querySelector('.asignacion-barra'), contenido = formulario.closest('.content');
        function reservarBarra() {
            if (formulario.dataset.guardarFormulario === '1') contenido.style.setProperty('padding-bottom', (barra.getBoundingClientRect().height + 24) + 'px', 'important');
        }
        if (window.ResizeObserver) new ResizeObserver(reservarBarra).observe(barra);
        window.addEventListener('resize', reservarBarra); reservarBarra();
        filas.forEach(function (f) { f.textoBusqueda = normalizarTexto(f.dataset.busqueda); });
        var categorias = {};
        formulario.querySelectorAll('.asignacion-categoria').forEach(function(f) { categorias[f.dataset.categoria] = f; f.remove(); });
        var elementoTabla = formulario.querySelector('.asignacion-tabla');
        window.jQuery.fn.dataTable.ext.search.push(function(settings, data, index) {
            if(settings.nTable !== elementoTabla) return true;
            var fila = settings.aoData[index].nTr;
            return fila.textoBusqueda.indexOf(normalizarTexto(buscar.value)) !== -1 && (!solo.checked || cantidad(fila.querySelector('.cantidad').value) > 0);
        });
        tabla = window.jQuery(elementoTabla).DataTable({
            dom: "t<'row dt-pie'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'i><'col-sm-12 col-md-4'p>>",
            buttons: [], ordering: false, autoWidth: false, pageLength: 10, lengthMenu: [10,20,50],
            drawCallback: function() {
                var anterior = null;
                this.api().rows({page:'current'}).nodes().toArray().forEach(function(f) {
                    if(anterior !== f.dataset.categoria) {
                        var encabezado = categorias[f.dataset.categoria].cloneNode(true);
                        encabezado.querySelector('.subtotal-categoria').textContent = dinero((subtotalesCategoria[f.dataset.categoria] || 0)/100);
                        f.parentNode.insertBefore(encabezado,f); anterior = f.dataset.categoria;
                    }
                });
            }
        });
        function filtrar(conservarPosicion) {
            var foco = conservarPosicion ? document.activeElement : null;
            var filaActual = foco ? foco.closest('.asignacion-producto') : null;
            var posicion = conservarPosicion ? {x: window.scrollX, y: window.scrollY, fila: filaActual ? filaActual.getBoundingClientRect().top : null} : null;
            tabla.draw(false);
            coincidencias = tabla.rows({search:'applied'}).nodes().toArray();
            document.getElementById('productos-vacio').hidden = coincidencias.length !== 0;
            document.getElementById('productos-vacio-texto').textContent = solo.checked ? 'No hay productos solicitados que coincidan con este filtro.' : 'No se encontraron productos. Prueba con otro nombre, unidad o marca.';
            if (posicion) {
                if (foco && foco.isConnected && !foco.disabled) foco.focus({preventScroll: true});
                var desplazamiento = filaActual && filaActual.isConnected ? filaActual.getBoundingClientRect().top - posicion.fila : 0;
                window.scrollTo(posicion.x, posicion.y + desplazamiento);
            }
        }
        function irATabla() {
            if (window.matchMedia('(max-width: 767px)').matches) {
                var fila = tabla.rows({page:'current'}).nodes()[0];
                if (fila) fila.scrollIntoView({block: 'start'});
            }
        }
        function mostrarError(entrada) {
            var fila = entrada.closest('.asignacion-producto');
            buscar.value = ''; solo.checked = false;
            tabla.page(Math.floor(filas.indexOf(fila) / tabla.page.len()));
            filtrar(); entrada.focus(); entrada.scrollIntoView({block: 'center'});
        }
        function validar(entrada) {
            var valido = entrada.value === '' || formatoCantidad.test(entrada.value);
            entrada.setCustomValidity(valido ? '' : 'Ingresa una cantidad positiva o cero, con hasta 3 decimales. Ejemplo: 1,5.');
            entrada.classList.toggle('is-invalid', !valido); entrada.setAttribute('aria-invalid', String(!valido));
            entrada.closest('.asignacion-producto').classList.toggle('tiene-error', !valido);
            return valido;
        }
        function actualizar() {
            var subtotales = {}, cambiaFiltro = false; total = 0; solicitados = 0;
            filas.forEach(function (f) {
                var entrada = f.querySelector('.cantidad'), c = cantidad(entrada.value), centimos = Math.round(c * Number(f.dataset.precio) * 100);
                var cantidadValida = entrada.value === '' || formatoCantidad.test(entrada.value), milesimas = Math.round(c * 1000);
                f.querySelectorAll('[data-cantidad-paso]').forEach(function(boton) {
                    boton.disabled = entrada.disabled || !cantidadValida || (boton.dataset.cantidadPaso === '-1' ? milesimas === 0 : milesimas > 999999998999);
                });
                total += centimos; subtotales[f.dataset.categoria] = (subtotales[f.dataset.categoria] || 0) + centimos;
                f.querySelector('.total-linea').textContent = entrada.value !== '' && !formatoCantidad.test(entrada.value) ? '—' : dinero(centimos / 100);
                if (solo.checked && f.classList.contains('solicitado') !== (c > 0)) cambiaFiltro = true;
                f.classList.toggle('solicitado', c > 0); if (c > 0) solicitados++;
            });
            subtotalesCategoria = subtotales;
            elementoTabla.querySelectorAll('.subtotal-categoria').forEach(function(subtotal) {
                subtotal.textContent = dinero((subtotalesCategoria[subtotal.dataset.categoria] || 0) / 100);
            });
            var diferencia = presupuesto - total / 100, saldo = saldoAnterior + diferencia;
            document.getElementById('total-general').textContent = dinero(total / 100);
            document.getElementById('total-barra').textContent = dinero(total / 100);
            document.getElementById('diferencia-etiqueta').textContent = diferencia < 0 ? 'Exceso del mes' : 'Disponible del mes';
            document.getElementById('diferencia-mes').textContent = dinero(Math.abs(diferencia));
            document.getElementById('saldo-acumulado').textContent = dinero(saldo);
            document.getElementById('solicitados-cantidad').textContent = solicitados;
            document.getElementById('alerta-presupuesto').hidden = diferencia >= -0.00001;
            document.getElementById('alerta-saldo').hidden = saldo >= -0.00001;
            document.getElementById('diferencia-mes').className = diferencia < 0 ? 'text-danger font-weight-bold' : '';
            document.getElementById('saldo-acumulado').className = saldo < 0 ? 'text-danger font-weight-bold' : '';
            // Keep the field and scroll position while editing unchanged results.
            if (coincidencias === null || cambiaFiltro) filtrar(coincidencias !== null);
        }
        buscar.addEventListener('input', function () { tabla.page('first'); filtrar(); });
        solo.addEventListener('change', function () { tabla.page('first'); filtrar(); });
        function limpiar() { buscar.value = ''; solo.checked = false; tabla.page('first'); filtrar(); buscar.focus(); }
        document.getElementById('productos-vacio-limpiar').onclick = limpiar;
        window.jQuery(elementoTabla).on('page.dt', function() { requestAnimationFrame(irATabla); });
        filas.forEach(function (f) {
            var entrada = f.querySelector('.cantidad');
            entrada.addEventListener('input', function () { validar(entrada); actualizar(); });
            f.querySelectorAll('[data-cantidad-paso]').forEach(function(boton) {
                boton.addEventListener('click', function() {
                    if (entrada.disabled || !validar(entrada)) return;
                    // Work in thousandths so repeated steps preserve decimal precision.
                    var siguiente = Math.max(0, Math.round(cantidad(entrada.value) * 1000) + Number(boton.dataset.cantidadPaso) * 1000);
                    if (siguiente > 999999999999) return;
                    var decimales = String(siguiente % 1000).padStart(3, '0').replace(/0+$/, '');
                    entrada.value = String(Math.floor(siguiente / 1000)) + (decimales ? (entrada.value.indexOf(',') !== -1 ? ',' : '.') + decimales : '');
                    entrada.dispatchEvent(new Event('input', {bubbles: true}));
                    if (boton.isConnected && !boton.disabled) boton.focus({preventScroll: true});
                    else if (entrada.isConnected) f.querySelector('[data-cantidad-paso="1"]').focus({preventScroll: true});
                    else solo.focus({preventScroll: true});
                });
            });
            entrada.addEventListener('focus', function () {
                requestAnimationFrame(function () {
                    var campo = entrada.getBoundingClientRect(), pie = barra.getBoundingClientRect();
                    if (campo.top < 80 || campo.bottom > pie.top - 12) entrada.scrollIntoView({block: 'center'});
                });
            });
            entrada.addEventListener('invalid', function (e) { e.preventDefault(); mostrarError(entrada); });
        });
        document.querySelectorAll('[data-error-producto]').forEach(function (enlace) {
            enlace.onclick = function (e) { e.preventDefault(); var fila=filas.find(function(f) { return f.dataset.productoId === enlace.dataset.errorProducto; }); if(fila) mostrarError(fila.querySelector('.cantidad')); };
        });
        formulario.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && e.target.tagName === 'INPUT') { e.preventDefault(); if (e.target.classList.contains('cantidad')) e.target.blur(); }
        });
        formulario.addEventListener('submit', function (e) {
            var boton = e.submitter || document.activeElement;
            if (!boton || boton.name !== 'accion') { e.preventDefault(); return; }
            var primera = null;
            filas.forEach(function (f) { var entrada = f.querySelector('.cantidad'); if (!entrada.disabled && !validar(entrada) && !primera) primera = entrada; });
            if (primera) { e.preventDefault(); mostrarError(primera); primera.reportValidity(); return; }
            if (!formulario.checkValidity()) { e.preventDefault(); formulario.reportValidity(); return; }
            actualizar();
            if (boton.value === 'publicar' && !aceptado) {
                e.preventDefault(); if (confirmando) return;
                if (!solicitados) { confirmar('Agrega productos a la solicitud', 'Ingresa una cantidad mayor a cero en al menos un producto antes de enviar a administración.', [], 'Entendido'); return; }
                confirmando = true;
                var mensaje = 'Revisa el resumen. Al enviar, las cantidades quedarán bloqueadas mientras administración revisa la solicitud.';
                if (total / 100 > presupuesto) mensaje += ' El total supera el presupuesto mensual; administración revisará el exceso.';
                confirmar('Enviar solicitud a administración', mensaje, [['Cliente', formulario.dataset.cliente], ['Mes', formulario.dataset.periodo], ['Productos solicitados', String(solicitados)], ['Total', 'S/ ' + dinero(total / 100)], ['Presupuesto mensual', 'S/ ' + dinero(presupuesto)]], 'Confirmar envío').then(function (ok) {
                    confirmando = false;
                    if (ok) { aceptado = true; reenviar(formulario, boton); }
                });
                return;
            }
            formulario.querySelectorAll('[data-cantidad-paginada]').forEach(function(campo) { campo.remove(); });
            filas.forEach(function(f) {
                var entrada=f.querySelector('.cantidad');
                if(!entrada.disabled && !formulario.contains(entrada)) {
                    var campo=document.createElement('input'); campo.type='hidden'; campo.name=entrada.name; campo.value=entrada.value;
                    campo.setAttribute('data-cantidad-paginada',''); formulario.appendChild(campo);
                }
            });
            aceptado = false; formulario.dataset.envioListo = '1'; guardia.enviar();
        });
        var contexto = document.getElementById('contexto-asignacion'), cliente = document.getElementById('elegir_cliente'), clienteInicial = cliente.value;
        contexto.addEventListener('submit', function (e) {
            if (guardia.modificado() && !window.confirm('Hay cambios sin guardar. ¿Quieres cambiar de cliente y descartarlos?')) { e.preventDefault(); cliente.value = clienteInicial; sincronizarSelect(cliente); }
            else guardia.permitirSalida();
        });
        cliente.addEventListener('change', function () { if (contexto.requestSubmit) contexto.requestSubmit(); else if (!guardia.modificado() || window.confirm('Hay cambios sin guardar. ¿Quieres cambiar de cliente y descartarlos?')) { guardia.permitirSalida(); contexto.submit(); } else { cliente.value = clienteInicial; sincronizarSelect(cliente); } });
        actualizar();
        var filaErrorInicial = filas.find(function(f) { return f.querySelector('.cantidad.is-invalid'); });
        var errorInicial = filaErrorInicial && filaErrorInicial.querySelector('.cantidad');
        if (errorInicial) mostrarError(errorInicial);
    }

    function configurarMovimiento(formulario) {
        var tipo = formulario.querySelector('#tipo'), entrada = formulario.querySelector('#cantidad'), stock = Number(formulario.dataset.stock);
        if(!tipo || !entrada) return null;
        var confirmado = formulario.dataset.stockConfirmado === '1', unidad = formulario.dataset.unidad, sinStock = formulario.dataset.sinStock === '1';
        var motivo = formulario.querySelector('#motivo'), camposEntrada = formulario.querySelector('[data-campos-entrada]');
        function actualizar() {
            entrada.setCustomValidity('');
            // Proveedor y factura solo en entradas; el conteo de un producto sin stock no exige motivo.
            var esEntrada = tipo.value === 'entrada', motivoOpcional = tipo.value === 'ajuste' && sinStock;
            if (camposEntrada) { camposEntrada.hidden = !esEntrada; camposEntrada.querySelectorAll('input').forEach(function (c) { c.disabled = !esEntrada; }); }
            motivo.required = !motivoOpcional;
            formulario.querySelector('#motivo-etiqueta').innerHTML = 'Motivo / referencia' + (motivoOpcional ? ' <span class="text-muted">(opcional)</span>' : '');
            motivo.placeholder = motivoOpcional ? 'Conteo físico' : 'Entrega o guía de compra';
            entrada.min = tipo.value === 'ajuste' ? '0' : '0.001';
            var c = Number(entrada.value), valido = entrada.value !== '' && entrada.validity.valid && tipo.value !== '';
            formulario.querySelector('#cantidad-etiqueta').textContent = (tipo.value === 'ajuste' ? 'Stock contado' : tipo.value === 'salida' ? 'Cantidad a retirar' : tipo.value === 'entrada' ? 'Cantidad recibida' : 'Cantidad') + ' (' + unidad + ')';
            var nuevo = tipo.value === 'ajuste' ? c : stock + (tipo.value === 'entrada' ? c : -c);
            formulario.querySelector('#movimiento-resumen').hidden = !valido;
            formulario.querySelector('#stock-final-previsto').textContent = unidades(nuevo) + ' ' + unidad;
            formulario.querySelector('#movimiento-exceso').hidden = !valido || nuevo >= 0;
            return nuevo;
        }
        tipo.addEventListener('change', actualizar); entrada.addEventListener('input', actualizar); actualizar();
        return {
            validar:function() {
                var nuevo=actualizar();
                if(nuevo<0) { entrada.setCustomValidity('La cantidad a retirar supera el stock disponible.'); entrada.reportValidity(); return false; }
                return formulario.reportValidity();
            },
            titulo:function() { return {entrada:'Registrar entrada',salida:'Registrar salida',ajuste:'Confirmar conteo físico'}[tipo.value]; },
            detalles:function() {
                var d=[['Producto',formulario.dataset.producto],['Stock actual',confirmado?unidades(stock)+' '+unidad:'Por verificar'],[tipo.value==='ajuste'?'Stock contado':'Cantidad registrada',entrada.value+' '+unidad],['Stock final',unidades(actualizar())+' '+unidad],['Motivo',motivo.value.trim()||'Conteo físico']];
                if(tipo.value==='entrada') {
                    var proveedor=formulario.querySelector('#proveedor').value.trim(), factura=formulario.querySelector('#factura').value.trim();
                    if(proveedor) d.push(['Proveedor',proveedor]);
                    if(factura) d.push(['Factura',factura]);
                }
                return d;
            },
            esConteo:function() { return tipo.value==='ajuste'; }
        };
    }

    function iniciarInventario(formulario) {
        var movimiento=configurarMovimiento(formulario); if(!movimiento) return;
        var guardia=proteger(formulario), aceptado=false, confirmando=false;
        formulario.addEventListener('submit',function(e) {
            if(!movimiento.validar()) { e.preventDefault(); return; }
            if (!aceptado) {
                e.preventDefault(); if (confirmando) return; confirmando = true;
                confirmar(movimiento.titulo(),movimiento.esConteo()?'El conteo reemplazará el stock actual por la cantidad física que indicaste.':'Revisa el movimiento antes de registrar el nuevo stock.',movimiento.detalles(),'Registrar movimiento').then(function (ok) { confirmando = false; if (ok) { aceptado = true; reenviar(formulario, formulario.querySelector('button[type="submit"]')); } });
                return;
            }
            aceptado = false; formulario.dataset.envioListo = '1'; guardia.enviar();
        });
    }

    function iniciarCompra(formulario) {
        var guardia = proteger(formulario, document.getElementById('estado-compra')), aceptado = false, confirmando = false, total = 0, productos = 0;
        var vistaClientes = document.getElementById('ver-clientes-compra');
        function mostrarVista() {
            document.getElementById('compra-consolidado').hidden = vistaClientes.checked;
            document.getElementById('compra-clientes').hidden = !vistaClientes.checked;
            document.getElementById('compra-vista-titulo').textContent = vistaClientes.checked ? 'Solicitado por cliente' : 'Consolidado';
            document.querySelectorAll('.almacen-cabecera [data-documento-guardado]').forEach(function(enlace) {
                var url = new URL(enlace.href);
                if(vistaClientes.checked) url.searchParams.set('vista','clientes');
                else url.searchParams.delete('vista');
                enlace.href = url.href;
            });
        }
        vistaClientes.addEventListener('change', mostrarVista); mostrarVista();
        // Reveal editable quantities before the browser focuses an invalid field.
        formulario.addEventListener('invalid', function(e) {
            if(e.target.closest('#compra-consolidado') && vistaClientes.checked) {
                vistaClientes.checked = false; mostrarVista();
            }
        }, true);
        function actualizar() {
            total = 0; productos = 0;
            formulario.querySelectorAll('.compra-linea').forEach(function (f) {
                var c = Number(f.querySelector('.cantidad-compra').value) || 0, importe = Math.round(c * Number(f.dataset.precio) * 100);
                total += importe; if (c > 0) productos++;
                f.querySelector('.importe-compra').textContent = dinero(importe / 100);
            });
            document.getElementById('total-compra').textContent = dinero(total / 100);
        }
        formulario.querySelectorAll('.cantidad-compra').forEach(function (e) { e.addEventListener('input', actualizar); }); actualizar();
        formulario.addEventListener('submit', function (e) {
            var boton = e.submitter || document.activeElement;
            if (!boton || boton.name !== 'accion') { e.preventDefault(); return; }
            actualizar();
            if ((boton.value === 'aprobar' || boton.value === 'reabrir') && !aceptado) {
                e.preventDefault(); if (confirmando) return; confirmando = true;
                var aprobar = boton.value === 'aprobar';
                confirmar(aprobar ? 'Aprobar orden de compra' : 'Reabrir orden de compra',
                    aprobar ? 'Se guardan las cantidades y la orden queda aprobada. Almacén solo podrá confirmar la recepción de lo aprobado.' : 'La orden vuelve a borrador para corregir cantidades o actualizar el consolidado. Deberá aprobarse de nuevo.',
                    aprobar ? [['Orden', formulario.dataset.orden], ['Productos a comprar', String(productos)], ['Importe con IGV', 'S/ ' + dinero(total / 100)]] : [['Orden', formulario.dataset.orden]],
                    aprobar ? 'Aprobar orden' : 'Reabrir').then(function (ok) { confirmando = false; if (ok) { aceptado = true; reenviar(formulario, boton); } });
                return;
            }
            if (boton.value === 'recibir' && !aceptado) {
                e.preventDefault(); if (confirmando) return; confirmando = true;
                var detalles = [['Orden', formulario.dataset.orden], ['Proveedor', document.getElementById('proveedor').value || 'Sin proveedor registrado'], ['Productos a ingresar', String(productos)], ['Importe de compra', 'S/ ' + dinero(total / 100)]];
                var sinConteo = 0;
                formulario.querySelectorAll('.compra-linea').forEach(function (f) {
                    var c = Number(f.querySelector('.cantidad-compra').value), marca = f.querySelector('.compra-sin-stock input');
                    if (c > 0) detalles.push([f.dataset.producto, unidades(c) + ' ' + f.dataset.unidad + (marca && marca.checked ? ' · se da por contado (no había stock)' : '')]);
                    if (marca && !marca.checked) sinConteo++;
                });
                if (sinConteo) detalles.push(['Atención', sinConteo + (sinConteo > 1 ? ' productos quedarán' : ' producto quedará') + ' sin conteo y no se podrá despachar hasta registrarlo en Productos']);
                confirmar('Confirmar recepción de la compra', 'Confirma que recibiste todas las cantidades indicadas. Se registrarán entradas al inventario y la orden quedará cerrada. Esta recepción no se puede repetir.', detalles, 'Confirmar recepción').then(function (ok) { confirmando = false; if (ok) { aceptado = true; reenviar(formulario, boton); } });
                return;
            }
            aceptado = false; formulario.dataset.envioListo = '1'; guardia.enviar();
        });
        document.querySelectorAll('form[data-actualizar-consolidado]').forEach(function (otro) {
            otro.addEventListener('submit', function (e) {
                if (guardia.modificado()) { e.preventDefault(); confirmar('Guarda las cantidades primero', 'Guarda los cambios de la orden antes de actualizar el consolidado para conservar tus ajustes.', [], 'Entendido'); }
            });
        });
        return guardia;
    }

    function iniciarAdministracion(elemento,guardia) {
        var mes=elemento.querySelector('[data-almacen-mes]');
        window.jQuery('#almacen-secciones a[data-toggle="tab"]').on('shown.bs.tab',function(e) {
            mes.elements.vista.value=e.target.dataset.vista;
            var url=new URL(location.href); url.searchParams.set('vista',e.target.dataset.vista);
            history.replaceState(null,'',url.href);
            window.jQuery('table[data-almacen-tabla]').DataTable().columns.adjust();
        });
        elemento.querySelectorAll('form[data-almacen-filtros]').forEach(function(formulario) {
            var campos=Array.from(formulario.elements).filter(function(c) { return c.name && c.type!=='hidden' && c.type!=='submit'; });
            var iniciales=campos.map(function(c) { return c.value; });
            formulario.addEventListener('change',function() { if(formulario.reportValidity()) formulario.requestSubmit(); });
            formulario.addEventListener('submit',function(e) {
                if(!guardia || !guardia.modificado()) return;
                if(!window.confirm('Hay cambios sin guardar. ¿Quieres cambiar el filtro y descartarlos?')) {
                    e.preventDefault(); campos.forEach(function(c,i) { c.value=iniciales[i]; sincronizarSelect(c); });
                } else guardia.permitirSalida();
            });
        });
    }

    // Horarios del cliente: filas con días (casillas) y hora de entrada/salida; se agregan y quitan sin recargar.
    var DIAS_HORARIO=['lun','mar','mie','jue','vie','sab','dom'], DIAS_CORTOS={lun:'Lun',mar:'Mar',mie:'Mié',jue:'Jue',vie:'Vie',sab:'Sáb',dom:'Dom'};
    function resumenHorarios(filas) {
        return filas.map(function (f) {
            var idx=f.dias.map(function (d) { return DIAS_HORARIO.indexOf(d); }).sort(function (a, b) { return a - b; }), tramos=[], inicio=null;
            idx.forEach(function (d, i) {
                if (inicio===null) inicio=d;
                if (idx[i+1]!==d+1) {
                    var a=DIAS_CORTOS[DIAS_HORARIO[inicio]], b=DIAS_CORTOS[DIAS_HORARIO[d]];
                    tramos.push(d-inicio>=2 ? a+'–'+b : (d>inicio ? a+', '+b : a)); inicio=null;
                }
            });
            return tramos.join(', ')+' '+f.entrada+'–'+f.salida+(f.detalle ? ' ('+f.detalle+')' : '');
        }).join(' · ');
    }
    function iniciarHorarios(formulario) {
        var bloque=formulario.querySelector('[data-horarios]');
        if(!bloque || bloque.dataset.listo) return;
        bloque.dataset.listo='1';
        var lista=bloque.querySelector('[data-horario-lista]'), plantilla=bloque.querySelector('[data-horario-plantilla]'), siguiente=Number(bloque.dataset.siguiente)||0;
        function validar(fila) {
            var dias=fila.querySelectorAll('.horario-dia input'), marcados=fila.querySelectorAll('.horario-dia input:checked').length;
            dias[0].setCustomValidity(marcados ? '' : 'Marca al menos un día para este horario.');
            var entrada=fila.querySelector('[name$="[entrada]"]'), salida=fila.querySelector('[name$="[salida]"]');
            salida.setCustomValidity(entrada.value && salida.value && entrada.value===salida.value ? 'La salida debe ser distinta de la entrada.' : '');
        }
        function actualizar() {
            var filas=Array.from(lista.querySelectorAll('[data-horario]'));
            bloque.querySelector('[data-horario-vacio]').hidden=filas.length>0;
            bloque.querySelector('[data-horario-resumen]').textContent=resumenHorarios(filas.map(function (f) {
                return {dias:Array.from(f.querySelectorAll('.horario-dia input:checked')).map(function (c) { return c.value; }), entrada:f.querySelector('[name$="[entrada]"]').value, salida:f.querySelector('[name$="[salida]"]').value, detalle:f.querySelector('.horario-detalle').value.trim()};
            }).filter(function (f) { return f.dias.length && f.entrada && f.salida; }));
        }
        function avisarCambio() { formulario.dispatchEvent(new Event('change', {bubbles:true})); }
        bloque.addEventListener('click', function (e) {
            if (e.target.closest('[data-horario-agregar]')) {
                var t=document.createElement('template'); t.innerHTML=plantilla.innerHTML.replace(/__i__/g, String(siguiente++)).trim();
                var fila=t.content.firstElementChild; lista.appendChild(fila); validar(fila); actualizar(); avisarCambio();
                fila.querySelector('.horario-dia input').focus(); return;
            }
            var quitar=e.target.closest('[data-horario-quitar]');
            if (quitar) { quitar.closest('[data-horario]').remove(); actualizar(); avisarCambio(); bloque.querySelector('[data-horario-agregar]').focus(); return; }
            var atajo=e.target.closest('[data-dias]');
            if (atajo) {
                var fila=atajo.closest('[data-horario]'), dias=atajo.dataset.dias.split(',');
                fila.querySelectorAll('.horario-dia input').forEach(function (c) { c.checked=dias.indexOf(c.value)!==-1; });
                validar(fila); actualizar(); avisarCambio();
            }
        });
        bloque.addEventListener('input', function (e) { var fila=e.target.closest('[data-horario]'); if (fila) validar(fila); actualizar(); });
        bloque.addEventListener('change', function (e) { var fila=e.target.closest('[data-horario]'); if (fila) validar(fila); actualizar(); });
        lista.querySelectorAll('[data-horario]').forEach(validar);
    }

    function configurarEdicion(formulario) {
        iniciarHorarios(formulario);
        var documento=formulario.querySelector('[data-documento-baja]'), estado=formulario.querySelector('[name="activo"]');
        if(documento && estado) {
            function actualizar() { documento.hidden=estado.value!=='0'; documento.querySelector('input').required=estado.value==='0'; }
            estado.addEventListener('change',actualizar); actualizar();
        }
    }

    function reservarGuia() {
        var ventana=null;
        try {
            // Reserve during the submit gesture, before the asynchronous stock confirmation.
            ventana=window.open('about:blank','_blank');
            if(ventana) {
                ventana.opener=null;
                ventana.document.title='Guía de salida';
                ventana.document.body.textContent='Generando guía de salida…';
                ventana.document.body.style.cssText='font:16px Arial,sans-serif;margin:24px;color:#374151';
                ventana.document.body.setAttribute('role','status');
            }
            return ventana;
        } catch(error) { if(ventana) ventana.close(); return null; }
    }

    function iniciarEditor(modal) {
        var contenido=document.getElementById('almacen-editor-contenido'), titulo=document.getElementById('almacen-editor-titulo');
        var formulario=null, inicial='', guardando=false, permitirCerrar=false, origen=null, solicitud=0, abortar=null, movimiento=null, revisando=false;
        function valores() {
            return formulario ? JSON.stringify(Array.from(formulario.elements).filter(function(c) { return c.name && c.type!=='submit'; }).map(function(c) { return [c.name,c.value]; })) : '';
        }
        function modificado() { return formulario && valores()!==inicial; }
        function estadoCarga(mensaje,reintentar) {
            contenido.innerHTML='<div class="modal-body" role="status"></div><div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button></div>';
            contenido.querySelector('.modal-body').textContent=mensaje;
            if(reintentar) {
                var boton=document.createElement('button'); boton.type='button'; boton.className='btn btn-primary'; boton.textContent='Reintentar'; boton.onclick=function() { cargar(origen); };
                contenido.querySelector('.modal-footer').appendChild(boton);
            }
        }
        function mostrarErrores(errores) {
            var alerta=formulario.querySelector('[data-formulario-errores]'); alerta.textContent='';
            var lista=document.createElement('ul'); lista.className='mb-0';
            errores.forEach(function(error) { var item=document.createElement('li'); item.textContent=error; lista.appendChild(item); });
            alerta.appendChild(lista); alerta.hidden=false; alerta.focus();
        }
        function revisarMovimiento(activo) {
            revisando=activo;
            formulario.querySelector('[data-movimiento-campos]').hidden=activo;
            formulario.querySelector('[data-movimiento-revision]').hidden=!activo;
            var historial=formulario.querySelector('[data-inventario-historial]'); if(historial) historial.hidden=activo;
            formulario.querySelector('[data-dismiss="modal"]').hidden=activo;
            formulario.querySelector('[data-movimiento-editar]').hidden=!activo;
            formulario.querySelector('button[type="submit"]').textContent=activo?'Confirmar movimiento':'Registrar movimiento';
            if(activo) {
                formulario.querySelector('[data-movimiento-titulo]').textContent=movimiento.titulo();
                var lista=formulario.querySelector('[data-movimiento-detalles]'); lista.textContent='';
                movimiento.detalles().forEach(function(d) { var etiqueta=document.createElement('dt'),valor=document.createElement('dd'); etiqueta.textContent=d[0]; valor.textContent=d[1]; lista.append(etiqueta,valor); });
                formulario.querySelector('[data-movimiento-revision]').focus();
            } else formulario.querySelector('#tipo').focus();
        }
        function prepararFormulario(nuevo) {
            contenido.textContent=''; contenido.appendChild(nuevo); formulario=nuevo; revisando=false;
            configurarEdicion(formulario); movimiento=formulario.dataset.almacenEdicion==='inventario'?configurarMovimiento(formulario):null;
            var editar=formulario.querySelector('[data-movimiento-editar]'); if(editar) editar.onclick=function() { revisarMovimiento(false); };
            inicial=valores(); formulario.addEventListener('submit',guardar);
            var primero=formulario.querySelector('input:not([type="hidden"]),select'); if(primero) primero.focus();
            else formulario.querySelector('[data-dismiss="modal"]').focus();
        }
        function actualizarFila(data) {
            var elemento=document.querySelector('table[data-almacen-tabla]');
            if(!elemento) {
                var acciones=document.querySelector('[data-despacho-acciones]'),estado=document.querySelector('[data-despacho-estado]');
                if(acciones) acciones.innerHTML=data.acciones;
                if(estado) estado.innerHTML='<span class="badge badge-info">Despachada</span>';
                return;
            }
            var tabla=window.jQuery(elemento).DataTable();
            var temporal=document.createElement('template'); temporal.innerHTML=data.fila.trim(); var nueva=temporal.content.querySelector('tr');
            var anterior=tabla.rows().nodes().toArray().find(function(f) { return f.dataset.registroId===String(data.id); });
            if(anterior) {
                Array.from(nueva.attributes).forEach(function(a) { if(a.name.indexOf('data-')===0) anterior.setAttribute(a.name,a.value); });
                tabla.row(anterior).data(Array.from(nueva.cells).map(function(c) { return c.innerHTML; }));
            }
            else tabla.row.add(nueva);
            tabla.draw(false);
            var alerta=document.getElementById('almacen-editor-resultado');
            if(!alerta) { alerta=document.createElement('div'); alerta.id='almacen-editor-resultado'; alerta.className='alert alert-success'; alerta.setAttribute('role','status'); elemento.closest('.card-box').before(alerta); }
            alerta.textContent=data.mensaje;
        }
        async function guardar(e) {
            e.preventDefault(); if(guardando) return;
            if(!formulario.reportValidity()) return;
            if(formulario.dataset.almacenEdicion==='inventario') {
                if(!movimiento || !movimiento.validar()) return;
                if(!revisando) { revisarMovimiento(true); return; }
            }
            var parametros=new URLSearchParams(new FormData(formulario)), campos=Array.from(modal.querySelectorAll('input,select,textarea,button'));
            var deshabilitados=campos.map(function(c) { return c.disabled; }), boton=formulario.querySelector('button[type="submit"],button:not([type])'), texto=boton.textContent;
            guardando=true; campos.forEach(function(c) { c.disabled=true; }); boton.textContent='Guardando…';
            formulario.querySelector('[data-formulario-errores]').hidden=true;
            var errores=null, guardado=null, ventanaGuia=formulario.dataset.almacenEdicion==='despacho'?reservarGuia():null;
            try {
                var respuesta=await fetch(formulario.action,{method:'POST',credentials:'same-origin',headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},body:parametros});
                if(!(respuesta.headers.get('Content-Type') || '').includes('application/json')) {
                    errores=[respuesta.status===403 || respuesta.redirected?'La sesión venció. Recarga la página para continuar.':'No se pudo confirmar el guardado. Intenta de nuevo.'];
                } else {
                    var data=await respuesta.json();
                    if(respuesta.ok && data.ok) guardado=data;
                    else errores=Array.isArray(data.errores)?data.errores:['No se pudo guardar. Revisa los campos.'];
                }
            } catch(error) { errores=['No se pudo conectar. Tus datos siguen en el formulario; vuelve a intentar.']; }
            finally { guardando=false; campos.forEach(function(c,i) { c.disabled=deshabilitados[i]; }); boton.textContent=texto; }
            if(guardado) {
                actualizarFila(guardado);
                if(formulario.dataset.almacenEdicion==='despacho') {
                    var incluida=document.querySelector('[data-compra-asignacion-id="'+guardado.id+'"]'),aviso=document.getElementById('almacen-compra-despachos');
                    if(incluida && aviso) {
                        aviso.hidden=false;
                        if(!incluida.querySelector('.badge')) incluida.insertAdjacentHTML('beforeend',' <span class="badge badge-info">Despachada</span>');
                    }
                    var consolidar=document.querySelector('[data-compra-generar] button'), nuevas=guardado.nuevas_compra;
                    if(consolidar && typeof nuevas==='number') {
                        consolidar.disabled=nuevas===0;
                        document.querySelector('[data-compra-nuevas]').textContent=nuevas?nuevas+(nuevas>1?' asignaciones aprobadas nuevas sin orden de compra':' asignación aprobada nueva sin orden de compra'):'No hay asignaciones aprobadas nuevas.';
                    }
                    document.dispatchEvent(new CustomEvent('almacen:despachada', {detail:{id:guardado.id}}));
                    permitirCerrar=true; formulario=null;
                    estadoCarga(guardado.mensaje);
                    var guia=document.createElement('a'); guia.className='btn btn-primary'; guia.href=guardado.guia_url; guia.target='_blank'; guia.rel='noopener';
                    guia.innerHTML='<i class="mdi mdi-file-pdf" aria-hidden="true"></i> Guía PDF';
                    contenido.querySelector('.modal-footer').appendChild(guia);
                    contenido.querySelector('[data-dismiss="modal"]').focus();
                    if(ventanaGuia && !ventanaGuia.closed) {
                        ventanaGuia.location.replace(guardado.guia_url);
                        ventanaGuia.focus();
                    } else if(!ventanaGuia) {
                        var visor=document.createElement('iframe'); visor.className='almacen-guia-visor'; visor.title='Guía de salida'; visor.src=guardado.guia_url;
                        contenido.querySelector('.modal-body').appendChild(visor);
                    }
                } else if(formulario.dataset.almacenEdicion==='inventario') {
                    var temporal=document.createElement('template'); temporal.innerHTML=guardado.formulario;
                    prepararFormulario(temporal.content.querySelector('form[data-almacen-edicion="inventario"]'));
                    var alerta=formulario.querySelector('[data-formulario-ok]'); alerta.textContent=guardado.mensaje; alerta.hidden=false; alerta.focus();
                } else { permitirCerrar=true; window.jQuery(modal).modal('hide'); }
            } else {
                if(ventanaGuia && !ventanaGuia.closed) ventanaGuia.close();
                if(revisando) revisarMovimiento(false); mostrarErrores(errores);
            }
        }
        async function cargar(enlace) {
            var numero=++solicitud; if(abortar) abortar.abort(); abortar=new AbortController(); formulario=null;
            estadoCarga('Cargando…'); titulo.textContent=enlace.dataset.editorTitulo; permitirCerrar=false;
            modal.classList.toggle('almacen-editor-inventario',['inventario','despacho'].includes(enlace.dataset.almacenFormulario));
            window.jQuery(modal).modal('show');
            try {
                var respuesta=await fetch(enlace.href,{credentials:'same-origin',cache:'no-store',headers:{'X-Requested-With':'XMLHttpRequest'},signal:abortar.signal});
                var html=await respuesta.text(); if(numero!==solicitud) return;
                var temporal=document.createElement('template'); temporal.innerHTML=html;
                var nuevo=temporal.content.querySelector('form[data-almacen-edicion="'+enlace.dataset.almacenFormulario+'"]');
                if(!respuesta.ok || !nuevo) { estadoCarga(respuesta.redirected?'La sesión venció. Recarga la página para continuar.':'No se pudo abrir el formulario.',!respuesta.redirected); return; }
                prepararFormulario(nuevo);
            } catch(error) { if(numero===solicitud && error.name!=='AbortError') estadoCarga('No se pudo conectar. Vuelve a intentar.',true); }
        }
        document.addEventListener('click',function(e) {
            var enlace=e.target.closest('a[data-almacen-formulario]');
            if(!enlace || e.defaultPrevented || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button!==0) return;
            e.preventDefault(); origen=enlace; cargar(enlace);
        });
        window.jQuery(modal).on('hide.bs.modal',function(e) {
            if(guardando || (!permitirCerrar && modificado() && !window.confirm('Hay cambios sin guardar. ¿Quieres cerrar y descartarlos?'))) e.preventDefault();
        }).on('hidden.bs.modal',function() {
            solicitud++; if(abortar) abortar.abort(); formulario=null; contenido.textContent='';
            if(origen && origen.isConnected) origen.focus();
            else { var buscar=document.querySelector('.dataTables_filter input'); if(buscar) buscar.focus(); }
        });
        window.addEventListener('beforeunload',function(e) { if(guardando || modificado()) { e.preventDefault(); e.returnValue=''; } });
    }

    // Select con buscador (Select2, ya cargado en el layout) para listas largas como los clientes.
    function selectsBuscables() {
        var $ = window.jQuery;
        if (!$ || !$.fn.select2) return;
        document.querySelectorAll('select[data-select-buscable]').forEach(function (select) {
            if ($(select).hasClass('select2-hidden-accessible')) return;
            $(select).select2({
                width: '100%',
                language: {noResults: function () { return 'Sin coincidencias'; }},
                matcher: function (params, data) {
                    var palabras = normalizarTexto(params.term || '').split(/\s+/).filter(Boolean), texto = normalizarTexto(data.text || '');
                    return palabras.every(function (p) { return texto.indexOf(p) !== -1; }) ? data : null;
                }
            }).on('select2:select', function () {
                // Los filtros escuchan el evento nativo; Select2 solo dispara el de jQuery.
                select.dispatchEvent(new Event('change', {bubbles: true}));
            });
        });
    }
    function sincronizarSelect(select) { if (window.jQuery && select.hasAttribute('data-select-buscable')) window.jQuery(select).trigger('change.select2'); }

    // Pendientes: confirmar antes de marcar como atendido (delegado: sirve también para filas actualizadas por el modal).
    document.addEventListener('submit', function (e) {
        var formulario = e.target.closest && e.target.closest('form[data-pendiente-atender]');
        if (!formulario || formulario.dataset.confirmado === '1') return;
        e.preventDefault();
        confirmar('Marcar pendiente como atendido', 'El pendiente pasará a Atendidos con tu usuario y la fecha de hoy.', [['Sede', formulario.dataset.sede], ['Pendiente', formulario.dataset.detalle]], 'Marcar atendido').then(function (ok) {
            if (ok) { formulario.dataset.confirmado = '1'; formulario.submit(); }
        });
    });

    function iniciar() {
        // Tras el ready de init.js, que aplica Select2 a ".select2" y alcanzaría nuestro contenedor.
        if (window.jQuery) window.jQuery(selectsBuscables);
        document.querySelectorAll('form[data-almacen-edicion]').forEach(configurarEdicion);
        var editor=document.getElementById('almacen-editor'); if(editor) iniciarEditor(editor);
        document.querySelectorAll('table[data-paginar]').forEach(paginarTabla);
        var revision = document.getElementById('form-revision-cantidades'); if (revision) iniciarRevision(revision);
        var decision = document.getElementById('form-decision-revision'); if (decision) iniciarDecision(decision);
        var asignacion = document.getElementById('form-asignacion');
        if (asignacion) iniciarAsignacion(asignacion);
        else {
            var contexto = document.getElementById('contexto-asignacion');
            if (contexto) contexto.addEventListener('change', function () { if (contexto.reportValidity()) contexto.submit(); });
        }
        var inventario = document.getElementById('form-movimiento'); if (inventario) iniciarInventario(inventario);
        var compra = document.getElementById('form-compra'), guardiaCompra = compra ? iniciarCompra(compra) : null;
        var administracion=document.getElementById('almacen-administracion'); if(administracion) iniciarAdministracion(administracion,guardiaCompra);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciar); else iniciar();
})();
