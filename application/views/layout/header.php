<?php
require_once APPPATH.'libraries/Almacen_acceso.php';
$usuario_nombre = trim((string) $this->session->userdata('nombre_usu'));
$usuario_rol = (string) $this->session->userdata('tipo_usu');
$roles_usuario = array('SA' => 'Super Admin', 'AD' => 'Administrador', 'RC' => 'Recibos', 'GE' => 'Gerencia', 'US' => 'Usuario') + Almacen_acceso::roles();
$usuario_rol_nombre = isset($roles_usuario[$usuario_rol]) ? $roles_usuario[$usuario_rol] : $usuario_rol;
$partes_nombre = preg_split('/\s+/', $usuario_nombre, -1, PREG_SPLIT_NO_EMPTY);
$usuario_iniciales = mb_strtoupper(implode('', array_map(function ($p) { return mb_substr($p, 0, 1); }, array_slice($partes_nombre, 0, 2))));
?>
<!-- LOGO -->
<div class="topbar-left">
    <a href="<?= site_url() ?>" class="logo" aria-label="Segelim Perú · Inicio">
        <span><img src="<?= base_url() ?>assets/images/logo_marca_claro.png" alt="Segelim Perú" class="marco-logo"></span>
        <i><img src="<?= base_url() ?>assets/images/logo_gota_claro.png" alt="Segelim Perú" class="marco-logo-sm"></i>
    </a>
</div>

<nav class="navbar-custom">
    <ul class="list-inline float-right mb-0">
        <li class="list-inline-item dropdown notification-list">
            <a class="nav-link dropdown-toggle arrow-none marco-usuario" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" aria-label="Menú de usuario">
                <span class="marco-avatar" aria-hidden="true"><?= html_escape($usuario_iniciales !== '' ? $usuario_iniciales : '?') ?></span>
                <span class="marco-usuario-texto hide-phone"><strong><?= html_escape($usuario_nombre) ?></strong><small><?= html_escape($usuario_rol_nombre) ?></small></span>
                <i class="mdi mdi-chevron-down hide-phone" aria-hidden="true"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                <div class="dropdown-item noti-title"><h5 class="text-overflow"><?= html_escape($usuario_nombre) ?><small class="d-block"><?= html_escape($usuario_rol_nombre) ?></small></h5></div>
                <a href="<?= site_url() ?>" class="dropdown-item notify-item"><i class="mdi mdi-home"></i> <span>Inicio</span></a>
                <a href="<?= site_url('login/logout') ?>" class="dropdown-item notify-item"><i class="mdi mdi-logout"></i> <span>Cerrar sesión</span></a>
            </div>
        </li>
    </ul>

    <ul class="list-inline menu-left mb-0">
        <li class="float-left">
            <button class="button-menu-mobile open-left waves-light waves-effect" aria-label="Mostrar u ocultar menú">
                <i class="dripicons-menu"></i>
            </button>
        </li>
    </ul>
</nav>
<!-- Top Bar End -->
