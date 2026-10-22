<?php
require_once APPPATH.'libraries/Almacen_acceso.php';
$rol_almacen = $this->session->userdata('tipo_usu');
$es_rol_almacen = Almacen_acceso::es_operativo($rol_almacen);
?>
<!-- content menu -->
<div class="slimscroll-menu" id="remove-scroll">
	<!--- Sidemenu -->
	<div id="sidebar-menu">
		<!-- Left Menu Start -->
		<ul class="metismenu" id="side-menu">
            <?php if ($es_rol_almacen): ?>
                <li class="menu-title"><?= html_escape(Almacen_acceso::roles()[$rol_almacen]) ?></li>
                <?php if ($rol_almacen === 'CM'): ?>
                    <li><a href="<?= site_url('almacen/clientes') ?>"><i class="dripicons-user-group"></i><span>Clientes</span></a></li>
                <?php elseif ($rol_almacen === 'SP'): ?>
                    <li><a href="<?= site_url('almacen/asignacion') ?>"><i class="mdi mdi-calendar"></i><span>Asignación mensual</span></a></li>
                    <li><a href="<?= site_url('almacen/asignaciones') ?>"><i class="mdi mdi-format-list-bulleted"></i><span>Asignaciones del mes</span></a></li>
                    <li><a href="<?= site_url('almacen/pendientes') ?>"><i class="mdi mdi-clipboard-alert"></i><span>Pendientes</span></a></li>
                <?php else: ?>
                    <li><a href="<?= site_url('almacen/productos') ?>"><i class="mdi mdi-package-variant"></i><span>Productos e inventario</span></a></li>
                    <li><a href="<?= site_url('almacen/clientes') ?>"><i class="dripicons-user-group"></i><span>Clientes y presupuestos</span></a></li>
                    <li><a href="<?= site_url('almacen/asignaciones') ?>"><i class="mdi mdi-calendar"></i><span>Consultar asignaciones</span></a></li>
                    <li><a href="<?= site_url('almacen/revision_asignaciones') ?>"><i class="mdi mdi-check-circle"></i><span>Administración de almacén</span></a></li>
                    <li><a href="<?= site_url('almacen/pendientes') ?>"><i class="mdi mdi-clipboard-alert"></i><span>Pendientes</span></a></li>
                <?php endif; ?>
                <li class="menu-title">Mi cuenta</li>
                <li><a href="<?= site_url('login/logout') ?>"><i class="mdi mdi-logout"></i><span>Cerrar sesión</span></a></li>
            <?php else: ?>
			<?php if ($this->session->userdata('tipo_usu') && $this->session->userdata('tipo_usu') == "SA") : ?>
				<li class="menu-title">Super Admin</li>
				<li>
					<a href="javascript: void(0);">
						<i class="fi-air-play"></i><span> Administrator </span>
					</a>
					<ul class="nav-second-level" aria-expanded=false>
						<li><a href="<?= site_url() ?>admin">Admin</a></li>
						<li><a href="<?= site_url() ?>admin/configurator">Configurador</a></li>
					</ul>
				</li>
			<?php endif ?>
			<?php if (($this->session->userdata('tipo_usu') && $this->session->userdata('tipo_usu') == "SA") || $this->session->userdata('tipo_usu') == "AD") : ?>
				<li class="menu-title">Administrador</li>
				<li><a href="<?= site_url('almacen/productos') ?>"><i class="mdi mdi-package-variant"></i><span>Productos e inventario</span></a></li>
				<li><a href="<?= site_url('almacen/clientes') ?>"><i class="dripicons-user-group"></i><span>Clientes</span></a></li>
				<li><a href="<?= site_url('almacen/asignacion') ?>"><i class="mdi mdi-calendar"></i><span>Asignación mensual</span></a></li>
				<li><a href="<?= site_url('almacen/revision_asignaciones') ?>"><i class="mdi mdi-check-circle"></i><span>Administración de almacén</span></a></li>
				<li><a href="<?= site_url('almacen/pendientes') ?>"><i class="mdi mdi-clipboard-alert"></i><span>Pendientes de almacén</span></a></li>
				<li>
					<a href="javascript: void(0);">
						<i class="dripicons-user-group"></i><?= $this->session->userdata('count_suscribers') ?  '<span class="badge badge-warning pull-right">' . $this->session->userdata('count_suscribers') . '</span>' : "" ?><span>Usuarios</span>
					</a>
					<ul class="nav-second-level" aria-expanded=false>
						<li><a href="<?= site_url() ?>usuarios/usuarios_reg">Recien Registrados<?= $this->session->userdata('count_suscribers') ?  '<span class="badge badge-warning pull-right">' . $this->session->userdata('count_suscribers') . '</span>' : "" ?></a></li>
						<li><a href="<?= site_url() ?>usuarios/">Todos Usuarios</a></li>
					</ul>
				</li>
				<li>
					<a href="javascript: void(0);"><i class="fa fa-address-book-o"></i><span>Cat Trabajador</span></a>
					<ul class="nav-second-level" aria-expanded=false>
						<li><a class="menu_dynamic" onclick="redirect_menu('<?= site_url() ?>form/create','Type_working', 'Categoría de Trabajador')" href="#">Registrar</a></li>
						<li><a class="menu_dynamic" onclick="redirect_menu('<?= site_url() ?>form/listar','Type_working', 'Categoría de Trabajador')" href="#">Listar </a></li>
					</ul>
				</li>
				<li>
					<a href="<?= site_url() ?>permisos">
						<i class="fa fa-drivers-license-o"></i><span> Permisos</span>
					</a>
				</li>
				<li>
					<a href="javascript: void(0);">
						<i class="mdi mdi-factory"></i><span>Operaciones</span>
					</a>
					<ul class="nav-second-level" aria-expanded=false>
						<li><a href="<?= site_url('cecon') ?>">CECON</a></li>
						<li><a href="<?= site_url('cecon/asistencia') ?>">Monitor de asistencia</a></li>
					</ul>
				</li>
			<?php endif ?>

			<li class="menu-title" id="navigation">Navigation</li>
			<?php if (isset($menu_tables) && $menu_tables && $this->session->userdata('tipo_usu') != "RC") : ?>
				<?php foreach ($menu_tables as $value) : ?>
					<?php if (isset($value->escritura) && isset($value->lectura) && ($value->escritura != 0 || $value->lectura != 0)) : ?>
						<li id="<?= $value->nameTable; ?>">
							<a href="javascript: void(0);">
								<i class="fi-paper"></i><span><?= $value->aliasTable ?></span>
							</a>
							<ul class="nav-second-level" aria-expanded=false>
								<?php if ($value->escritura == 1 && $value->lectura == 1) : ?>
									<li><a class="menu_dynamic" onclick="redirect_menu('<?= site_url() ?>form/create','<?= $value->nameTable ?>', '<?= $value->aliasTable ?>')" href="#">Registrar</a></li>
									<li><a class="menu_dynamic" onclick="redirect_menu('<?= site_url() ?>form/listar','<?= $value->nameTable ?>', '<?= $value->aliasTable ?>')" href="#">Listar </a></li>
								<?php elseif ($value->escritura == 0 && $value->lectura == 1) : ?>
									<li><a class="menu_dynamic" onclick="redirect_menu('<?= site_url() ?>form/listar','<?= $value->nameTable ?>', '<?= $value->aliasTable ?>')" href="#">Listar </a></li>
								<?php elseif ($value->escritura == 1 && $value->lectura == 0) : ?>
									<li><a class="menu_dynamic" onclick="redirect_menu('<?= site_url() ?>form/create','<?= $value->nameTable ?>', '<?= $value->aliasTable ?>')" href="#">Registrar</a></li>
									<li><a class="menu_dynamic" onclick="redirect_menu('<?= site_url() ?>form/listar','<?= $value->nameTable ?>', '<?= $value->aliasTable ?>')" href="#">Listar </a></li>
								<?php endif ?>
							</ul>
						</li>
					<?php elseif (isset($value->escritura) != true) : ?>
						<li id="<?= $value->nameTable; ?>">
							<a href="javascript: void(0);">
								<i class="fi-paper"></i><span><?= $value->aliasTable ?></span>
							</a>
							<ul class="nav-second-level" aria-expanded=false>
								<li><a class="menu_dynamic" onclick="redirect_menu('<?= site_url() ?>form/create','<?= $value->nameTable ?>', '<?= $value->aliasTable ?>')" href="#">Registrar</a></li>
								<li><a class="menu_dynamic" onclick="redirect_menu('<?= site_url() ?>form/listar','<?= $value->nameTable ?>', '<?= $value->aliasTable ?>')" href="#">Listar </a></li>
							</ul>
						</li>
					<?php endif ?>

				<?php endforeach ?>
			<?php endif ?>

			<?php if (($this->session->userdata('tipo_usu') && $this->session->userdata('tipo_usu') == "RC")) : ?>
				<li><a href="<?= site_url() ?>recibo/listar"><i class="mdi mdi-account-card-details"></i><span>Recibos</span></a></li>
				<li class="menu-title">Config</li>
				<li>
					<a href="<?php echo site_url('login/logout'); ?>">
						<i class="mdi mdi-logout"></i><span> Cerrar sesión</span>
					</a>
				</li>
			<?php elseif (($this->session->userdata('tipo_usu') && $this->session->userdata('tipo_usu') == "GE")) : ?>
				<li><a href="<?= site_url() ?>recibo/actas"><i class="mdi mdi-account-card-details"></i><span>Conformidades</span></a></li>
				<li><a href="<?= site_url() ?>recibo/certificados"><i class="mdi mdi-account-card-details"></i><span>Certificados</span></a></li>
			<?php else : ?>
				<li>
					<a href="<?= site_url() ?>contratos/">
						<i class="mdi mdi-account-card-details"></i><span>Contratos</span>
					</a>
				</li>
				<?php if ($this->session->userdata('tipo_usu') && ($this->session->userdata('tipo_usu') == "SA" || $this->session->userdata('tipo_usu') == "AD")) : ?>
					<li><a href="<?= site_url() ?>personal/asistencia"><i class="mdi mdi-account-card-details"></i><span>Control asistencias</span></a></li>

					<li><a href="<?= site_url() ?>recibo/listar"><i class="mdi mdi-account-card-details"></i><span>Recibos</span></a></li>
					<li><a href="<?= site_url() ?>recibo/actas"><i class="mdi mdi-account-card-details"></i><span>Conformidades</span></a></li>
					<li><a href="<?= site_url() ?>recibo/certificados"><i class="mdi mdi-account-card-details"></i><span>Certificados</span></a></li>
				<?php endif ?>
				<li>
					<a href="javascript: void(0);">
						<i class="mdi mdi-account-card-details"></i><span>Boletas Uniforme</span>
					</a>
					<ul class="nav-second-level" aria-expanded=false>
						<li><a href="<?= site_url() ?>BoletasUnif/register">Registrar Boleta</a></li>
						<li><a href="<?= site_url() ?>BoletasUnif/listar">Listar Boletas</a></li>
					</ul>
				</li>
				<li id="Personal">
					<a href="javascript: void(0);">
						<i class="fi-paper"></i><span>Personal</span>
					</a>
					<ul class="nav-second-level" aria-expanded=false>
						<li><a class="" href="<?= site_url() ?>personal/registrar">Registrar</a></li>
						<li><a class="" href="<?= site_url() ?>personal/listar">Listar </a></li>
					</ul>
				</li>
				<li class="menu-title">Config</li>
				<li>
					<a href="<?php echo site_url('login/logout'); ?>">
						<i class="mdi mdi-logout"></i><span> Cerrar sesión</span>
					</a>
				</li>
			<?php endif ?>
            <?php endif; ?>
		</ul>
	</div>
	<!-- Sidebar -->
	<div class="clearfix"></div>

</div>
<!-- content menu -->