(function () {
    'use strict';
    function iniciar() {
        var modal=document.getElementById('almacen-asignaciones');
        if (!modal || !window.jQuery || !window.jQuery.fn.modal) return;
        var contenido=document.getElementById('almacen-asignaciones-contenido');
        var titulo=document.getElementById('almacen-asignaciones-titulo');
        var origen=null, solicitud=0, abortar=null;

        function estado(destino,mensaje,reintentar) {
            destino.textContent='';
            var texto=document.createElement('p'); texto.className='mb-2'; texto.setAttribute('role','status'); texto.textContent=mensaje;
            var bloque=document.createElement('div'); bloque.appendChild(texto); destino.appendChild(bloque);
            if (reintentar) {
                var boton=document.createElement('button'); boton.type='button'; boton.className='btn btn-light'; boton.textContent='Reintentar';
                boton.addEventListener('click',reintentar); bloque.appendChild(boton);
            }
        }
        function comenzar(destino) {
            var numero=++solicitud;
            if (abortar) abortar.abort();
            abortar=new AbortController();
            destino.setAttribute('aria-busy','true'); estado(destino,'Cargando…');
            return numero;
        }
        async function consultar(url,selector) {
            var respuesta=await fetch(url,{credentials:'same-origin',cache:'no-store',headers:{'X-Requested-With':'XMLHttpRequest'},signal:abortar.signal});
            var html=await respuesta.text(), temporal=document.createElement('template'); temporal.innerHTML=html;
            var fragmento=temporal.content.querySelector(selector);
            if (!respuesta.ok || respuesta.redirected || !fragmento || fragmento.dataset.clienteId!==origen.dataset.clienteId) {
                var error=new Error('No se pudo cargar la consulta.');
                error.sesion=respuesta.redirected || respuesta.status===401 || respuesta.status===403;
                error.inexistente=respuesta.status===404;
                throw error;
            }
            return fragmento;
        }
        function fallo(destino,error,reintentar) {
            var mensaje=error.sesion?'La sesión venció o no tienes permiso. Recarga la página para continuar.':
                error.inexistente?'La asignación o el cliente ya no está disponible.':'No se pudo cargar. Vuelve a intentar.';
            estado(destino,mensaje,error.sesion || error.inexistente?null:reintentar);
        }
        async function cargar(enlace) {
            origen=enlace; titulo.textContent='Asignaciones · '+enlace.dataset.clienteNombre;
            var numero=comenzar(contenido);
            window.jQuery(modal).modal('show');
            try {
                var nuevo=await consultar(enlace.href,'[data-cliente-asignaciones-contenido]');
                if (numero!==solicitud) return;
                contenido.textContent=''; contenido.appendChild(nuevo); contenido.scrollTop=0;
            } catch (error) {
                if (numero===solicitud && error.name!=='AbortError') fallo(contenido,error,function() { cargar(enlace); });
            } finally { if (numero===solicitud) contenido.setAttribute('aria-busy','false'); }
        }
        async function seleccionar(enlace) {
            var destino=document.getElementById('almacen-asignacion-detalle');
            var actual=destino.querySelector('[data-asignacion-consulta]');
            if (actual && actual.dataset.asignacionId===enlace.dataset.asignacionId) return;
            var numero=comenzar(destino);
            try {
                var nuevo=await consultar(enlace.href,'[data-asignacion-consulta]');
                if (numero!==solicitud) return;
                if (nuevo.dataset.asignacionId!==enlace.dataset.asignacionId) throw new Error('Asignación incorrecta.');
                destino.textContent=''; destino.appendChild(nuevo); destino.scrollTop=0;
                contenido.querySelectorAll('[data-asignacion-detalle]').forEach(function(item) {
                    var activo=item===enlace; item.classList.toggle('active',activo);
                    if (activo) item.setAttribute('aria-current','true'); else item.removeAttribute('aria-current');
                });
                contenido.querySelector('[data-asignacion-anuncio]').textContent='Asignación de '+nuevo.getAttribute('aria-label')+' cargada.';
            } catch (error) {
                if (numero===solicitud && error.name!=='AbortError') fallo(destino,error,function() { seleccionar(enlace); });
            } finally { if (numero===solicitud) destino.setAttribute('aria-busy','false'); }
        }
        document.addEventListener('click',function(e) {
            if (e.defaultPrevented || e.button!==0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
            var enlace=e.target.closest('a[data-cliente-asignaciones]');
            if (enlace) { e.preventDefault(); cargar(enlace); return; }
            enlace=e.target.closest('#almacen-asignaciones a[data-asignacion-detalle]');
            if (enlace) { e.preventDefault(); seleccionar(enlace); }
        });
        window.jQuery(modal).on('hidden.bs.modal',function() {
            solicitud++; if (abortar) abortar.abort(); contenido.textContent=''; contenido.setAttribute('aria-busy','false');
            if (origen && origen.isConnected) origen.focus({preventScroll:true});
        });
    }
    if (document.readyState==='loading') document.addEventListener('DOMContentLoaded',iniciar); else iniciar();
})();
