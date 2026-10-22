<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'third_party/xlsxwriter/xlsxwriter.class.php';

/** Saved warehouse reports, with numeric amounts and literal user-entered text. */
class Almacen_excel
{
    const MONEDA = '"S/ " #,##0.00;[Red]-"S/ " #,##0.00';
    const CANTIDAD = '#,##0.000';

    public function asignacion($asignacion, $cliente, array $detalles, $saldo_anterior)
    {
        $writer=$this->escritor('Asignación '.$asignacion->periodo.' · '.$cliente->nombre);
        $columnas=array('Categoría'=>'string','Descripción'=>'string','Unidad'=>'string','Marca'=>'string','Cantidad'=>self::CANTIDAD,'Precio valorado'=>self::MONEDA,'Importe'=>self::MONEDA);
        $anchos=array(24,55,12,22,15,22,22);
        $hoja='Asignación';
        $this->iniciar($writer,$hoja,$columnas,$anchos,6);
        $this->combinada($writer,$hoja,'SEGELIM PERÚ · ASIGNACIÓN MENSUAL',7,'titulo');
        $this->combinada($writer,$hoja,'Cliente: '.$cliente->nombre.($cliente->grupo?' · '.$cliente->grupo:''),7);
        $this->combinada($writer,$hoja,'Período: '.$asignacion->periodo.' · Estado: '.$this->estado($asignacion->estado),7);
        $this->combinada($writer,$hoja,'Precios valorados con IGV incluido · Versión guardada',7);
        $writer->writeSheetRow($hoja,array());
        $writer->writeSheetRow($hoja,array_keys($columnas),$this->estilo('cabecera'));
        $grupos=array();
        foreach($detalles as $detalle) $grupos[$detalle->categoria][]=$detalle;
        foreach($grupos as $categoria=>$lineas) {
            $this->combinada($writer,$hoja,$categoria,7,'grupo');
            $subtotal=0;
            foreach($lineas as $i=>$detalle) {
                $writer->writeSheetRow($hoja,$this->fila_asignacion($detalle),$this->estilo($i%2?'alterno':'fila',$detalle->descripcion));
                $subtotal+=(float)$detalle->total;
            }
            $this->importe($writer,$hoja,'Subtotal '.$categoria,round($subtotal,2),7,'subtotal');
        }
        $this->importe($writer,$hoja,'TOTAL SOLICITADO',(float)$asignacion->total,7,'total');
        $writer->writeSheetRow($hoja,array());
        $this->importe($writer,$hoja,'Presupuesto mensual',(float)$asignacion->presupuesto,7);
        $this->importe($writer,$hoja,'Saldo anterior',(float)$saldo_anterior,7);
        $this->importe($writer,$hoja,'Disponible del mes',round($asignacion->presupuesto-$asignacion->total,2),7);
        $this->importe($writer,$hoja,'Saldo acumulado',round($saldo_anterior+$asignacion->presupuesto-$asignacion->total,2),7,'subtotal');
        $this->notas($writer,$hoja,'Observaciones',$asignacion->observaciones,7);
        $this->notas($writer,$hoja,'Comentario de revisión',$asignacion->comentario_revision,7);
        $this->iniciar($writer,'Detalle',$columnas,$anchos,1,true);
        $writer->writeSheetRow('Detalle',array_keys($columnas),$this->estilo('cabecera'));
        foreach($detalles as $i=>$detalle) $writer->writeSheetRow('Detalle',$this->fila_asignacion($detalle),$this->estilo($i%2?'alterno':'fila',$detalle->descripcion));
        return $this->contenido($writer);
    }

    public function compra($compra, array $detalles, array $asignaciones, $cantidades_clientes=null)
    {
        $codigo='OC-'.str_replace('-','',$compra->periodo).'-'.str_pad((int)($compra->numero ?? 1),2,'0',STR_PAD_LEFT);
        $writer=$this->escritor('Orden de compra '.$codigo);
        if($cantidades_clientes!==null) $this->compra_clientes($writer,$detalles,$asignaciones,$cantidades_clientes);
        $columnas=array('Descripción'=>'string','Unidad'=>'string','Marca'=>'string','Solicitado'=>self::CANTIDAD,'Stock al consolidar'=>self::CANTIDAD,'Sugerido'=>self::CANTIDAD,'A adquirir'=>self::CANTIDAD,'Precio con IGV'=>self::MONEDA,'Importe'=>self::MONEDA);
        $anchos=array(55,12,22,16,21,16,16,22,22);
        $hoja='Orden de compra';
        $this->iniciar($writer,$hoja,$columnas,$anchos,6);
        $this->combinada($writer,$hoja,'SEGELIM PERÚ · ORDEN DE COMPRA '.$codigo,9,'titulo');
        $this->combinada($writer,$hoja,'Proveedor: '.($compra->proveedor?:'Sin registrar'),9);
        $this->combinada($writer,$hoja,'Período: '.$compra->periodo.' · Estado: '.($compra->estado==='recibida'?'Recibida':'Borrador'),9);
        $this->combinada($writer,$hoja,'Precios reales de compra · Versión guardada'.($compra->estado==='recibida'?' · Recepción: '.$compra->recibido_en:''),9);
        $writer->writeSheetRow($hoja,array());
        $writer->writeSheetRow($hoja,array_keys($columnas),$this->estilo('cabecera'));
        $this->iniciar($writer,'Detalle',$columnas,$anchos,1,true);
        $writer->writeSheetRow('Detalle',array_keys($columnas),$this->estilo('cabecera'));
        foreach($detalles as $i=>$detalle) {
            $fila=array($detalle->descripcion,$detalle->unidad,$detalle->marca,(float)$detalle->solicitado,$detalle->stock_confirmado?(float)$detalle->stock_disponible:'Por verificar',(float)max(0,$detalle->solicitado-$detalle->stock_disponible),(float)$detalle->cantidad,(float)$detalle->precio_real,(float)$detalle->total);
            $estilo=$this->estilo($i%2?'alterno':'fila',$detalle->descripcion);
            $writer->writeSheetRow($hoja,$fila,$estilo);
            $writer->writeSheetRow('Detalle',$fila,$estilo);
        }
        $this->importe($writer,$hoja,'TOTAL DE COMPRA',(float)$compra->total,9,'total');
        $this->notas($writer,$hoja,'Observaciones',$compra->observaciones,9);
        $this->iniciar($writer,'Asignaciones',array('Cliente'=>'string','Período'=>'string','Total solicitado'=>self::MONEDA),array(60,18,25),1,true);
        $writer->writeSheetRow('Asignaciones',array('Cliente','Período','Total solicitado'),$this->estilo('cabecera'));
        foreach($asignaciones as $i=>$asignacion) $writer->writeSheetRow('Asignaciones',array($asignacion->nombre,$asignacion->periodo,(float)$asignacion->total),$this->estilo($i%2?'alterno':'fila',$asignacion->nombre));
        return $this->contenido($writer);
    }

    private function compra_clientes($writer,array $detalles,array $asignaciones,array $cantidades)
    {
        $columnas=array('Descripción'=>'string','Unidad'=>'string','Marca'=>'string');
        $etiquetas=array_keys($columnas); $anchos=array(55,12,22); $altura=32;
        foreach($asignaciones as $a) {
            // Distinct IDs keep equally named clients in separate columns.
            $columnas['cliente-'.$a->cliente_id]=self::CANTIDAD;
            $etiquetas[]=$a->nombre; $anchos[]=24;
            $altura=max($altura,ceil(mb_strlen($a->nombre)/22)*15);
        }
        $columnas['Total solicitado']=self::CANTIDAD; $etiquetas[]='Total solicitado'; $anchos[]=20;
        $this->iniciar($writer,'Por cliente',$columnas,$anchos,1,true);
        $cabecera=$this->estilo('cabecera'); $cabecera['height']=$altura;
        $writer->writeSheetRow('Por cliente',$etiquetas,$cabecera);
        foreach($detalles as $i=>$d) {
            $fila=array($d->descripcion,$d->unidad,$d->marca);
            foreach($asignaciones as $a) $fila[]=(float)($cantidades[$d->producto_id][$a->cliente_id] ?? 0);
            $fila[]=(float)$d->solicitado;
            $writer->writeSheetRow('Por cliente',$fila,$this->estilo($i%2?'alterno':'fila',$d->descripcion));
        }
    }

    private function escritor($titulo)
    {
        if(!class_exists('ZipArchive')) throw new RuntimeException('La extensión ZIP es necesaria para exportar Excel.');
        $writer=new Almacen_excel_escritor();
        $writer->setAuthor('Segelim Perú'); $writer->setCompany('Segelim Perú'); $writer->setTitle($titulo);
        return $writer;
    }

    private function contenido($writer)
    {
        $contenido=$writer->writeToString();
        if(!is_string($contenido) || substr($contenido,0,2)!=='PK') throw new RuntimeException('No se pudo generar el archivo Excel.');
        return $contenido;
    }

    private function iniciar($writer,$hoja,$columnas,$anchos,$filas_fijas,$filtros=false)
    {
        $writer->writeSheetHeader($hoja,$columnas,array('suppress_row'=>true,'widths'=>$anchos,'freeze_rows'=>$filas_fijas,'auto_filter'=>$filtros));
    }

    private function fila_asignacion($detalle)
    {
        return array($detalle->categoria,$detalle->descripcion,$detalle->unidad,$detalle->marca,(float)$detalle->cantidad,(float)$detalle->precio_valorado,(float)$detalle->total);
    }

    private function estilo($tipo='fila',$texto='')
    {
        $estilo=array('font'=>'Arial','font-size'=>11,'color'=>'#374151','valign'=>'center','wrap_text'=>true,'height'=>max(25,ceil(mb_strlen($texto)/48)*15),'border'=>'bottom','border-color'=>'#E5E9EE');
        if($tipo==='alterno') $estilo['fill']='#F3F6FA';
        if(in_array($tipo,array('titulo','cabecera','total'),true)) $estilo=array_merge($estilo,array('fill'=>'#225D97','color'=>'#FFFFFF','font-style'=>'bold','height'=>$tipo==='titulo'?38:32));
        if(in_array($tipo,array('grupo','subtotal'),true)) $estilo=array_merge($estilo,array('fill'=>'#E7EEF7','font-style'=>'bold'));
        if($tipo==='titulo') $estilo['font-size']=16;
        return $estilo;
    }

    private function combinada($writer,$hoja,$texto,$columnas,$tipo='fila')
    {
        $fila=$writer->countSheetRows($hoja);
        $estilo=$this->estilo($tipo);
        $estilo['height']=max($estilo['height'],15*(ceil(mb_strlen($texto)/100)+substr_count($texto,"\n")));
        $writer->writeSheetRow($hoja,array_pad(array($texto),$columnas,''),$estilo);
        $writer->markMergedCell($hoja,$fila,0,$fila,$columnas-1);
    }

    private function importe($writer,$hoja,$etiqueta,$importe,$columnas,$tipo='fila')
    {
        $fila=$writer->countSheetRows($hoja);
        $datos=array_pad(array($etiqueta),$columnas-1,''); $datos[]=$importe;
        $estilos=array_fill(0,$columnas,$this->estilo($tipo));
        $estilos[$columnas-1]['halign']='right'; $estilos['height']=28;
        $writer->writeSheetRow($hoja,$datos,$estilos);
        $writer->markMergedCell($hoja,$fila,0,$fila,$columnas-2);
    }

    private function notas($writer,$hoja,$etiqueta,$texto,$columnas)
    {
        if($texto!=='') { $writer->writeSheetRow($hoja,array()); $this->combinada($writer,$hoja,$etiqueta.': '.$texto,$columnas); }
    }

    private function estado($estado)
    {
        $nombres=array('borrador'=>'Borrador','publicado'=>'Pendiente de revisión','aprobado'=>'Aprobada','rechazado'=>'Necesita corrección');
        return $nombres[$estado] ?? $estado;
    }
}

/** No formulas are accepted from descriptions, client names or other user fields. */
class Almacen_excel_escritor extends XLSXWriter
{
    protected function writeCell(XLSXWriter_BuffererWriter &$file,$row_number,$column_number,$value,$num_format_type,$cell_style_idx)
    {
        if(is_string($value) && $value!=='') {
            $file->write('<c r="'.self::xlsCell($row_number,$column_number).'" s="'.$cell_style_idx.'" t="inlineStr"><is><t xml:space="preserve">'.self::xmlspecialchars($value).'</t></is></c>');
            return;
        }
        parent::writeCell($file,$row_number,$column_number,$value,$num_format_type,$cell_style_idx);
    }
}
