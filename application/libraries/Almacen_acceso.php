<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Permissions for the two operational warehouse roles, shared by routes and views. */
class Almacen_acceso
{
    public static function roles()
    {
        return array('SP' => 'Supervisor', 'AL' => 'Almacén', 'CM' => 'Comercial');
    }

    public static function es_operativo($rol)
    {
        return isset(self::roles()[$rol]);
    }

    public static function inicio($rol)
    {
        if ($rol === 'SP') return 'almacen/asignacion';
        return $rol === 'CM' ? 'almacen/clientes' : 'almacen/revision_asignaciones';
    }

    public static function permite($rol, $controlador, $metodo)
    {
        $controlador = strtolower($controlador);
        $metodo = strtolower($metodo);
        if ($controlador !== 'almacen') {
            if (!self::es_operativo($rol)) return true;
            if ($controlador === 'index') return $metodo === 'index';
            return $controlador === 'login' && in_array($metodo, array('index', 'access_login', 'logout', 'logueado'), true);
        }
        if (in_array($rol, array('AD', 'SA'), true)) return true;
        $consulta_asignaciones = array('index', 'asignacion', 'asignaciones', 'imprimir_asignacion', 'exportar_asignacion');
        $pendientes = array('pendientes', 'pendiente', 'guardar_pendiente');
        if ($rol === 'SP') return in_array($metodo, array_merge($consulta_asignaciones, $pendientes, array('guardar_asignacion')), true);
        // Comercial: solo crear y editar los datos de clientes (sin asignaciones ni inventario).
        if ($rol === 'CM') return in_array($metodo, array('index', 'clientes', 'cliente', 'guardar_cliente'), true);
        if ($rol === 'AL') {
            return in_array($metodo, array_merge($consulta_asignaciones, array(
                'productos', 'producto', 'guardar', 'inventario', 'movimiento', 'pendientes', 'pendiente', 'guardar_pendiente', 'atender_pendiente',
                'clientes', 'cliente', 'guardar_cliente',
                'revision_asignaciones', 'revisar_asignacion', 'guardar_revision_asignacion', 'decidir_asignacion',
                'despacho', 'confirmar_despacho', 'guia_salida',
                'ordenes_compra', 'consolidar_compras', 'orden_compra', 'guardar_compra', 'imprimir_compra', 'exportar_compra'
            )), true);
        }
        return false;
    }
}
