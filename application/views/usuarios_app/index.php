<?php defined('BASEPATH') OR exit('No direct script access allowed');
$roles = App_usuarios_model::ROLES;
$clase_rol = array('ADMIN' => 'badge-dark', 'SUPERVISOR' => 'badge-primary', 'AREA_USER' => 'badge-info');
$url = function($v) { return site_url('usuarios_app').'?vista='.urlencode($v); };
?>
<div class="content"><div class="container-fluid">
<div class="almacen-cabecera">
    <h4>Usuarios de la app <span class="badge badge-secondary">Aplicativo móvil</span></h4>
    <div class="almacen-acciones" role="group" aria-label="Acciones de la pantalla">
        <button type="button" class="btn btn-primary" data-usuario-nuevo aria-haspopup="dialog"><i class="mdi mdi-plus" aria-hidden="true"></i><span>Nuevo usuario</span></button>
    </div>
</div>

<div class="card-box">
    <div class="pendientes-barra">
        <nav class="pendientes-vistas" aria-label="Filtrar usuarios">
            <?php foreach(App_usuarios_model::VISTAS as $clave=>$texto): $actual=$vista===$clave; ?>
            <a class="btn btn-sm <?= $actual?'btn-primary':'btn-light' ?>" href="<?= html_escape($url($clave)) ?>"<?= $actual?' aria-current="page"':'' ?>><?= html_escape($texto) ?> <span class="badge <?= $actual?'badge-light':($clave==='alertas'&&$conteos['alertas']?'badge-danger':'badge-secondary') ?>"><?= (int)$conteos[$clave] ?></span></a>
            <?php endforeach; ?>
        </nav>
    </div>
    <p class="small text-muted mb-2">Cuentas con las que se inicia sesión en el aplicativo móvil. Son distintas de los usuarios de la plataforma web; un supervisor se vincula a su usuario de la plataforma para ver sus clientes.</p>

    <div class="table-responsive"><table data-almacen-tabla data-filtros-columna class="table table-striped cecon-tabla"><thead><tr>
        <th>Usuario</th><th data-filtro="lista">Rol</th><th data-filtro="lista">Área</th><th data-filtro="lista">Estado</th><th>Usuario de la plataforma</th><th>Última actividad</th><th>Notificaciones</th><th>Acciones</th>
    </tr></thead><tbody>
    <?php foreach($usuarios as $u): $editable = in_array($u->rol, App_usuarios_model::CREABLES, true); ?>
    <tr class="<?= $u->alertas ? 'cecon-fila-alerta' : '' ?>">
        <td class="pendiente-detalle"><strong><?= html_escape($u->fullname) ?></strong><div class="small text-muted"><?= html_escape($u->email) ?></div>
            <?php foreach($u->alertas as $alerta): ?><div class="small text-danger"><i class="mdi mdi-alert" aria-hidden="true"></i> <?= html_escape($alerta) ?></div><?php endforeach; ?></td>
        <td><span class="badge <?= html_escape($clase_rol[$u->rol] ?? 'badge-secondary') ?>"><?= html_escape($roles[$u->rol] ?? $u->rol) ?></span></td>
        <td><?= $u->area!==null ? html_escape($u->area).' <span class="text-muted small">('.html_escape($u->area_codigo).')</span>' : '<span class="text-muted">Sin área</span>' ?></td>
        <td><span class="badge <?= $u->is_active?'badge-success':'badge-secondary' ?>"><?= $u->is_active?'Activo':'Inactivo' ?></span></td>
        <td class="pendiente-detalle"><?php if($u->legacy_user_id!==null): ?><?= html_escape($u->legacy_nombre!=='' ? $u->legacy_nombre : 'Usuario #'.$u->legacy_user_id) ?> <span class="badge badge-light"><?= html_escape((string)$u->legacy_tipo) ?></span><?php else: ?><span class="text-muted">Sin vincular</span><?php endif; ?></td>
        <td data-order="<?= html_escape((string)$u->ultima) ?>"><?= $u->ultima ? html_escape(Cecon_model::fecha($u->ultima)) : '<span class="text-muted">Nunca</span>' ?></td>
        <td><?= $u->con_push ? '<span class="badge badge-success">Activas</span>' : '<span class="text-muted">No</span>' ?></td>
        <td class="almacen-celda-acciones"><?php if($editable): ?><div class="almacen-acciones-iconos">
            <button type="button" class="btn btn-light" data-usuario-editar data-id="<?= (int)$u->id ?>" data-nombre="<?= html_escape($u->fullname) ?>" data-email="<?= html_escape($u->email) ?>" data-rol="<?= html_escape($u->rol) ?>"
                data-area-id="<?= $u->area_id!==null?(int)$u->area_id:'' ?>" data-legacy-id="<?= $u->legacy_user_id!==null?(int)$u->legacy_user_id:'' ?>" data-legacy-nombre="<?= html_escape($u->legacy_nombre) ?>" data-legacy-tipo="<?= html_escape((string)$u->legacy_tipo) ?>" data-activo="<?= $u->is_active?'1':'0' ?>"
                data-almacen-tooltip title="Editar usuario" aria-label="Editar a <?= html_escape($u->fullname) ?>" aria-haspopup="dialog"><i class="mdi mdi-pencil" aria-hidden="true"></i></button>
        </div><?php else: ?><span class="text-muted" aria-hidden="true">—</span><span class="sr-only">No editable</span><?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody></table></div>
</div>
</div></div>

<div class="modal fade cecon-modal cecon-modal-chico" id="usuario-app-form" tabindex="-1" role="dialog" aria-labelledby="usuario-app-titulo" aria-hidden="true">
    <div class="modal-dialog" role="document"><form class="modal-content" novalidate>
        <div class="modal-header"><h5 class="modal-title" id="usuario-app-titulo">Nuevo usuario</h5><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body">
            <div class="alert alert-danger" role="alert" data-uf="error" hidden></div>
            <input type="hidden" name="token" value="<?= html_escape($token) ?>"><input type="hidden" name="id" value="0">
            <div class="form-group"><label for="uf-nombre">Nombre completo</label><input class="form-control" id="uf-nombre" name="nombre" maxlength="100" autocomplete="off" required><div class="invalid-feedback" data-uf-error="nombre"></div></div>
            <div class="form-group"><label for="uf-email">Correo (con el que inicia sesión)</label><input class="form-control" id="uf-email" name="email" type="email" maxlength="150" autocomplete="off" required><div class="invalid-feedback" data-uf-error="email"></div></div>
            <div class="form-row">
                <div class="form-group col-sm-6"><label for="uf-rol">Rol</label><select class="form-control" id="uf-rol" name="rol"><option value="SUPERVISOR">Supervisor</option><option value="AREA_USER">Usuario de área</option></select><div class="invalid-feedback" data-uf-error="rol"></div></div>
                <div class="form-group col-sm-6"><label for="uf-area">Área</label><select class="form-control" id="uf-area" name="area_id"></select><div class="invalid-feedback" data-uf-error="area"></div><small class="form-text text-muted" data-uf="ayuda-area"></small></div>
            </div>
            <div class="form-group"><label for="uf-legacy">Usuario de la plataforma <span class="text-muted" data-uf="opcional"></span></label><select class="form-control" id="uf-legacy" name="legacy_id"></select><div class="invalid-feedback" data-uf-error="legacy_id"></div><small class="form-text text-muted" data-uf="ayuda-legacy"></small></div>
            <div class="form-group form-check" data-uf="fila-activo" hidden><input type="checkbox" class="form-check-input" id="uf-activo" name="activo" value="1"><label class="form-check-label" for="uf-activo">Usuario activo (puede iniciar sesión en la app)</label></div>
            <p class="small text-muted mb-0" data-uf="aviso-clave">Al crearlo se generará una contraseña segura y se mostrará una sola vez, para que se la entregue al usuario.</p>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary" data-uf="guardar">Guardar</button></div>
    </form></div>
</div>

<div class="modal fade cecon-modal cecon-modal-chico" id="usuario-app-clave" tabindex="-1" role="dialog" aria-labelledby="usuario-app-clave-titulo" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="usuario-app-clave-titulo">Usuario creado</h5></div>
        <div class="modal-body">
            <p>Entregue estas credenciales al usuario de forma segura. <strong>La contraseña no se podrá volver a ver.</strong></p>
            <dl class="mb-2"><dt>Correo</dt><dd data-uc="email"></dd><dt>Contraseña</dt>
                <dd class="d-flex align-items-center" style="gap:8px"><code class="cecon-clave" data-uc="clave"></code><button type="button" class="btn btn-sm btn-light" data-uc="copiar"><i class="mdi mdi-content-copy" aria-hidden="true"></i> Copiar</button></dd></dl>
            <p class="small text-success mb-0" role="status" aria-live="polite" data-uc="copiado"></p>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-primary" data-uc="listo">Ya la guardé, cerrar</button></div>
    </div></div>
</div>

<?php $this->load->view('almacen/datatables'); ?>
<script>
jQuery(function($) {
    var datos={areas:<?= json_encode($areas, JSON_UNESCAPED_UNICODE) ?>, candidatos:<?= json_encode($candidatos, JSON_UNESCAPED_UNICODE) ?>, url:<?= json_encode(site_url('usuarios_app/guardar')) ?>, aplicativo:'OPE'};
    var modal=$('#usuario-app-form'), form=modal.find('form')[0], clave=$('#usuario-app-clave'), enviando=false;
    function el(n) { return form.querySelector('[data-uf="'+n+'"]'); }
    function opcion(valor, texto, extra) { var o=document.createElement('option'); o.value=valor; o.textContent=texto; if(extra) o.dataset.email=extra; return o; }
    function limpiarErrores() {
        form.querySelectorAll('.is-invalid').forEach(function(c) { c.classList.remove('is-invalid'); });
        form.querySelectorAll('[data-uf-error]').forEach(function(d) { d.textContent=''; });
        el('error').hidden=true; el('error').textContent='';
    }
    function pintarAreas(rol, seleccion) {
        var s=form.area_id; s.textContent='';
        if(rol==='SUPERVISOR') {
            var ope=datos.areas.filter(function(a) { return a.code===datos.aplicativo; })[0];
            s.appendChild(opcion(ope?ope.id:'', ope?ope.spanish_name+' ('+ope.code+')':'Operaciones (OPE)')); s.disabled=true;
            el('ayuda-area').textContent='Un supervisor siempre va en Operaciones: así la app lo lleva a su pantalla de supervisor.';
        } else {
            s.disabled=false; s.appendChild(opcion('', 'Elija un área…'));
            datos.areas.filter(function(a) { return a.code!==datos.aplicativo; }).forEach(function(a) { s.appendChild(opcion(a.id, a.spanish_name+' ('+a.code+')')); });
            s.value=seleccion||''; el('ayuda-area').textContent='No puede ser Operaciones: en esa área la app lo trataría como supervisor.';
        }
    }
    function pintarCandidatos(rol, actual) {
        var s=form.legacy_id; s.textContent=''; s.appendChild(opcion('', rol==='SUPERVISOR'?'Elija el supervisor de la plataforma…':'Sin vincular'));
        if(actual && actual.id && !datos.candidatos[rol].some(function(c) { return String(c.id)===String(actual.id); })) s.appendChild(opcion(actual.id, actual.nombre+' ('+actual.tipo+') · actual'));
        datos.candidatos[rol].forEach(function(c) { s.appendChild(opcion(c.id, (c.nombre||('Usuario #'+c.id))+' ('+c.tipo+')', (c.email||'').toLowerCase())); });
        if(actual && actual.id) s.value=actual.id;
        el('opcional').textContent=rol==='SUPERVISOR'?'(obligatorio)':'(opcional)';
        el('ayuda-legacy').textContent=rol==='SUPERVISOR'?'Solo supervisores activos de la plataforma que aún no tienen usuario en el app. Con este vínculo el supervisor ve sus clientes.':'Opcional: relaciona esta cuenta con un usuario activo de la plataforma que aún no esté vinculado.';
    }
    function sugerir() {
        var correo=form.email.value.trim().toLowerCase(); if(!correo || form.id.value!=='0') return;
        var coincide=[].filter.call(form.legacy_id.options, function(o) { return o.dataset.email===correo; })[0];
        if(coincide && !form.legacy_id.value) { form.legacy_id.value=coincide.value; el('ayuda-legacy').textContent='Sugerido: coincide por correo con el usuario de la plataforma.'; }
    }
    function abrir(modo, d) {
        limpiarErrores(); form.reset(); var nuevo=modo==='nuevo';
        form.id.value=nuevo?'0':d.id; form.token.value=form.token.value;
        $('#usuario-app-titulo').text(nuevo?'Nuevo usuario':'Editar usuario');
        form.nombre.value=nuevo?'':d.nombre; form.email.value=nuevo?'':d.email;
        form.nombre.readOnly=!nuevo; form.email.readOnly=!nuevo; form.rol.disabled=!nuevo; form.rol.value=nuevo?'SUPERVISOR':d.rol;
        el('fila-activo').hidden=nuevo; form.activo.checked=!nuevo && d.activo==='1'; el('aviso-clave').hidden=!nuevo;
        pintarAreas(form.rol.value, nuevo?'':d.areaId);
        pintarCandidatos(form.rol.value, nuevo||!d.legacyId?null:{id:d.legacyId, nombre:d.legacyNombre||('Usuario #'+d.legacyId), tipo:d.legacyTipo});
        modal.modal('show');
    }
    $(document).on('click','[data-usuario-nuevo]', function() { abrir('nuevo'); });
    $(document).on('click','[data-usuario-editar]', function() { abrir('editar', this.dataset); });
    form.rol.addEventListener('change', function() { pintarAreas(form.rol.value, ''); pintarCandidatos(form.rol.value, null); sugerir(); });
    form.email.addEventListener('input', sugerir);
    modal.on('shown.bs.modal', function() { (form.nombre.readOnly ? form.legacy_id : form.nombre).focus(); });
    form.addEventListener('submit', function(e) {
        e.preventDefault(); if(enviando) return; limpiarErrores();
        var datosForm=new FormData(form); datosForm.set('rol', form.rol.value); datosForm.set('area_id', form.area_id.value); if(!form.activo.checked) datosForm.delete('activo');
        var boton=el('guardar'); enviando=true; boton.disabled=true; boton.textContent='Guardando…';
        fetch(datos.url, {method:'POST', body:datosForm, credentials:'same-origin', headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(function(r) { return r.json().catch(function() { return {error:'Respuesta inesperada del servidor. Actualice la página.'}; }).then(function(d) { return {estado:r.status, d:d}; }); })
            .then(function(res) {
                var d=res.d;
                if(res.estado===201 || (res.estado===200 && d.ok)) {
                    modal.modal('hide');
                    if(res.estado===201) { clave.find('[data-uc="email"]').text(d.email); clave.find('[data-uc="clave"]').text(d.clave); clave.find('[data-uc="copiado"]').text(''); clave.modal('show'); }
                    else location.reload();
                    return;
                }
                var primero=null;
                Object.keys(d.errores||{}).forEach(function(k) {
                    var c=k==='area'?form.area_id:form[k]; if(c) { c.classList.add('is-invalid'); if(!primero) primero=c; }
                    var m=form.querySelector('[data-uf-error="'+k+'"]'); if(m) m.textContent=d.errores[k];
                });
                if(d.error) { el('error').textContent=d.error; el('error').hidden=false; }
                if(primero) primero.focus();
            })
            .catch(function() { el('error').textContent='No se pudo conectar. Intente nuevamente.'; el('error').hidden=false; })
            .then(function() { enviando=false; boton.disabled=false; boton.textContent='Guardar'; });
    });
    clave.find('[data-uc="copiar"]').on('click', function() {
        var texto=clave.find('[data-uc="clave"]').text(), aviso=clave.find('[data-uc="copiado"]');
        function ok() { aviso.text('Contraseña copiada.'); } function falla() { aviso.text('No se pudo copiar; selecciónela y cópiela manualmente.'); }
        if(navigator.clipboard && window.isSecureContext) navigator.clipboard.writeText(texto).then(ok, falla);
        else { var r=document.createRange(); r.selectNodeContents(clave.find('[data-uc="clave"]')[0]); var s=getSelection(); s.removeAllRanges(); s.addRange(r); try { document.execCommand('copy') ? ok() : falla(); } catch(e) { falla(); } }
    });
    clave.find('[data-uc="listo"]').on('click', function() { clave.find('[data-uc="clave"]').text(''); clave.modal('hide'); location.reload(); });
});
</script>
