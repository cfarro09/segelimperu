<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// CECON · Monitor de asistencia: qué clientes y supervisores ya registraron la asistencia de un día y, sobre todo, cuáles no.
// Solo lectura sobre app_attendances (la escribe el API). Cuenta operarios; no lee ni muestra sus nombres.
class Cecon_asistencia_model extends CI_Model
{
    const VISTAS = array('sin' => 'Sin asistencia', 'con' => 'Con asistencia', 'todos' => 'Todos los clientes', 'supervisores' => 'Por supervisor');

    public function __construct() { parent::__construct(); $this->load->database(); }

    public function disponible()
    {
        foreach (array('app_attendances', 'almacen_clientes', 'Usuarios') as $tabla) {
            if (!$this->db->table_exists($tabla)) return false;
        }
        return true;
    }

    // El día de asistencia es el día calendario en America/Lima (así lo guarda el API en attendance_date).
    public static function hoy() { return (new DateTime('now', new DateTimeZone('America/Lima')))->format('Y-m-d'); }

    public static function fecha_valida($fecha)
    {
        if (!is_string($fecha) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) return null;
        $d = DateTime::createFromFormat('!Y-m-d', $fecha);
        return $d && $d->format('Y-m-d') === $fecha ? $fecha : null;
    }

    // Todos los clientes activos con lo que se registró ese día (solo filas REGISTERED; las anuladas no cuentan).
    public function clientes($fecha)
    {
        $sql = "SELECT c.id, c.codigo, c.nombre, c.supervisor_id,
                       TRIM(CONCAT(COALESCE(u.nombre_usu,''), ' ', COALESCE(u.apellido_pat,''))) AS supervisor,
                       c.dias_servicio, c.hora_ingreso, c.hora_salida,
                       COALESCE(a.operarios, 0) AS operarios, COALESCE(a.envios, 0) AS envios, a.primero, a.ultimo
                  FROM almacen_clientes c
                  LEFT JOIN Usuarios u ON u.id_usu = c.supervisor_id
                  LEFT JOIN (SELECT client_id, COUNT(*) AS operarios, COUNT(DISTINCT submission_id) AS envios,
                                    MIN(attended_at) AS primero, MAX(attended_at) AS ultimo
                               FROM app_attendances WHERE attendance_date = ? AND status = 'REGISTERED' GROUP BY client_id) a
                    ON a.client_id = c.id
                 WHERE c.activo = 1
                 ORDER BY (a.client_id IS NOT NULL), c.nombre, c.id";
        $filas = $this->db->query($sql, array($fecha))->result();
        foreach ($filas as $f) {
            $f->operarios = (int) $f->operarios;
            $f->envios = (int) $f->envios;
            $f->registrada = $f->operarios > 0;
            $f->supervisor = trim((string) $f->supervisor);
            $f->con_supervisor = $f->supervisor_id !== null;
        }
        return $filas;
    }

    // Resumen por supervisor a partir de la lista de clientes (los clientes sin supervisor van en su propio grupo).
    public static function supervisores($clientes)
    {
        $grupos = array();
        foreach ($clientes as $c) {
            $clave = $c->con_supervisor ? (int) $c->supervisor_id : 0;
            if (!isset($grupos[$clave])) {
                $grupos[$clave] = (object) array('nombre' => $c->con_supervisor ? ($c->supervisor !== '' ? $c->supervisor : 'Supervisor #' . $clave) : 'Sin supervisor asignado',
                    'asignado' => $c->con_supervisor, 'clientes' => 0, 'con' => 0, 'operarios' => 0, 'ultimo' => null);
            }
            $g = $grupos[$clave];
            $g->clientes++;
            if ($c->registrada) { $g->con++; $g->operarios += $c->operarios; if ($g->ultimo === null || $c->ultimo > $g->ultimo) $g->ultimo = $c->ultimo; }
        }
        foreach ($grupos as $g) {
            $g->sin = $g->clientes - $g->con;
            $g->porcentaje = $g->clientes ? (int) round($g->con * 100 / $g->clientes) : 0;
            if (!$g->asignado) $g->estado = array('Sin supervisor', 'badge-secondary');
            elseif ($g->con === 0) $g->estado = array('Sin registros', 'badge-danger');
            elseif ($g->sin > 0) $g->estado = array('Parcial', 'badge-warning');
            else $g->estado = array('Completo', 'badge-success');
        }
        uasort($grupos, function ($a, $b) { return array($b->asignado, $a->porcentaje, $a->nombre) <=> array($a->asignado, $b->porcentaje, $b->nombre); });
        return array_values($grupos);
    }

    public static function resumen($clientes, $supervisores)
    {
        $con = 0; $sin_supervisor = 0; $operarios = 0;
        foreach ($clientes as $c) { if ($c->registrada) { $con++; $operarios += $c->operarios; } if (!$c->con_supervisor) $sin_supervisor++; }
        $total = count($clientes);
        $sin_registrar = 0; $asignados = 0;
        foreach ($supervisores as $s) { if ($s->asignado) { $asignados++; if ($s->con === 0) $sin_registrar++; } }
        return array('total' => $total, 'con' => $con, 'sin' => $total - $con, 'sin_supervisor' => $sin_supervisor, 'operarios' => $operarios,
            'porcentaje' => $total ? (int) round($con * 100 / $total) : 0, 'supervisores' => $asignados, 'supervisores_sin_registrar' => $sin_registrar);
    }
}
