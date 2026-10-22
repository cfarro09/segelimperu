<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Almacen extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id_usu')) { redirect('login'); exit; }
        require_once APPPATH.'libraries/Almacen_acceso.php';
        if (!Almacen_acceso::permite($this->session->userdata('tipo_usu'), 'almacen', $this->router->fetch_method())) {
            show_error('Tu rol no tiene permiso para este módulo o acción.', 403); exit;
        }
        $this->load->library('layout');
        $this->load->model('Almacen_model');
        $this->load->model('General_model');
        if (!$this->session->userdata('almacen_token')) $this->session->set_userdata('almacen_token', bin2hex(random_bytes(32)));
        $this->db->db_debug = false;
        if (!$this->db->table_exists('almacen_productos') || !$this->db->table_exists('almacen_movimientos')) {
            show_error('El módulo de almacén aún no ha sido instalado.', 503); exit;
        }
    }

    public function index()
    {
        $rol = $this->session->userdata('tipo_usu');
        redirect(Almacen_acceso::es_operativo($rol) ? Almacen_acceso::inicio($rol) : 'almacen/productos');
    }

    private function pagina($vista, $data)
    {
        $rol = $this->session->userdata('tipo_usu');
        $data['menu_tables'] = Almacen_acceso::es_operativo($rol) ? array() : $this->General_model->get_tables_active();
        $data['puede_editar_asignacion'] = Almacen_acceso::permite($rol, 'almacen', 'guardar_asignacion');
        $data['token'] = $this->session->userdata('almacen_token');
        $this->layout->view('almacen/'.$vista, $data);
    }

    private function validar_post()
    {
        if ($this->input->method() !== 'post') { show_error('Método no permitido.', 405); exit; }
        $token = $this->input->post('token');
        if (!is_string($token) || !hash_equals($this->session->userdata('almacen_token'), $token)) {
            show_error('La sesión del formulario no es válida. Vuelve a abrirlo.', 403); exit;
        }
    }

    public function productos()
    {
        $buscar = $this->input->get('buscar');
        $buscar = is_string($buscar) ? trim($buscar) : '';
        $this->pagina('productos', array(
            'productos' => $this->Almacen_model->productos('', ''),
            'resumen_stock' => $this->Almacen_model->resumen_stock(),
            'buscar' => $buscar
        ));
    }

    public function producto($id = 0)
    {
        $p = $id ? $this->Almacen_model->producto((int) $id) : null;
        if ($id && !$p) { show_404(); return; }
        if($this->input->is_ajax_request()) { $this->formulario_modal('producto',$p); return; }
        $this->pagina('producto', array('producto' => $p, 'errores' => array()));
    }

    private function decimal($valor, $digitos)
    {
        return is_string($valor) && preg_match('/^\d{1,9}(?:\.\d{1,'.$digitos.'})?$/D', $valor);
    }

    public function guardar()
    {
        $this->validar_post();
        $id = $this->input->post('id');
        if (!is_string($id) || !ctype_digit($id)) { show_error('Producto inválido.', 400); return; }
        $id = (int) $id;
        if ($id && !$this->Almacen_model->producto($id)) { show_404(); return; }
        $data = array(); $errores = array();
        foreach (array('codigo' => 40, 'descripcion' => 255, 'categoria' => 80, 'marca' => 100, 'unidad' => 30) as $campo => $max) {
            $v = $this->input->post($campo);
            $data[$campo] = is_string($v) ? trim($v) : '';
            if (($campo !== 'marca' && $data[$campo] === '') || mb_strlen($data[$campo]) > $max) $errores[] = 'Revisa el campo '.$campo.'.';
        }
        foreach (array('precio_real', 'precio_valorado') as $campo) {
            $v = $this->input->post($campo);
            if ($campo === 'precio_real' && $v === '') $data[$campo] = null;
            elseif (!$this->decimal($v, 2)) { $errores[] = 'El '.$campo.' debe ser un monto positivo o cero, con hasta 2 decimales.'; $data[$campo] = $v; }
            else $data[$campo] = $v;
        }
        if (!isset(Almacen_model::CATEGORIAS[$data['categoria']])) $errores[] = 'Elige la categoría: Materiales, Implementos o Venenos.';
        // Vencimiento opcional en mes/año (se guarda el último día del mes).
        $vence = $this->input->post('vencimiento'); $data['vencimiento'] = null;
        if (is_string($vence) && $vence !== '') {
            $mes = DateTime::createFromFormat('!Y-m', $vence);
            if ($mes && $mes->format('Y-m') === $vence) $data['vencimiento'] = $mes->format('Y-m-t'); else $errores[] = 'Revisa la fecha de vencimiento.';
        }
        $data['activo'] = $this->input->post('activo') === '1' ? 1 : 0;
        $stock = $id ? null : $this->input->post('stock');
        if (!$id && !$this->decimal($stock, 3)) $errores[] = 'El stock inicial debe ser positivo o cero, con hasta 3 decimales.';
        if (!$errores) {
            $resultado = $this->Almacen_model->guardar($id, $data, $stock, (int) $this->session->userdata('id_usu'));
            if ($resultado === true) {
                if($this->input->is_ajax_request()) {
                    $p=$id?$this->Almacen_model->producto($id):$this->Almacen_model->producto_por_codigo($data['codigo']);
                    $this->respuesta_json(array('ok'=>true,'id'=>(int)$p->id,'fila'=>$this->load->view('almacen/producto_fila',array('p'=>$p),true),'mensaje'=>'Producto guardado.')); return;
                }
                $this->session->set_flashdata('almacen_ok', 'Producto guardado.'); redirect('almacen/productos'); return;
            }
            $errores[] = $resultado;
        }
        if($this->input->is_ajax_request()) { $this->respuesta_json(array('ok'=>false,'errores'=>$errores),422); return; }
        $data['id'] = $id; $data['stock'] = $stock;
        $this->pagina('producto', array('producto' => (object) $data, 'errores' => $errores));
    }

    public function inventario($id)
    {
        $p = $this->Almacen_model->producto((int) $id);
        if (!$p) { show_404(); return; }
        $vista=$this->input->get('vista');
        $vista=in_array($vista,array('movimiento','historial'),true)?$vista:'completo';
        if($this->input->is_ajax_request()) { $this->formulario_modal('inventario',$p,$vista); return; }
        $this->pagina('inventario', array('producto' => $p, 'movimientos' => $vista==='movimiento'?array():$this->Almacen_model->movimientos($p->id),'vista_inventario'=>$vista));
    }

    public function movimiento()
    {
        $this->validar_post();
        $id = $this->input->post('id');
        if (!is_string($id) || !ctype_digit($id) || !(int) $id) { show_error('Producto inválido.', 400); return; }
        $tipo = $this->input->post('tipo'); $cantidad = $this->input->post('cantidad'); $motivo = $this->input->post('motivo');
        $proveedor = $this->input->post('proveedor'); $factura = $this->input->post('factura');
        $proveedor = is_string($proveedor) ? trim($proveedor) : ''; $factura = is_string($factura) ? trim($factura) : '';
        $motivo = is_string($motivo) ? trim($motivo) : null;
        // El conteo físico de un producto sin stock (sin conteo previo o en 0) no exige motivo.
        $p = $this->Almacen_model->producto((int) $id);
        $sin_stock = $p && (!$p->stock_confirmado || (float) $p->stock == 0);
        if ($tipo === 'ajuste' && $sin_stock && $motivo === '') $motivo = 'Conteo físico';
        if (!in_array($tipo, array('entrada', 'salida', 'ajuste'), true) || !$this->decimal($cantidad, 3) || $motivo === null || $motivo === '' || mb_strlen($motivo) > 255) {
            $resultado = 'Revisa el tipo, la cantidad y el motivo del movimiento.';
        } elseif (mb_strlen($proveedor) > 200 || mb_strlen($factura) > 40) {
            $resultado = 'Revisa el proveedor (hasta 200 caracteres) y el número de factura (hasta 40).';
        } else $resultado = $this->Almacen_model->mover((int) $id, $tipo, $cantidad, $motivo, (int) $this->session->userdata('id_usu'), $proveedor, $factura);
        if($this->input->is_ajax_request()) {
            if($resultado!==true) { $this->respuesta_json(array('ok'=>false,'errores'=>array($resultado)),422); return; }
            $p=$this->Almacen_model->producto((int)$id);
            $this->respuesta_json(array('ok'=>true,'id'=>(int)$p->id,'fila'=>$this->load->view('almacen/producto_fila',array('p'=>$p),true),'formulario'=>$this->load->view('almacen/inventario_formulario',array('producto'=>$p,'movimientos'=>array(),'token'=>$this->session->userdata('almacen_token'),'en_modal'=>true,'vista_inventario'=>'movimiento'),true),'mensaje'=>'Movimiento registrado.')); return;
        }
        $this->session->set_flashdata($resultado === true ? 'almacen_ok' : 'almacen_error', $resultado === true ? 'Movimiento registrado.' : $resultado);
        redirect('almacen/inventario/'.(int) $id);
    }

    private function cargar_clientes()
    {
        $this->load->model('Almacen_clientes_model');
        $this->load->model('Almacen_asignaciones_model');
    }

    /** Id del supervisor en sesión; null para roles que ven todos los clientes. */
    private function supervisor_actual()
    {
        return $this->session->userdata('tipo_usu') === 'SP' ? (int) $this->session->userdata('id_usu') : null;
    }

    private function cliente_visible($cliente)
    {
        $supervisor = $this->supervisor_actual();
        return $cliente && ($supervisor === null || (int) $cliente->supervisor_id === $supervisor);
    }

    public function clientes()
    {
        $this->cargar_clientes();
        $buscar = $this->input->get('buscar');
        $buscar = is_string($buscar) ? trim($buscar) : '';
        $data = array();
        $data['clientes'] = $this->Almacen_clientes_model->listar('', '');
        $data['buscar'] = $buscar;
        $this->pagina('clientes', $data);
    }

    public function cliente($id = 0)
    {
        $this->cargar_clientes();
        $cliente = $id ? $this->Almacen_clientes_model->obtener((int) $id) : null;
        if ($id && !$cliente) { show_404(); return; }
        if($this->input->is_ajax_request()) { $this->formulario_modal('cliente',$cliente); return; }
        $this->pagina('cliente', array('cliente' => $cliente, 'errores' => array(), 'supervisores' => $this->Almacen_clientes_model->supervisores(), 'puede_editar_presupuesto' => $this->puede_editar_presupuesto()));
    }

    public function guardar_cliente()
    {
        $this->validar_post(); $this->cargar_clientes();
        $id = $this->input->post('id');
        if (!is_string($id) || !ctype_digit($id)) { show_error('Cliente inválido.', 400); return; }
        $id = (int) $id;
        if ($id && !$this->Almacen_clientes_model->obtener($id)) { show_404(); return; }
        $data = array(); $errores = array();
        $etiquetas = array('codigo'=>'código', 'nombre'=>'nombre', 'ruc'=>'RUC', 'grupo'=>'unidad asignada', 'contacto'=>'persona de contacto', 'telefono'=>'teléfono', 'correo'=>'correo', 'direccion'=>'dirección', 'modalidad'=>'modalidad', 'observaciones_servicio'=>'observaciones del servicio', 'documento_baja'=>'documento de baja');
        foreach (array('codigo'=>40, 'nombre'=>255, 'ruc'=>11, 'grupo'=>80, 'contacto'=>150, 'telefono'=>30, 'correo'=>150, 'direccion'=>255, 'modalidad'=>40, 'observaciones_servicio'=>255, 'documento_baja'=>255) as $campo=>$max) {
            $v = $this->input->post($campo); $data[$campo] = is_string($v) ? trim($v) : '';
            if (mb_strlen($data[$campo]) > $max || ($campo === 'nombre' && $data[$campo] === '')) $errores[] = 'Revisa el campo '.$etiquetas[$campo].'.';
        }
        if ($data['ruc'] !== '' && !preg_match('/^\d{11}$/D', $data['ruc'])) $errores[] = 'El RUC debe tener 11 dígitos.';
        if ($data['telefono'] !== '' && !preg_match('/^[0-9+() -]{6,30}$/D', $data['telefono'])) $errores[] = 'El teléfono solo admite números, espacios, +, guiones y paréntesis.';
        if ($data['correo'] !== '' && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) $errores[] = 'Revisa el correo electrónico.';
        if ($data['modalidad'] !== '' && !isset(Almacen_clientes_model::MODALIDADES[$data['modalidad']])) $errores[] = 'Elige una modalidad de la lista.';
        $data['con_material'] = $this->input->post('con_material') === '0' ? 0 : 1;
        list($horarios, $errores_horario) = $this->leer_horarios($this->input->post('horarios'));
        $errores = array_merge($errores, $errores_horario);
        $supervisor = $this->input->post('supervisor_id');
        $data['supervisor_id'] = null;
        if (is_string($supervisor) && $supervisor !== '') {
            if (ctype_digit($supervisor) && strlen($supervisor) <= 9 && $this->Almacen_clientes_model->es_supervisor((int) $supervisor)) $data['supervisor_id'] = (int) $supervisor;
            else $errores[] = 'Elige un supervisor válido.';
        }
        $data['presupuesto'] = $this->input->post('presupuesto');
        $data['saldo_inicial'] = $this->input->post('saldo_inicial');
        if (!$this->puede_editar_presupuesto()) {
            // Comercial edita solo los datos del cliente: presupuesto, saldo y estado se conservan.
            $actual = $id ? $this->Almacen_clientes_model->obtener($id) : null;
            $data['presupuesto'] = $actual ? $actual->presupuesto : '0';
            $data['saldo_inicial'] = $actual ? $actual->saldo_inicial : '0';
            $data['documento_baja'] = $actual ? $actual->documento_baja : '';
            $_POST['activo'] = !$actual || $actual->activo ? '1' : '0';
        }
        if (!$this->decimal($data['presupuesto'], 2)) { $errores[] = 'Revisa el presupuesto máximo.'; $data['presupuesto'] = ''; }
        if (!is_string($data['saldo_inicial']) || !preg_match('/^-?\d{1,9}(?:\.\d{1,2})?$/D', $data['saldo_inicial'])) { $errores[] = 'Revisa el saldo inicial.'; $data['saldo_inicial'] = ''; }
        $data['activo'] = $this->input->post('activo') === '1' ? 1 : 0;
        if (!$errores) {
            $resultado = $this->Almacen_clientes_model->guardar($id, $data, $horarios);
            if ($resultado === true) {
                if($this->input->is_ajax_request()) {
                    $c=$this->Almacen_clientes_model->obtener($this->Almacen_clientes_model->ultimo_id);
                    $this->respuesta_json(array('ok'=>true,'id'=>(int)$c->id,'fila'=>$this->load->view('almacen/cliente_fila',array('c'=>$c),true),'mensaje'=>'Cliente guardado.')); return;
                }
                $this->session->set_flashdata('almacen_ok', 'Cliente guardado.'); redirect('almacen/clientes'); return;
            }
            $errores[] = $resultado;
        }
        if($this->input->is_ajax_request()) { $this->respuesta_json(array('ok'=>false,'errores'=>$errores),422); return; }
        $data['id'] = $id; $data['horarios'] = array_map(function ($h) { return (object) $h; }, $horarios); $this->pagina('cliente', array('cliente' => (object) $data, 'errores' => $errores, 'supervisores' => $this->Almacen_clientes_model->supervisores(), 'puede_editar_presupuesto' => $this->puede_editar_presupuesto()));
    }

    // Filas de horario del formulario: días marcados + hora de entrada y salida (HH:MM). Las filas vacías se ignoran.
    private function leer_horarios($entrada)
    {
        $horarios = array(); $errores = array();
        if (!is_array($entrada)) return array($horarios, $errores);
        if (count($entrada) > 14) return array(array(), array('Se admiten hasta 14 horarios por cliente.'));
        $n = 0;
        foreach ($entrada as $fila) {
            if (!is_array($fila)) continue;
            $dias = isset($fila['dias']) && is_array($fila['dias']) ? array_values(array_intersect(array_keys(Almacen_clientes_model::DIAS), $fila['dias'])) : array();
            $e = isset($fila['entrada']) && is_string($fila['entrada']) ? $fila['entrada'] : '';
            $s = isset($fila['salida']) && is_string($fila['salida']) ? $fila['salida'] : '';
            $detalle = isset($fila['detalle']) && is_string($fila['detalle']) ? trim($fila['detalle']) : '';
            if (!$dias && $e === '' && $s === '' && $detalle === '') continue;
            $n++;
            $hora = '/^([01]\d|2[0-3]):[0-5]\d$/D';
            if (!$dias) $errores[] = 'Horario '.$n.': marca al menos un día.';
            if (!preg_match($hora, $e) || !preg_match($hora, $s)) $errores[] = 'Horario '.$n.': indica la hora de entrada y de salida.';
            elseif ($e === $s) $errores[] = 'Horario '.$n.': la entrada y la salida no pueden ser iguales.';
            if (mb_strlen($detalle) > 80) $errores[] = 'Horario '.$n.': el detalle admite hasta 80 caracteres.';
            $horarios[] = array('dias' => $dias, 'entrada' => $e, 'salida' => $s, 'detalle' => $detalle);
        }
        return array($horarios, $errores);
    }

    private function formulario_modal($tipo,$registro,$vista_inventario='completo')
    {
        $this->output->set_header('Cache-Control: private, no-store');
        $datos=array($tipo==='inventario'?'producto':$tipo=>$registro,'errores'=>array(),'token'=>$this->session->userdata('almacen_token'),'en_modal'=>true);
        if($tipo==='cliente') { $datos['supervisores']=$this->Almacen_clientes_model->supervisores(); $datos['puede_editar_presupuesto']=$this->puede_editar_presupuesto(); }
        if($tipo==='pendiente') $datos=array_merge($datos,$this->datos_pendiente());
        if($tipo==='inventario') { $datos['movimientos']=$vista_inventario==='movimiento'?array():$this->Almacen_model->movimientos($registro->id); $datos['vista_inventario']=$vista_inventario; }
        $this->load->view('almacen/'.$tipo.'_formulario',$datos);
    }

    private function respuesta_json($data,$estado=200)
    {
        $this->output->set_status_header($estado)->set_content_type('application/json')
            ->set_header('Cache-Control: private, no-store')->set_output(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE));
    }

    private function periodo_valido($periodo)
    {
        return is_string($periodo) && preg_match('/^(?:19|20|21)\d{2}-(?:0[1-9]|1[0-2])$/D', $periodo);
    }

    public function asignaciones()
    {
        $this->cargar_clientes();
        $cliente_id = $this->input->get('cliente_id');
        $cliente_id = is_string($cliente_id) && ctype_digit($cliente_id) ? (int) $cliente_id : 0;
        if ($this->input->is_ajax_request()) { $this->consultar_asignaciones_cliente($cliente_id); return; }
        $periodo = date('Y-m');
        $data = array();
        $data['asignaciones'] = $this->Almacen_asignaciones_model->listar($cliente_id, $periodo, null, 0, '', $this->supervisor_actual());
        foreach ($data['asignaciones'] as $a) $a->saldo_acumulado = $this->Almacen_asignaciones_model->saldo_anterior($a->cliente_id, $a->periodo) + $a->presupuesto - $a->total;
        $data['clientes'] = $this->Almacen_clientes_model->listar('', '', null, 0, $this->supervisor_actual());
        $data['cliente_id'] = $cliente_id; $data['periodo'] = $periodo;
        $this->pagina('asignaciones', $data);
    }

    private function consultar_asignaciones_cliente($cliente_id)
    {
        $cliente = $cliente_id ? $this->Almacen_clientes_model->obtener($cliente_id) : null;
        if (!$this->cliente_visible($cliente)) { show_404(); return; }
        $id = $this->input->get('asignacion_id');
        $datos = array('cliente' => $cliente, 'asignacion' => null, 'detalles' => array(), 'saldo_anterior' => 0);
        if ($id !== null) {
            if (!is_string($id) || !ctype_digit($id) || strlen($id) > 9 || (int)$id < 1) { show_error('Asignación inválida.', 400); return; }
            $asignacion = $this->Almacen_asignaciones_model->obtener((int)$id);
            if (!$asignacion || (int)$asignacion->cliente_id !== $cliente_id) { show_404(); return; }
        } else {
            $datos['asignaciones'] = $this->Almacen_asignaciones_model->listar($cliente_id, '');
            $asignacion = $datos['asignaciones'] ? $datos['asignaciones'][0] : null;
        }
        if ($asignacion) {
            $datos['asignacion'] = $asignacion;
            $datos['detalles'] = $this->Almacen_asignaciones_model->detalles($asignacion->id);
            $datos['saldo_anterior'] = $this->Almacen_asignaciones_model->saldo_anterior($cliente_id, $asignacion->periodo);
        }
        $this->output->set_content_type('text/html')->set_header('Cache-Control: private, no-store');
        $this->load->view('almacen/'.($id === null ? 'cliente_asignaciones' : 'asignacion_consulta'), $datos);
    }

    public function revision_asignaciones()
    {
        $this->cargar_clientes();
        $cliente_id=$this->input->get('cliente_id'); $cliente_id=is_string($cliente_id)&&ctype_digit($cliente_id)?(int)$cliente_id:0;
        $periodo=$this->input->get('periodo'); $periodo=$this->periodo_valido($periodo)?$periodo:date('Y-m');
        $estado=$this->input->get('estado'); $estado=in_array($estado,array('revision','publicado','aprobado','rechazado'),true)?$estado:'revision';
        $this->load->model('Almacen_compras_model');
        $this->load->model('Almacen_despachos_model');
        $data=array();
        $data['asignaciones']=$this->Almacen_asignaciones_model->listar($cliente_id,$periodo,null,0,$estado);
        $despachos=$this->Almacen_despachos_model->del_mes($periodo);
        foreach($data['asignaciones'] as $a) {
            $a->saldo_acumulado=$this->Almacen_asignaciones_model->saldo_anterior($a->cliente_id,$a->periodo)+$a->presupuesto-$a->total;
            $a->despacho=$despachos[$a->id] ?? null;
        }
        $data['clientes']=$this->Almacen_clientes_model->listar('',''); $data['cliente_id']=$cliente_id; $data['periodo']=$periodo; $data['estado']=$estado;
        // Órdenes del mes: se muestra la pedida, si no la abierta (borrador), si no la última.
        $data['compras']=$this->Almacen_compras_model->del_mes($periodo);
        $compra_id=$this->input->get('compra_id'); $compra_id=is_string($compra_id)&&ctype_digit($compra_id)?(int)$compra_id:0;
        $data['compra']=null;
        foreach($data['compras'] as $o) if((int)$o->id===$compra_id) $data['compra']=$o;
        if(!$data['compra']) foreach($data['compras'] as $o) if($o->estado==='borrador') $data['compra']=$o;
        if(!$data['compra'] && $data['compras']) $data['compra']=end($data['compras']);
        $data['nuevas_compra']=$this->Almacen_compras_model->nuevas($periodo);
        $data['datos_compra']=$data['compra']?$this->datos_compra($data['compra']):null;
        $data['vista']=$this->input->get('vista')==='compra'?'compra':'asignaciones';
        $data['publicados']=$this->Almacen_asignaciones_model->total(0,$periodo,'publicado');
        $data['aprobados']=$this->Almacen_asignaciones_model->total(0,$periodo,'aprobado');
        $data['pendientes_despacho']=$this->Almacen_despachos_model->pendientes($periodo);
        $this->pagina('revision_asignaciones',$data);
    }

    public function revisar_asignacion($id)
    {
        $this->cargar_clientes(); $a=$this->Almacen_asignaciones_model->obtener((int)$id);
        if(!$a || $a->estado==='borrador') { show_404(); return; }
        $this->formulario_revision_asignacion($a);
    }

    private function formulario_revision_asignacion($a, $errores=array(), $cantidades=null, $revision=null)
    {
        $this->load->model('Almacen_despachos_model');
        $restriccion=$this->Almacen_asignaciones_model->restriccion_ajuste($a);
        $permiso=Almacen_acceso::permite($this->session->userdata('tipo_usu'),'almacen','guardar_revision_asignacion');
        $this->output->set_header('Cache-Control: private, no-store');
        $this->pagina('revision_asignacion',array('asignacion'=>$a,'despacho'=>$this->Almacen_despachos_model->de_asignacion($a->id),'cliente'=>$this->Almacen_clientes_model->obtener($a->cliente_id),'detalles'=>$this->Almacen_asignaciones_model->detalles($a->id),'saldo_anterior'=>$this->Almacen_asignaciones_model->saldo_anterior($a->cliente_id,$a->periodo),'eventos'=>$this->Almacen_asignaciones_model->eventos($a->id),
            'puede_ajustar_cantidades'=>$permiso && $restriccion==='', 'restriccion_ajuste'=>$restriccion,'errores'=>$errores,'cantidades_revision'=>$cantidades,'revision_formulario'=>$revision===null?$a->revision:$revision));
    }

    public function guardar_revision_asignacion()
    {
        $this->validar_post(); $this->cargar_clientes();
        $id=$this->input->post('id'); $revision=$this->input->post('revision');
        foreach(array($id,$revision) as $v) if(!is_string($v)||!ctype_digit($v)||strlen($v)>9||(int)$v<1) { show_error('Asignación inválida.',400); return; }
        $a=$this->Almacen_asignaciones_model->obtener((int)$id);
        if(!$a || $a->estado==='borrador') { show_404(); return; }
        $cantidades=$this->input->post('cantidades'); $errores=array();
        if(!is_array($cantidades)) { $cantidades=array(); $errores[]='No se recibieron las cantidades de la asignación.'; }
        foreach($cantidades as $pid=>$valor) {
            $cantidades[$pid]=is_string($valor)?str_replace(',', '.', trim($valor)):null;
            if($cantidades[$pid]==='') $cantidades[$pid]='0';
            if(!$this->decimal($cantidades[$pid],3)) $errores[]='Revisa las cantidades: usa valores positivos o cero, con hasta 3 decimales.';
        }
        if(!$errores) {
            $r=$this->Almacen_asignaciones_model->ajustar_cantidades((int)$id,(int)$revision,$cantidades,(int)$this->session->userdata('id_usu'));
            if(isset($r['id'])) {
                $this->session->set_flashdata('almacen_ok',$r['cambios']?'Cantidades guardadas correctamente.':'Las cantidades ya estaban guardadas.');
                redirect('almacen/revisar_asignacion/'.(int)$id); return;
            }
            $errores[]=$r['error'];
        }
        $this->output->set_status_header(422);
        $this->formulario_revision_asignacion($this->Almacen_asignaciones_model->obtener((int)$id),array_unique($errores),$cantidades,(int)$revision);
    }

    public function despacho($id)
    {
        $this->load->model('Almacen_despachos_model');
        $datos=$this->Almacen_despachos_model->preparar((int)$id);
        if (!$datos) { show_404(); return; }
        $datos['token']=$this->session->userdata('almacen_token');
        $datos['en_modal']=$this->input->is_ajax_request();
        $this->output->set_header('Cache-Control: private, no-store');
        if ($datos['en_modal']) $this->load->view('almacen/despacho_formulario',$datos);
        else $this->pagina('despacho',$datos);
    }

    public function confirmar_despacho()
    {
        $this->validar_post(); $this->cargar_clientes(); $this->load->model('Almacen_despachos_model');
        $id=$this->input->post('id'); $revision=$this->input->post('revision');
        foreach (array($id,$revision) as $v) if (!is_string($v) || !ctype_digit($v) || strlen($v)>9 || (int)$v<1) { show_error('Asignación inválida.',400); return; }
        $r=$this->Almacen_despachos_model->despachar((int)$id,(int)$revision,(int)$this->session->userdata('id_usu'));
        if (!isset($r['id'])) {
            if ($this->input->is_ajax_request()) $this->respuesta_json(array('ok'=>false,'errores'=>$r['errores']),422);
            else { $this->session->set_flashdata('almacen_error',implode(' ',$r['errores'])); redirect('almacen/despacho/'.(int)$id); }
            return;
        }
        $mensaje=$r['existente']?'Esta asignación ya fue despachada.':'Despacho registrado. El stock fue actualizado.';
        if (!$this->input->is_ajax_request()) { redirect('almacen/guia_salida/'.(int)$r['id']); return; }
        $s=$this->Almacen_despachos_model->obtener($r['id']);
        $a=$this->Almacen_asignaciones_model->obtener((int)$id);
        $cliente=$this->Almacen_clientes_model->obtener($a->cliente_id);
        $a->nombre=$cliente->nombre; $a->despacho=$s;
        $a->saldo_acumulado=$this->Almacen_asignaciones_model->saldo_anterior($a->cliente_id,$a->periodo)+$a->presupuesto-$a->total;
        $this->respuesta_json(array('ok'=>true,'id'=>(int)$id,'fila'=>$this->load->view('almacen/revision_asignacion_fila',array('a'=>$a),true),
            'acciones'=>$this->load->view('almacen/despacho_acciones',array('asignacion'=>$a,'despacho'=>$s),true),
            'pendientes_despacho'=>$this->Almacen_despachos_model->pendientes($a->periodo),
            'nuevas_compra'=>$this->nuevas_compra($a->periodo),
            'guia_url'=>site_url('almacen/guia_salida/'.$s->id),'mensaje'=>$mensaje));
    }

    public function guia_salida($id)
    {
        $this->load->model('Almacen_despachos_model');
        $s=$this->Almacen_despachos_model->obtener((int)$id);
        if (!$s) { show_404(); return; }
        $this->load->library('Almacen_guia_pdf');
        $pdf=$this->almacen_guia_pdf->generar($s,$this->Almacen_despachos_model->detalles($s->id));
        $this->output->set_content_type('application/pdf')->set_header('Cache-Control: private, no-store')
            ->set_header('Content-Disposition: inline; filename="'.Almacen_despachos_model::numero($s).'.pdf"')->set_output($pdf);
    }

    public function decidir_asignacion()
    {
        $this->validar_post(); $this->cargar_clientes();
        $id=$this->input->post('id'); $revision=$this->input->post('revision');
        foreach(array($id,$revision) as $v) if(!is_string($v)||!ctype_digit($v)||strlen($v)>9) { show_error('Asignación inválida.',400);return; }
        $resultado=$this->Almacen_asignaciones_model->revisar((int)$id,(int)$revision,$this->input->post('decision'),$this->input->post('comentario'),(int)$this->session->userdata('id_usu'));
        $this->session->set_flashdata($resultado===true?'almacen_ok':'almacen_error',$resultado===true?'Revisión registrada.':$resultado);
        redirect('almacen/revisar_asignacion/'.(int)$id);
    }

    public function ordenes_compra()
    {
        $periodo=$this->input->get('periodo');$periodo=$this->periodo_valido($periodo)?$periodo:date('Y-m');
        redirect($this->ruta_compra($periodo));
    }
    public function consolidar_compras()
    {
        $this->validar_post();$this->load->model('Almacen_compras_model');$periodo=$this->input->post('periodo');
        if(!$this->periodo_valido($periodo)){show_error('Período inválido.',400);return;}
        $r=$this->Almacen_compras_model->consolidar($periodo,(int)$this->session->userdata('id_usu'));
        if(isset($r['id'])){$o=$this->Almacen_compras_model->obtener($r['id']);$this->session->set_flashdata('almacen_ok','Orden '.Almacen_compras_model::codigo($o).(empty($r['nueva'])?' actualizada con las asignaciones aprobadas.':' generada con las asignaciones aprobadas nuevas.'));redirect($this->ruta_compra($periodo,$r['id']));return;}
        $this->session->set_flashdata('almacen_error',$r['error']);redirect($this->ruta_compra($periodo));
    }
    public function orden_compra($id)
    {
        $this->load->model('Almacen_compras_model');$o=$this->Almacen_compras_model->obtener((int)$id);
        if(!$o){show_404();return;}
        redirect($this->ruta_compra($o->periodo,$o->id));
    }
    private function puede_editar_presupuesto() { return $this->session->userdata('tipo_usu') !== 'CM'; }
    private function puede_aprobar_compra() { return in_array($this->session->userdata('tipo_usu'),array('AD','SA'),true); }
    private function nuevas_compra($periodo) { $this->load->model('Almacen_compras_model'); return $this->Almacen_compras_model->nuevas($periodo); }
    private function ruta_compra($periodo,$compra_id=0)
    {
        return 'almacen/revision_asignaciones?periodo='.rawurlencode($periodo).'&vista=compra'.($compra_id?'&compra_id='.(int)$compra_id:'');
    }
    private function datos_compra($compra)
    {
        return array('compra'=>$compra,'puede_aprobar_compra'=>$this->puede_aprobar_compra(),'aprobador'=>$this->Almacen_compras_model->nombre_usuario($compra->aprobado_por ?? 0),'nuevas_compra'=>$this->Almacen_compras_model->nuevas($compra->periodo),'detalles'=>$this->Almacen_compras_model->detalles($compra->id),'asignaciones'=>$this->Almacen_compras_model->asignaciones($compra->id),'cantidades_clientes'=>$this->Almacen_compras_model->cantidades_por_cliente($compra->id));
    }
    public function guardar_compra()
    {
        $this->validar_post();$this->load->model('Almacen_compras_model');
        $id=$this->input->post('id');$revision=$this->input->post('revision');$accion=$this->input->post('accion');
        foreach(array($id,$revision) as $v)if(!is_string($v)||!ctype_digit($v)||strlen($v)>9){show_error('Orden inválida.',400);return;}
        if(!in_array($accion,array('guardar','aprobar','recibir','reabrir'),true)){show_error('Acción inválida.',400);return;}
        // Solo Administración aprueba o reabre una orden; Almacén confirma la recepción.
        if(in_array($accion,array('aprobar','reabrir'),true) && !$this->puede_aprobar_compra()){show_error('Solo Administración puede aprobar o reabrir una orden de compra.',403);return;}
        $cantidades=$this->input->post('cantidades');$proveedor=$this->input->post('proveedor');$observaciones=$this->input->post('observaciones');
        $sin_stock_previo=$this->input->post('sin_stock_previo'); if(!is_array($sin_stock_previo)) $sin_stock_previo=array();
        if(!is_array($cantidades)||!is_string($proveedor)||mb_strlen($proveedor)>200||!is_string($observaciones)||mb_strlen($observaciones)>1000)$r='Revisa las cantidades, proveedor y observaciones.';
        else $r=$this->Almacen_compras_model->guardar((int)$id,(int)$revision,$cantidades,trim($proveedor),trim($observaciones),(int)$this->session->userdata('id_usu'),$accion,$sin_stock_previo);
        $mensajes=array('guardar'=>'Cantidades de compra guardadas.','aprobar'=>'Orden aprobada. Almacén ya puede confirmar la recepción.','recibir'=>'Recepción registrada. El stock fue actualizado.','reabrir'=>'Orden reabierta como borrador.');
        $this->session->set_flashdata($r===true?'almacen_ok':'almacen_error',$r===true?$mensajes[$accion]:$r);
        $compra=$this->Almacen_compras_model->obtener((int)$id);
        redirect($this->ruta_compra($compra?$compra->periodo:date('Y-m'),$compra?$compra->id:0));
    }
    public function imprimir_compra($id)
    {
        $this->load->model('Almacen_compras_model');$o=$this->Almacen_compras_model->obtener((int)$id);if(!$o){show_404();return;}
        $datos=array('compra'=>$o,'detalles'=>$this->Almacen_compras_model->detalles($o->id),'vista_clientes'=>$this->input->get('vista')==='clientes');
        if($datos['vista_clientes']) {
            $datos['asignaciones']=$this->Almacen_compras_model->asignaciones($o->id);
            $datos['cantidades_clientes']=$this->Almacen_compras_model->cantidades_por_cliente($o->id);
        }
        $this->load->view('almacen/compra_impresion',$datos);
    }

    private function formulario_asignacion($asignacion, $cantidades = null, $errores = array(), $errores_cantidades = array(), $volver_asignaciones = false)
    {
        $cliente = $asignacion ? $this->Almacen_clientes_model->obtener($asignacion->cliente_id) : null;
        $clientes=$this->Almacen_clientes_model->listar('', '1', null, 0, $this->supervisor_actual());
        foreach($clientes as $c) {
            $mes=$this->Almacen_asignaciones_model->por_cliente_periodo($c->id,date('Y-m'));
            $c->estado_mes=$mes?$mes->estado:'sin_registrar';
        }
        $productos = array();
        // Los venenos (fumigación) no forman parte de la asignación mensual de materiales.
        foreach ($this->Almacen_model->productos('', '1') as $p) { if ($p->categoria === 'VENENOS') continue; $p->cantidad = 0; $productos[$p->id] = $p; }
        if ($asignacion && $asignacion->id) foreach ($this->Almacen_asignaciones_model->detalles($asignacion->id) as $d) {
            $p = clone $d; $p->id = $d->producto_id;
            $productos[$p->id] = $p;
        }
        if ($cantidades !== null) foreach ($productos as $p) $p->cantidad = isset($cantidades[$p->id]) ? $cantidades[$p->id] : 0;
        uasort($productos, function($a,$b) { $orden=array('MATERIALES'=>0,'IMPLEMENTOS'=>1); return (($orden[$a->categoria] ?? 2) - ($orden[$b->categoria] ?? 2)) ?: strcmp($a->categoria,$b->categoria) ?: strcmp($a->descripcion, $b->descripcion) ?: ($a->id - $b->id); });
        $this->pagina('asignacion', array('asignacion' => $asignacion, 'cliente' => $cliente,
            'clientes' => $clientes, 'productos' => $productos,
            'saldo_anterior' => $cliente ? $this->Almacen_asignaciones_model->saldo_anterior($cliente->id, $asignacion->periodo) : 0,
            'errores' => $errores, 'errores_cantidades' => $errores_cantidades, 'volver_asignaciones' => $volver_asignaciones));
    }

    public function asignacion($id = 0)
    {
        $this->cargar_clientes();
        $volver_asignaciones = $this->input->get('origen') === 'asignaciones';
        $origen = $volver_asignaciones ? 'origen=asignaciones' : '';
        if ($id) {
            $a = $this->Almacen_asignaciones_model->obtener((int) $id);
            if (!$a || !$this->cliente_visible($this->Almacen_clientes_model->obtener($a->cliente_id))) { show_404(); return; }
            if($a->periodo!==date('Y-m')) { redirect('almacen/asignacion?cliente_id='.(int)$a->cliente_id.($origen?'&'.$origen:''));return; }
        } else {
            $cliente_id = $this->input->get('cliente_id'); $periodo = date('Y-m');
            $cliente_id = is_string($cliente_id) && ctype_digit($cliente_id) ? (int) $cliente_id : 0;
            if (!$cliente_id) {
                $activos = $this->Almacen_clientes_model->listar('', '1', null, 0, $this->supervisor_actual());
                if ($activos) $cliente_id = (int) $activos[0]->id;
                foreach($activos as $candidato) {
                    if(!$this->Almacen_asignaciones_model->por_cliente_periodo($candidato->id,$periodo)) { $cliente_id=(int)$candidato->id;break; }
                }
            }
            $c = $cliente_id ? $this->Almacen_clientes_model->obtener($cliente_id) : null;
            if ($c && !$this->cliente_visible($c)) { show_404(); return; }
            $periodo = $this->periodo_valido($periodo) ? $periodo : date('Y-m');
            if ($c && $c->activo) {
                $existente = $this->Almacen_asignaciones_model->por_cliente_periodo($c->id, $periodo);
                if ($existente) { redirect('almacen/asignacion/'.$existente->id.($origen?'?'.$origen:'')); return; }
            }
            $a = $c && $c->activo ? (object) array('id' => 0, 'cliente_id' => $c->id, 'periodo' => $periodo, 'presupuesto' => $c->presupuesto, 'observaciones' => '', 'revision' => 0, 'estado'=>'borrador', 'comentario_revision'=>'') : null;
        }
        $this->formulario_asignacion($a, null, array(), array(), $volver_asignaciones);
    }

    public function guardar_asignacion()
    {
        $this->validar_post(); $this->cargar_clientes();
        $volver_asignaciones = $this->input->post('origen') === 'asignaciones';
        $id = $this->input->post('id'); $cliente_id = $this->input->post('cliente_id'); $revision = $this->input->post('revision');
        foreach (array($id, $cliente_id, $revision) as $v) if (!is_string($v) || !ctype_digit($v) || strlen($v) > 9) { show_error('Asignación inválida.', 400); return; }
        $cliente = $this->Almacen_clientes_model->obtener((int) $cliente_id);
        if ($cliente && !$this->cliente_visible($cliente)) { show_404(); return; }
        $periodo = $this->input->post('periodo');
        if (!$cliente || !$this->periodo_valido($periodo) || $periodo!==date('Y-m')) { show_error('Solo se pueden registrar asignaciones del mes actual.', 400); return; }
        $actual=(int)$id?$this->Almacen_asignaciones_model->obtener((int)$id):null;
        if((int)$id&&!$actual) { show_404();return; }
        $presupuesto = $actual?$actual->presupuesto:$cliente->presupuesto; $observaciones = $this->input->post('observaciones'); $cantidades = $this->input->post('cantidades');
        $accion=$this->input->post('accion');
        if(!in_array($accion,array('borrador','publicar'),true)) { show_error('Acción inválida.',400);return; }
        $errores = array(); $errores_cantidades = array();
        if (!$this->decimal($presupuesto,2)) $errores[] = 'Revisa el presupuesto del mes.';
        if (!is_string($observaciones) || mb_strlen($observaciones)>1000) { $errores[] = 'Las observaciones admiten hasta 1000 caracteres.'; $observaciones = ''; }
        if (!is_array($cantidades)) { $errores[] = 'No se recibieron los productos de la plantilla.'; $cantidades = array(); }
        foreach ($cantidades as $pid=>$cant) {
            if ($cant === '') $cantidades[$pid] = '0';
            else {
                $normalizada = is_string($cant) ? str_replace(',', '.', trim($cant)) : null;
                if (!$this->decimal($normalizada,3)) {
                    $p = ctype_digit((string)$pid) ? $this->Almacen_model->producto((int)$pid) : null;
                    $nombre = $p ? $p->descripcion.' · '.$p->unidad.' · '.$p->marca : 'Producto '.$pid;
                    $errores[] = $nombre.': ingresa una cantidad positiva o cero, con hasta 3 decimales.';
                    $errores_cantidades[$pid] = 'Ingresa una cantidad positiva o cero, con hasta 3 decimales. Ejemplo: 1,5.';
                    // Keep the entered text so the user can correct it, including on another page.
                    $cantidades[$pid] = is_string($cant) ? $cant : '';
                } else $cantidades[$pid] = $normalizada;
            }
        }
        if (!$errores) {
            $resultado = $this->Almacen_asignaciones_model->guardar((int)$id, (int)$revision, (int)$cliente_id, $periodo, $presupuesto, $cantidades, trim($observaciones), (int)$this->session->userdata('id_usu'), $accion==='publicar');
            if (isset($resultado['id'])) { $this->session->set_flashdata('almacen_ok', $accion==='publicar'?'Asignación enviada a administración. Está pendiente de revisión.':'Borrador guardado correctamente.'); redirect('almacen/asignacion/'.$resultado['id'].($volver_asignaciones?'?origen=asignaciones':'')); return; }
            $errores[] = $resultado['error'];
        }
        $a = (object) array('id'=>(int)$id, 'cliente_id'=>(int)$cliente_id, 'periodo'=>$periodo, 'presupuesto'=>$presupuesto, 'observaciones'=>$observaciones, 'revision'=>(int)$revision, 'estado'=>$actual?$actual->estado:'borrador','comentario_revision'=>$actual?$actual->comentario_revision:'');
        $this->formulario_asignacion($a, $cantidades, array_unique($errores), $errores_cantidades, $volver_asignaciones);
    }

    public function imprimir_asignacion($id)
    {
        $this->cargar_clientes(); $a = $this->Almacen_asignaciones_model->obtener((int)$id);
        if (!$a || !$this->cliente_visible($this->Almacen_clientes_model->obtener($a->cliente_id))) { show_404(); return; }
        $this->load->view('almacen/asignacion_impresion', array('asignacion'=>$a, 'cliente'=>$this->Almacen_clientes_model->obtener($a->cliente_id), 'detalles'=>$this->Almacen_asignaciones_model->detalles($a->id), 'saldo_anterior'=>$this->Almacen_asignaciones_model->saldo_anterior($a->cliente_id,$a->periodo)));
    }

    public function exportar_asignacion($id)
    {
        $this->cargar_clientes();
        $a=$this->Almacen_asignaciones_model->obtener((int)$id);
        if(!$a){show_404();return;}
        $c=$this->Almacen_clientes_model->obtener($a->cliente_id);
        if(!$this->cliente_visible($c)){show_404();return;}
        $this->load->library('Almacen_excel');
        $this->descargar_excel('asignacion-'.$a->id.'-'.$a->periodo.'.xlsx', $this->almacen_excel->asignacion($a,$c,$this->Almacen_asignaciones_model->detalles($a->id),$this->Almacen_asignaciones_model->saldo_anterior($a->cliente_id,$a->periodo)));
    }

    public function exportar_compra($id)
    {
        $this->load->model('Almacen_compras_model');
        $o=$this->Almacen_compras_model->obtener((int)$id);
        if(!$o){show_404();return;}
        $this->load->library('Almacen_excel');
        $clientes=$this->input->get('vista')==='clientes';
        $this->descargar_excel('orden-'.Almacen_compras_model::codigo($o).($clientes?'-por-cliente':'').'.xlsx', $this->almacen_excel->compra($o,$this->Almacen_compras_model->detalles($o->id),$this->Almacen_compras_model->asignaciones($o->id),$clientes?$this->Almacen_compras_model->cantidades_por_cliente($o->id):null));
    }

    // ---------------- Pendientes de almacén ----------------
    private function puede_gestionar_pendientes() { return in_array($this->session->userdata('tipo_usu'), array('AD','SA','AL'), true); }
    // Supervisor: solo ve y edita lo que registró.
    private function pendientes_propios() { return $this->puede_gestionar_pendientes() ? null : (int) $this->session->userdata('id_usu'); }
    private function pendiente_visible($p) { $propios = $this->pendientes_propios(); return $p && ($propios === null || (int) $p->creado_por === $propios); }
    private function datos_pendiente()
    {
        $this->load->model('Almacen_clientes_model');
        return array('clientes'=>$this->Almacen_clientes_model->listar('', '1'), 'solicitantes'=>$this->Almacen_pendientes_model->solicitantes(),
            'puede_gestionar'=>$this->puede_gestionar_pendientes(), 'nombre_usuario'=>trim((string) $this->session->userdata('nombre_usu')));
    }

    public function pendientes()
    {
        $this->load->model('Almacen_pendientes_model');
        $vista = $this->input->get('vista'); $vista = isset(Almacen_pendientes_model::VISTAS[$vista]) ? $vista : 'abiertos';
        $this->pagina('pendientes', array('pendientes'=>$this->Almacen_pendientes_model->listar($vista, $this->pendientes_propios()),
            'conteos'=>$this->Almacen_pendientes_model->conteos($this->pendientes_propios()), 'vista'=>$vista, 'puede_gestionar'=>$this->puede_gestionar_pendientes()));
    }

    public function pendiente($id = 0)
    {
        $this->load->model('Almacen_pendientes_model');
        $p = $id ? $this->Almacen_pendientes_model->obtener((int) $id) : null;
        if ($id && !$this->pendiente_visible($p)) { show_404(); return; }
        if ($this->input->is_ajax_request()) { $this->formulario_modal('pendiente', $p); return; }
        $this->pagina('pendiente', array_merge(array('pendiente'=>$p, 'errores'=>array()), $this->datos_pendiente()));
    }

    public function guardar_pendiente()
    {
        $this->validar_post(); $this->load->model('Almacen_pendientes_model');
        $id = $this->input->post('id');
        if (!is_string($id) || !ctype_digit($id)) { show_error('Pendiente inválido.', 400); return; }
        $id = (int) $id; $actual = $id ? $this->Almacen_pendientes_model->obtener($id) : null;
        if ($id && !$this->pendiente_visible($actual)) { show_404(); return; }
        $errores = array(); $data = array();
        foreach (array('fecha'=>10, 'tipo'=>20, 'sede'=>150, 'detalle'=>3000, 'solicitante'=>80, 'prioridad'=>10, 'estado'=>20, 'observaciones'=>500) as $campo=>$max) {
            $v = $this->input->post($campo); $data[$campo] = is_string($v) ? trim($v) : '';
            if (mb_strlen($data[$campo]) > $max) $errores[] = 'El campo '.$campo.' es demasiado largo.';
        }
        $f = DateTime::createFromFormat('!Y-m-d', $data['fecha']);
        if (!$f || $f->format('Y-m-d') !== $data['fecha'] || $data['fecha'] > date('Y-m-d')) $errores[] = 'Indica una fecha válida (no futura).';
        if (!isset(Almacen_pendientes_model::TIPOS[$data['tipo']])) $errores[] = 'Elige el tipo de pendiente.';
        if ($data['sede'] === '') $errores[] = 'Indica la sede o unidad.';
        if ($data['detalle'] === '') $errores[] = 'Describe el pendiente.';
        if (!in_array($data['prioridad'], array('normal','urgente'), true)) $data['prioridad'] = 'normal';
        // Solo Almacén/Administración cambian el estado; el supervisor registra y corrige su pedido.
        if (!$this->puede_gestionar_pendientes() || !isset(Almacen_pendientes_model::ESTADOS[$data['estado']])) $data['estado'] = $actual ? $actual->estado : 'pendiente';
        if ($actual && $actual->estado === 'atendido' && !$this->puede_gestionar_pendientes()) $errores[] = 'El pendiente ya fue atendido; no se puede modificar.';
        if ($data['estado'] === 'atendido' && (!$actual || $actual->estado !== 'atendido')) { $data['atendido_por'] = (int) $this->session->userdata('id_usu'); $data['atendido_en'] = date('Y-m-d H:i:s'); }
        if ($data['estado'] !== 'atendido') { $data['atendido_por'] = null; $data['atendido_en'] = null; }
        $data['cliente_id'] = $this->Almacen_pendientes_model->cliente_por_nombre($data['sede']);
        if (!$id) $data['creado_por'] = (int) $this->session->userdata('id_usu');
        $guardado = $errores ? false : $this->Almacen_pendientes_model->guardar($id, $data);
        if (!$errores && !$guardado) $errores[] = 'No se pudo guardar el pendiente.';
        if ($this->input->is_ajax_request()) {
            if ($errores) { $this->respuesta_json(array('ok'=>false,'errores'=>$errores), 422); return; }
            $p = $this->Almacen_pendientes_model->obtener($guardado);
            $this->respuesta_json(array('ok'=>true,'id'=>(int) $p->id,'fila'=>$this->load->view('almacen/pendiente_fila', array('p'=>$p,'puede_gestionar'=>$this->puede_gestionar_pendientes(),'token'=>$this->session->userdata('almacen_token')), true),'mensaje'=>'Pendiente guardado.'));
            return;
        }
        if ($errores) { $data['id'] = $id; $this->pagina('pendiente', array_merge(array('pendiente'=>(object) $data, 'errores'=>$errores), $this->datos_pendiente())); return; }
        $this->session->set_flashdata('almacen_ok', 'Pendiente guardado.'); redirect('almacen/pendientes');
    }

    public function atender_pendiente()
    {
        $this->validar_post(); $this->load->model('Almacen_pendientes_model');
        if (!$this->puede_gestionar_pendientes()) { show_error('Solo Almacén o Administración pueden marcar pendientes como atendidos.', 403); return; }
        $id = $this->input->post('id'); $obs = $this->input->post('observacion');
        if (!is_string($id) || !ctype_digit($id)) { show_error('Pendiente inválido.', 400); return; }
        $obs = is_string($obs) ? trim($obs) : '';
        $ok = $this->Almacen_pendientes_model->atender((int) $id, (int) $this->session->userdata('id_usu'), mb_substr($obs, 0, 200));
        $this->session->set_flashdata($ok ? 'almacen_ok' : 'almacen_error', $ok ? 'Pendiente marcado como atendido.' : 'El pendiente no existe o ya estaba atendido.');
        $vista = $this->input->post('vista'); $vista = is_string($vista) && isset(Almacen_pendientes_model::VISTAS[$vista]) ? $vista : 'abiertos';
        redirect('almacen/pendientes?vista='.$vista);
    }

    private function descargar_excel($nombre,$contenido)
    {
        $this->output->set_content_type('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->set_header('Content-Disposition: attachment; filename="'.$nombre.'"')
            ->set_header('Cache-Control: private, no-store')
            ->set_header('X-Content-Type-Options: nosniff')
            ->set_output($contenido);
    }
}
