<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Some bundled FPDF/font files end with whitespace outside PHP tags.
ob_start();
require_once APPPATH.'third_party/fpdf181/fpdf.php';
ob_end_clean();

/**
 * Guía de traslado - almacén (formato de la empresa) para cada despacho.
 * Transporte, conductor, peso y comprobante quedan en blanco para llenarse a mano.
 */
class Almacen_guia_pdf extends FPDF
{
    const RUC='20524055130';
    const DIRECCION='Jr. Enrique Barrón N° 1323 - Urb. Santa Beatriz, Lima - Lima';
    const MOTIVOS=array(1=>'Venta',2=>'Venta sujeta a confirmación',3=>'Compra',4=>'Consignación',5=>'Devolución',6=>'Traslado establec. misma empresa',7=>'Traslado de zona primaria',8=>'Traslado para transformación',9=>'Recojo de bienes transportados',10=>'Traslado por emisor itinerante',11=>'Importación',12=>'Exportación',13=>'Otros');
    private $despacho;
    private $azul=array(35,52,140);
    private $anchos=array(24,100,22,24,20);
    private function texto($s) { return iconv('UTF-8','Windows-1252//TRANSLIT',str_replace(array("\r","\t"),array('',' '),(string)$s)); }
    public function generar($despacho,$detalles)
    {
        ob_start();
        try { return $this->construir($despacho,$detalles); }
        finally { ob_end_clean(); }
    }
    private function construir($despacho,$detalles)
    {
        $this->despacho=$despacho;
        $this->SetMargins(10,10,10); $this->SetAutoPageBreak(true,18);
        $this->AliasNbPages();
        $this->SetTitle($this->texto('Guía de traslado - almacén '.Almacen_despachos_model::numero($despacho)));
        $this->SetAuthor($this->texto('SEGELIMPERÚ S.A.C.'));
        $this->AddPage();
        foreach ($detalles as $d) {
            $descripcion=$d->descripcion.($d->marca!==''?' - '.$d->marca:'');
            $this->fila(array($d->codigo,$descripcion,number_format($d->cantidad,3,'.',','),$d->unidad,''));
        }
        // Cierre del cuerpo de la tabla hasta el bloque final.
        $fin=max($this->GetY(),150);
        if ($fin+88>$this->PageBreakTrigger) { $this->columnas($this->GetY(),$this->GetY()); $this->AddPage(); $fin=$this->GetY(); }
        $this->columnas($this->GetY(),$fin);
        $this->bloque_final($fin+3);
        return $this->Output('S');
    }
    private function etiqueta($x,$y,$texto)
    {
        $this->SetFillColor($this->azul[0],$this->azul[1],$this->azul[2]); $this->SetTextColor(255); $this->SetFont('Arial','B',6.5);
        $ancho=$this->GetStringWidth($this->texto($texto))+3;
        $this->SetXY($x,$y); $this->Cell($ancho,4,$this->texto($texto),0,0,'L',true);
        return $x+$ancho+1.5;
    }
    private function campo($x,$y,$etiqueta,$valor,$ancho)
    {
        $this->SetTextColor(60); $this->SetFont('Arial','',7); $this->SetXY($x,$y);
        $w=$this->GetStringWidth($this->texto($etiqueta))+1.5; $this->Cell($w,4.5,$this->texto($etiqueta),0,0);
        // Reduce la letra si el dato no cabe en su recuadro.
        $tamano=8; $this->SetFont('Arial','B',$tamano); $this->SetTextColor(20);
        while ($tamano>5.5 && $this->GetStringWidth($this->texto($valor))>$ancho-$w-1) { $tamano-=0.5; $this->SetFontSize($tamano); }
        $this->Cell($ancho-$w,4.5,$this->texto($valor),0,0);
        $this->SetDrawColor(150); $this->SetLineWidth(0.1); $this->Line($x+$w,$y+4.3,$x+$ancho,$y+4.3);
    }
    public function Header()
    {
        $d=$this->despacho;
        // Empresa
        $this->Image(FCPATH.'assets/images/logo_segel.png',14,8,96);
        $this->SetTextColor($this->azul[0],$this->azul[1],$this->azul[2]); $this->SetFont('Arial','',7.5);
        $this->SetXY(10,26.5); $this->Cell(104,3.3,$this->texto('Limpieza de alfombras · Limpieza de muebles · Fumigaciones · Limpieza en general'),0,2,'C');
        $this->Cell(104,3.3,$this->texto(self::DIRECCION),0,2,'C');
        $this->Cell(104,3.3,$this->texto('Telf.: 01 472-5307 · Cel.: 975365954 - 998332483'),0,2,'C');
        $this->Cell(104,3.3,$this->texto('www.segelimperu.com · servicioalcliente@segelimperu.com'),0,2,'C');
        // Recuadro RUC / número
        $this->SetDrawColor($this->azul[0],$this->azul[1],$this->azul[2]); $this->SetLineWidth(0.5);
        $this->RoundedRect(124,9,76,32,3);
        $this->SetLineWidth(0.3); $this->Line(124,20,200,20); $this->Line(124,29,200,29);
        $this->SetFont('Arial','B',15); $this->SetXY(124,11); $this->Cell(76,8,'R.U.C. '.self::RUC,0,0,'C');
        $this->SetFont('Arial','B',11); $this->SetXY(124,21); $this->Cell(76,7,$this->texto('GUÍA DE TRASLADO - ALMACÉN'),0,0,'C');
        list($serie,$numero)=explode('-',Almacen_despachos_model::numero($d));
        $this->SetXY(124,31); $this->SetFont('Arial','B',15); $this->Cell(30,9,$serie.' -',0,0,'R');
        $this->SetTextColor(200,30,30); $this->Cell(46,9,$this->texto('N° ').$numero,0,0,'C');
        if ($this->PageNo()===1) $this->datos_traslado($d);
        else $this->SetY(45);
        $this->cabecera_tabla();
    }
    private function datos_traslado($d)
    {
        $this->SetDrawColor($this->azul[0],$this->azul[1],$this->azul[2]); $this->SetLineWidth(0.4);
        $this->RoundedRect(10,45,94,37,2); $this->RoundedRect(106,45,94,37,2);
        // Partida, fecha y transporte (en blanco)
        $x=$this->etiqueta(12,47,'PUNTO DE PARTIDA'); $this->campo($x,47,'',self::DIRECCION,102-$x);
        $x=$this->etiqueta(12,53,'TRASLADO'); $this->campo($x,53,'Fecha de inicio:',date('d/m/Y',strtotime($d->creado_en)),68-$x);
        $this->campo(70,53,'Costo mín. S/','',32);
        $x=$this->etiqueta(12,60,'UNIDAD DE TRANSPORTE Y CONDUCTOR'); $this->campo($x,60,'Marca:','',102-$x);
        $this->campo(12,66,'Placa:','',28); $this->campo(41,66,'N° Cons. Insc.:','',30); $this->campo(72,66,'N° Lic.:','',30);
        $this->campo(12,73,'Nombre del conductor:','',90);
        // Llegada, destinatario y empresa de transportes (en blanco)
        $x=$this->etiqueta(108,47,'PUNTO DE LLEGADA'); $this->campo($x,47,'',$d->cliente_direccion,198-$x);
        $x=$this->etiqueta(108,53,'DESTINATARIO'); $this->campo($x,53,'Nombre o razón social:',$d->cliente_nombre,198-$x);
        $this->campo(108,59,'R.U.C.:',$d->cliente_ruc,45); $this->campo(154,59,'Código:',$d->cliente_codigo,44);
        $x=$this->etiqueta(108,66,'EMPRESA DE TRANSPORTES'); $this->campo($x,66,'Razón social:','',198-$x);
        $this->campo(108,73,'R.U.C.:','',90);
        $this->SetY(85);
    }
    private function cabecera_tabla()
    {
        $this->SetDrawColor($this->azul[0],$this->azul[1],$this->azul[2]); $this->SetLineWidth(0.4);
        $this->SetFillColor(225,230,245); $this->SetTextColor($this->azul[0],$this->azul[1],$this->azul[2]); $this->SetFont('Arial','B',8);
        $titulos=array('CÓDIGO','DESCRIPCIÓN','CANTIDAD',"UNIDAD DE\nMEDIDA","PESO\nTOTAL");
        $x=10; $y=$this->GetY();
        foreach ($titulos as $i=>$t) {
            $this->Rect($x,$y,$this->anchos[$i],8,'DF');
            $this->SetXY($x,$y+(strpos($t,"\n")===false?2:0.5)); $this->MultiCell($this->anchos[$i],3.6,$this->texto($t),0,'C');
            $x+=$this->anchos[$i];
        }
        $this->SetXY(10,$y+8); $this->SetTextColor(20); $this->SetFont('Arial','',8.5);
    }
    private function columnas($desde,$hasta)
    {
        // Líneas verticales del cuerpo de la tabla, como en la guía impresa.
        $this->SetDrawColor($this->azul[0],$this->azul[1],$this->azul[2]); $this->SetLineWidth(0.4);
        $x=10; $inicio=$this->PageNo()===1?93:53;
        foreach ($this->anchos as $a) { $this->Line($x,$inicio,$x,$hasta); $x+=$a; }
        $this->Line(200,$inicio,200,$hasta); $this->Line(10,$hasta,200,$hasta);
    }
    public function AcceptPageBreak()
    {
        $this->columnas($this->GetY(),$this->GetY());
        return true;
    }
    private function bloque_final($y)
    {
        $d=$this->despacho;
        $this->SetDrawColor($this->azul[0],$this->azul[1],$this->azul[2]); $this->SetLineWidth(0.4);
        // Comprobante de pago (en blanco)
        $this->RoundedRect(10,$y,190,7,1.5);
        $this->SetXY(12,$y+1.5); $this->SetFont('Arial','B',8); $this->SetTextColor($this->azul[0],$this->azul[1],$this->azul[2]);
        $this->Cell(80,4,$this->texto('TIPO Y NÚMERO DE COMPROBANTE DE PAGO:'),0,0);
        // Motivo de traslado: entrega de materiales al cliente (13. Otros)
        $y+=9; $this->RoundedRect(10,$y,190,27,1.5);
        $this->etiqueta(12,$y+1.5,'MOTIVO DE TRASLADO');
        $columnas=array(array(1,2,3,4,5),array(6,7,8,9,10),array(11,12,13));
        $xs=array(12,74,136); $ancho=60;
        foreach ($columnas as $c=>$numeros) foreach ($numeros as $f=>$n) {
            $yy=$y+7+$f*3.9; $this->SetXY($xs[$c],$yy); $this->SetTextColor(40); $this->SetFont('Arial','',6.5);
            $this->Cell($ancho-6,3.6,$this->texto($n.'. '.mb_strtoupper(self::MOTIVOS[$n],'UTF-8')),0,0);
            $this->SetDrawColor(120); $this->SetLineWidth(0.2); $this->Rect($xs[$c]+$ancho-5,$yy+0.2,3.2,3.2);
            if ($n===13) { $this->SetFont('Arial','B',9); $this->SetXY($xs[$c]+$ancho-5,$yy-0.2); $this->Cell(3.2,4,'X',0,0,'C'); }
        }
        $this->SetXY(136,$y+19); $this->SetFont('Arial','B',7); $this->SetTextColor(20);
        $this->MultiCell(60,3.4,$this->texto('Entrega de materiales · Asignación '.$d->periodo.' #'.$d->asignacion_id),0,'L');
        // Firmas
        $y+=40; $this->SetDrawColor(60); $this->SetLineWidth(0.3);
        $this->Line(20,$y,95,$y); $this->Line(115,$y,190,$y);
        $this->SetTextColor($this->azul[0],$this->azul[1],$this->azul[2]); $this->SetFont('Arial','B',8);
        $this->SetXY(20,$y+1); $this->Cell(75,4,$this->texto('SEGELIMPERÚ S.A.C. · ALMACÉN'),0,2,'C'); $this->SetFont('Arial','',7); $this->Cell(75,4,'FIRMA',0,0,'C');
        $this->SetFont('Arial','B',8); $this->SetXY(115,$y+1); $this->Cell(75,4,$this->texto('RECIBÍ CONFORME'),0,2,'C'); $this->SetFont('Arial','',7); $this->Cell(75,4,'NOMBRE, DNI Y FIRMA',0,0,'C');
    }
    public function Footer()
    {
        $this->SetY(-12); $this->SetFont('Arial','',7); $this->SetTextColor(110);
        $this->Cell(95,5,$this->texto('Generada el '.date('d/m/Y H:i',strtotime($this->despacho->creado_en)).' · Despacho #'.$this->despacho->id),0,0,'L');
        $this->Cell(95,5,$this->texto('Página '.$this->PageNo().' de {nb}'),0,0,'R');
    }
    private function RoundedRect($x,$y,$w,$h,$r)
    {
        $k=$this->k; $hp=$this->h; $arc=4/3*(sqrt(2)-1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k));
        $this->_out(sprintf('%.2F %.2F l',($x+$w-$r)*$k,($hp-$y)*$k));
        $this->arco($x+$w-$r+$r*$arc,$y,$x+$w,$y+$r-$r*$arc,$x+$w,$y+$r);
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h-$r))*$k));
        $this->arco($x+$w,$y+$h-$r+$r*$arc,$x+$w-$r+$r*$arc,$y+$h,$x+$w-$r,$y+$h);
        $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-($y+$h))*$k));
        $this->arco($x+$r-$r*$arc,$y+$h,$x,$y+$h-$r+$r*$arc,$x,$y+$h-$r);
        $this->_out(sprintf('%.2F %.2F l',$x*$k,($hp-($y+$r))*$k));
        $this->arco($x,$y+$r-$r*$arc,$x+$r-$r*$arc,$y,$x+$r,$y);
        $this->_out('S');
    }
    private function arco($x1,$y1,$x2,$y2,$x3,$y3)
    {
        $h=$this->h; $k=$this->k;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',$x1*$k,($h-$y1)*$k,$x2*$k,($h-$y2)*$k,$x3*$k,($h-$y3)*$k));
    }
    private function lineas($ancho,$texto)
    {
        $cw=$this->CurrentFont['cw']; $max=($ancho-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$texto); $n=strlen($s); if ($n && $s[$n-1]==="\n") $n--;
        $sep=-1; $i=0; $j=0; $l=0; $lineas=1;
        while ($i<$n) {
            $c=$s[$i];
            if ($c==="\n") { $i++; $sep=-1; $j=$i; $l=0; $lineas++; continue; }
            if ($c===' ') $sep=$i;
            $l+=$cw[$c] ?? 0;
            if ($l>$max) { if ($sep===-1) { if ($i===$j) $i++; } else $i=$sep+1; $sep=-1; $j=$i; $l=0; $lineas++; }
            else $i++;
        }
        return $lineas;
    }
    private function fila($campos)
    {
        $textos=array_map(array($this,'texto'),$campos); $n=1;
        foreach ($textos as $i=>$s) $n=max($n,$this->lineas($this->anchos[$i],$s));
        $alto=4.5*$n+2;
        if ($this->GetY()+$alto>$this->PageBreakTrigger) { $this->columnas($this->GetY(),$this->GetY()); $this->AddPage(); }
        $x=10; $y=$this->GetY();
        $alineacion=array('L','L','R','C','R');
        foreach ($textos as $i=>$s) {
            $this->SetXY($x,$y+1); $this->MultiCell($this->anchos[$i],4.5,$s,0,$alineacion[$i]); $x+=$this->anchos[$i];
        }
        $this->SetXY(10,$y+$alto);
    }
}
