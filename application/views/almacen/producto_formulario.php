<?php defined('BASEPATH') OR exit('No direct script access allowed'); $nuevo=!$producto||!$producto->id; ?>
<?= form_open('almacen/guardar',array('data-almacen-edicion'=>'producto','id'=>'form-producto')) ?>
<div class="<?= !empty($en_modal)?'modal-body':'almacen-formulario-campos' ?>">
<div class="alert alert-danger" data-formulario-errores role="alert" tabindex="-1" <?= !$errores?'hidden':'' ?>><?php if($errores): ?><ul class="mb-0"><?php foreach($errores as $error): ?><li><?= html_escape($error) ?></li><?php endforeach; ?></ul><?php endif; ?></div>

            <input type="hidden" name="token" value="<?= html_escape($token) ?>"><input type="hidden" name="id" value="<?= $nuevo ? 0 : (int) $producto->id ?>">
            <div class="row">
                <?php foreach (array('codigo' => array('Código',40), 'descripcion' => array('Descripción',255), 'marca' => array('Marca',100), 'unidad' => array('Unidad / presentación',30)) as $campo => $def): ?>
                    <div class="col-md-<?= $campo === 'descripcion' ? '8' : '4' ?> form-group"><label for="<?= $campo ?>"><?= $def[0] ?></label><input class="form-control" id="<?= $campo ?>" name="<?= $campo ?>" maxlength="<?= $def[1] ?>" <?= $campo !== 'marca' ? 'required' : '' ?> value="<?= html_escape($producto && isset($producto->$campo) ? $producto->$campo : '') ?>"></div>
                <?php endforeach; ?>
                <?php $categoria=$producto && isset($producto->categoria) ? $producto->categoria : ''; $vence=$producto && !empty($producto->vencimiento) ? substr($producto->vencimiento,0,7) : ''; ?>
                <div class="col-md-4 form-group"><label for="categoria">Categoría</label><select class="form-control" id="categoria" name="categoria" required><option value="">Selecciona</option><?php foreach (Almacen_model::CATEGORIAS as $clave => $texto): ?><option value="<?= $clave ?>" <?= $categoria === $clave ? 'selected' : '' ?>><?= html_escape($texto) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-4 form-group"><label for="vencimiento">Vencimiento <span class="text-muted">(opcional)</span></label><input class="form-control" id="vencimiento" name="vencimiento" type="month" value="<?= html_escape($vence) ?>"></div>
                <div class="col-md-4 form-group"><label for="precio_real">Precio real de compra (S/)</label><input class="form-control" id="precio_real" name="precio_real" type="number" min="0" max="999999999.99" step="0.01" placeholder="Vacío: precio valorado" value="<?= html_escape($producto ? ($producto->precio_real === null ? $producto->precio_valorado : $producto->precio_real) : '') ?>"></div>
                <div class="col-md-4 form-group"><label for="precio_valorado">Precio valorado con IGV (S/)</label><input class="form-control" id="precio_valorado" name="precio_valorado" type="number" min="0" max="999999999.99" step="0.01" required value="<?= html_escape($producto ? $producto->precio_valorado : '') ?>"></div>
                <?php if ($nuevo): ?><div class="col-md-4 form-group"><label for="stock">Stock inicial</label><input class="form-control" id="stock" name="stock" type="number" min="0" max="999999999.999" step="0.001" required value="<?= html_escape($producto ? $producto->stock : '0') ?>"></div><?php endif; ?>
                <div class="col-md-4 form-group"><label for="activo">Estado</label><select class="form-control" name="activo" id="activo"><option value="1" <?= !$producto || $producto->activo ? 'selected' : '' ?>>Activo</option><option value="0" <?= $producto && !$producto->activo ? 'selected' : '' ?>>Inactivo</option></select></div>
            </div>
            
</div>
<div class="<?= !empty($en_modal)?'modal-footer':'almacen-formulario-acciones' ?>">
<?php if(!empty($en_modal)): ?><button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button><?php endif; ?>
<button class="btn btn-primary" type="submit">Guardar producto</button>
</div>
<?= form_close() ?>
