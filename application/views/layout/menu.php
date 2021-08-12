<!-- content menu -->
<div class="slimscroll-menu" id="remove-scroll">
  <!--- Sidemenu -->
  <div id="sidebar-menu">
    <!-- Left Menu Start -->
    <ul class="metismenu" id="side-menu">
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
      <?php endif ?>

      <li class="menu-title" id="navigation">Navigation</li>
      <?php if (isset($menu_tables) && $menu_tables) : ?>
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
      <li>
        <a href="<?= site_url() ?>contratos/">
          <i class="mdi mdi-account-card-details"></i><span>Contratos</span>
        </a>
      </li>
      <?php if ($this->session->userdata('tipo_usu') && ($this->session->userdata('tipo_usu') == "SA" || $this->session->userdata('tipo_usu') == "AD")) : ?>
        <li><a href="<?= site_url() ?>recibo/listar"><i class="mdi mdi-account-card-details"></i><span>Recibos</span></a></li>
        <li><a href="<?= site_url() ?>recibo/actas"><i class="mdi mdi-account-card-details"></i><span>Conformidades</span></a></li>
        <li><a href="<?= site_url() ?>recibo/certificados"><i class="mdi mdi-account-card-details"></i><span>Certificados</span></a></li>
      <?php endif ?>
      <li>
        <a href="javascript: void(0);">
          <i class="mdi mdi-account-card-details"></i><span>Boletas Uniforme</span>
        </a>
        <ul class="nav-second-level" aria-expanded=false>
          <?php if ($this->session->userdata('tipo_usu') && ($this->session->userdata('tipo_usu') == "SA" || $this->session->userdata('tipo_usu') == "AD")) : ?>
            <li><a href="<?= site_url() ?>BoletasUnif/register">Registrar Boleta</a></li>
          <?php endif ?>
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
    </ul>
  </div>
  <!-- Sidebar -->
  <div class="clearfix"></div>

</div>
<!-- content menu -->