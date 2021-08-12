<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// require('Fpdf_gen.php');
require_once APPPATH.'third_party/fpdf181/fpdf.php';

class Reporte_certificado extends FPDF{

	public $margin_left = 20;
	var $B=0;
    var $I=0;
    var $U=0;
    var $HREF='';
	var $ALIGN='';

	const DPI = 96;
	const MM_IN_INCH = 25.4;
	const A4_HEIGHT = 210;
	const A4_WIDTH = 297;
	// tweak these values (in pixels)
	const MAX_WIDTH = 650;
	const MAX_HEIGHT = 1040;

	function pixelsToMM($val) {
        return $val * self::MM_IN_INCH / self::DPI;
	}

	function resizeToFit($imgFilename)
	{
		list($width, $height) = getimagesize($imgFilename);
		$widthScale = self::MAX_WIDTH / $width;
		$heightScale = self::MAX_HEIGHT / $height;
		$scale = min($widthScale, $heightScale);
		return array(
			round($this->pixelsToMM($scale * $width)),
			round($this->pixelsToMM($scale * $height))
		);
	}

	function centerImage($img)
	{
		list($width, $height) = $this->resizeToFit($img);
		// you will probably want to swap the width/height
		// around depending on the page's orientation
		$this->Image(
			$img,
			(self::A4_HEIGHT - $width) / 2,
			(self::A4_WIDTH - $height) / 2,
			$width,
			$height
		);
	}
	
	function setHeader($id)
	{
        $this->SetFont('Arial','B',7);
		$this->Image('assets/images/header2.png',4,5,203);
        $this->SetXY(4,102);
        $this->SetTextColor(0,79,144);
        $this->Cell(12,4,utf8_decode('SEGELIM'),0,0,'L');
        $this->SetTextColor(255,0,0);
        $this->Cell(20,4,utf8_decode('PERU SAC'),0,1,'L');

        $this->SetFont('Arial','',7);
        $this->SetTextColor(0,79,144);
        $this->SetX(4);
        $this->Cell(20,3,utf8_decode('RUC: 20524055130'),0,1,'L');
        $this->SetX(4);
        $this->Cell(20,3,utf8_decode('Jr. Cdte Enrique Barrón Nº 1323'),0,1,'L');
        $this->SetX(4);
        $this->Cell(20,3,utf8_decode('Urb. Santa Beatriz - Lima'),0,1,'L');
        $this->SetX(4);
        $this->Cell(20,3,utf8_decode('Central: 4725307 - 4718938'),0,1,'L');
        $this->SetX(4);
        $this->Cell(20,3,utf8_decode('Cel.: 975365954'),0,1,'L');
        $this->SetX(4);
        $this->Cell(20,3,utf8_decode('RPM: 946156888 / 998332483'),0,1,'L');
        $this->SetTextColor(255,0,0);
        $this->SetX(4);
        $this->Cell(20,3,utf8_decode('www.segelimperu.com'),0,1,'L');
        $this->SetX(4);
        $this->Cell(20,3,utf8_decode('servicioalcliente@segelimperu.com'),0,1,'L');
        $this->SetTextColor(0,79,144);
        $this->SetX(4);
        $this->Cell(20,3,utf8_decode('Autorización:Resolución Administrativa'),0,1,'L');
        $this->SetX(4);
        $this->Cell(20,3,utf8_decode('Nº 1249-2018-DESAIA-DIRIS-LC'),0,1,'L');
		$this->Ln(10);
	}

    public function setData($data)
    {
        $cellMargin = 2 * 1.000125;
        $this->SetFont('Arial','B',18);
        $this->SetTextColor(255,0,0);
        $this->SetXY(95,23);
        $this->Cell(55,3,utf8_decode('CERTIFICADO Nº '),0,0,'L');
        $this->SetTextColor(0);
        $this->Cell(20,3,str_pad($data->certificadoid, 11, "0", STR_PAD_LEFT),0,0,'L');

        $this->Ln(10);
        $this->SetFont('Arial','',10);

        $this->SetX(59);
        $this->Cell(55,5,utf8_decode('Por el presente certificamos que se han realizado los servicios de saneamiento ambiental'),0,1,'L');
        $this->SetX(54);
        $this->Cell(55,5,utf8_decode('correspondiente a:'),0,1,'L');
        $this->Ln(3);

        $this->SetFont('Arial','',9);
        $this->SetX(52);
        $this->Cell(5,4,'(' . (($data->servicio == 'DESINSECTACION') ? "X" : '  ') . ')',0,0,'L');
        $this->Cell(35,4,utf8_decode('Desinsectación'),0,0,'L');
        $this->Cell(5,4,'(' . (($data->servicio == 'LIMPIEZARESERVORIO') ? "X" : '  ') . ')',0,0,'L');
        $y = $this->GetY();
        $x = $this->GetX();
        $this->MultiCell(50,3,utf8_decode('Limpieza y desinfección de reservarios de agua'),0,'L');
        $this->SetY($y);
        $this->SetX($x+50);
        $this->Cell(5,4,'(' . (($data->servicio == 'LIMPIEZATANQUE') ? "X" : '  ') . ')',0,0,'L');
        $this->Cell(43,4,utf8_decode('Limpieza de tanque séptico'),0,0,'L');
        $this->Ln(9);

        $this->SetX(52);
        $this->Cell(5,4,'(' . (($data->servicio == 'DESRATIZACION') ? "X" : '  ') . ')',0,0,'L');
        $this->Cell(35,4,utf8_decode('Desratizacón'),0,0,'L');
        $this->Cell(5,4,'(' . (($data->servicio == 'DESINFECCION') ? "X" : '  ') . ')',0,0,'L');
        $this->Cell(19,4,utf8_decode('Desinfección'),0,0,'L');
        $this->SetFont('Arial','B',6);
        $this->Cell(43,4,(($data->servicio == 'DESINFECCION') ? '(Protocolo Covid-19/Aplicación de Virucida DQM/Resol. 3365-2017/DCEA/DIGESA/SA)' : ''),0,0,'L');
        $this->Ln(8);

        $this->SetFont('Arial','',9);
        $this->SetX(43);
        $this->Cell(6,5,utf8_decode('A: '),0,0,'L');
        $this->Cell(154,5,utf8_decode(strtoupper($data->cliente)),'B',1,'L');
        $this->Ln(4);

        $direccion = utf8_decode($data->ubicacion);
        $dir_w = $this->GetStringWidth($direccion);
        $rows = ceil($dir_w / (142 - $cellMargin));
        $h = 5 * $rows;

        $this->SetX(41);
        $this->Cell(20,$h,utf8_decode('Ubicado en:'),0,0,'L');
        $this->MultiCell(142,5,strtoupper($direccion),'B','L');
        $this->Ln(4);

        $this->SetFont('Arial','',7);
        $giro = utf8_decode($data->giro);
        $giro_w = $this->GetStringWidth($giro);
        $rows = ceil($giro_w / (59 - $cellMargin));
        $h = 5 * $rows;

        $this->SetFont('Arial','',9);
        $this->SetX(43);
        $this->Cell(12,$h,utf8_decode('Giro: '),0,0,'L');
        $y = $this->GetY();
        $x = $this->GetX();

        $this->SetFont('Arial','',7);
        $this->MultiCell(59,5,strtoupper($giro),'B','C');
        $this->SetY($y+$h-5);
        $this->SetX($x+59);

        $this->SetFont('Arial','',7);
        $area_tratada = utf8_decode($data->area);
        $area_tratada_w = $this->GetStringWidth($area_tratada);
        $rows = ceil($area_tratada_w / (64 - $cellMargin));
        $h = 5 * $rows;

        $this->SetFont('Arial','',9);
        $this->Cell(22,$h,utf8_decode(' Area Tratada: '),0,0,'L');
        $this->SetFont('Arial','',7);
        $this->MultiCell(64,5,strtoupper($area_tratada),'B','C');
        $this->Ln(4);

        setlocale(LC_TIME, "spanish");
        $fechacreacion = strftime("%d - %B - %Y", strtotime($data->fechacreacion));
        $fechaservicio = strftime("%d - %B - %Y", strtotime($data->fechaservicio));
        $this->SetX(43);
        $this->SetFont('Arial','',9);
        $this->Cell(12,5,utf8_decode('Fecha: '),0,0,'L');
        $this->SetFont('Arial','',9);
        $this->Cell(59,5,utf8_decode(strtoupper($fechacreacion)),'B',0,'C');
        $this->SetFont('Arial','',9);
        $this->Cell(33,5,utf8_decode('Fecha de Servicios: '),0,0,'L');
        $this->Cell(56,5,utf8_decode(strtoupper($fechaservicio)),'B',1,'C');
        $this->Ln(23);

        $this->SetX(63);
        $this->Cell(50,5,utf8_decode('REPRESENTANTE LEGAL'),'T',0,'C');
        $this->Cell(20,5,'',0,0,'C');
        $this->Cell(50,5,utf8_decode('DIRECTOR TECNICO'),'T',0,'C');
    }

	function setFooter($url_foto_firma, $fecha_ingreso)
	{
		//date_default_timezone_set('America/Bogota');
		$this->SetFont('Arial',null,10);
		$this->SetMargins($this->margin_left,10);  
		$this->Ln(10);
		$this->Cell(0,6, utf8_decode('Declaro bajo juramente que lo escrito en el presente formulario es correcto y que no he omitido'),0,2);
		$this->Cell(0,6, utf8_decode('intencionalmente ningún dato sobre las preguntas contenidas en el mismo. Si se descubriera posteriormente'),0,2);	    
		$this->Cell(0,6, utf8_decode('su inexactitud será causal de retiro de la empresa de acuerdo al Art. 25 inciso D del D.L. Nº 728, LEY DE'),0,2);
		$this->Cell(0,6, utf8_decode('PRODUCTIVIDAD Y COMPETITIVIDAD LABORAL - DECRETO SUPREMO Nº 003- 97 TR'),0,2);
	    $x = $this->GetX();
		$this->Ln(10);
		$this->Cell(26,6,'Lugar y Fecha: ');
		$this->SetFont('Arial','B',10);
		$this->Cell(40,6,'LIMA '.$fecha_ingreso);

		$y = $this->GetY();
		$this->Ln(60);
		$this->SetFont('Arial',null,10);
		if(!empty($url_foto_firma)){
			$this->Image($url_foto_firma,$x+28,$y+18,120,48);
		}
		// $this->Cell(0,6,'FIRMA Y HUELLA DACTILAR',0,0,'C');
	}

	function WordWrap($text, $maxwidth)
	{
	    $text = trim($text);
	    if ($text==='')
	        return 0;
	    $space = $this->GetStringWidth(' ');
	    $lines = explode("\n", $text);
	    $text = '';
	    $count = 0;

	    foreach ($lines as $line)
	    {
	        $words = preg_split('/ +/', $line);
	        $width = 0;

	        foreach ($words as $word)
	        {
	            $wordwidth = $this->GetStringWidth($word);
	            if ($wordwidth > $maxwidth)
	            {
	                // Word is too long, we cut it
	                for($i=0; $i<strlen($word); $i++)
	                {
	                    $wordwidth = $this->GetStringWidth(substr($word, $i, 1));
	                    if($width + $wordwidth <= $maxwidth)
	                    {
	                        $width += $wordwidth;
	                        $text .= substr($word, $i, 1);
	                    }
	                    else
	                    {
	                        $width = $wordwidth;
	                        $text = rtrim($text)."\n".substr($word, $i, 1);
	                        $count++;
	                    }
	                }
	            }
	            elseif($width + $wordwidth <= $maxwidth)
	            {
	                $width += $wordwidth + $space;
	                $text .= $word.' ';
	            }
	            else
	            {
	                $width = $wordwidth + $space;
	                $text = rtrim($text)."\n".$word.' ';
	                $count++;
	            }
	        }
	        $text = rtrim($text)."\n";
	        $count++;
	    }
	    $text = rtrim($text);
	    return $count;
	}

	function calculateLineH($hijo,$w)
	{
		$h = 6;
		$i = 1;
		foreach ($hijo as $key => $propiedad) {
			$nb[] = $this->WordWrap(utf8_decode($propiedad),$w[$i]);
			$i+=1;
		}
		$lh = max($nb)*$h;
		return $lh;
	}

	function setImagenPagina($rutaimagen)
	{
		$this->AddPage();
		$this->centerImage($rutaimagen);
	}
	function WriteHTML($html)
    {
        //HTML parser
        $html=str_replace("\n",' ',$html);
        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                //Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                elseif($this->ALIGN=='center')
                    $this->Cell(0,5,$e,0,1,'C');
				else
                    $this->Write(5,$e);
            }
            else
            {
                //Tag
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    //Extract properties
                    $a2=explode(' ',$e);
                    $tag=strtoupper(array_shift($a2));
                    $prop=array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $prop[strtoupper($a3[1])]=$a3[2];
                    }
                    $this->OpenTag($tag,$prop);
                }
            }
        }
    }

    function OpenTag($tag,$prop)
    {
        //Opening tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF=$prop['HREF'];
        if($tag=='BR')
            $this->Ln(5);
        if($tag=='P')
            $this->ALIGN=$prop['ALIGN'];
        if($tag=='HR')
        {
            if( !empty($prop['WIDTH']) )
                $Width = $prop['WIDTH'];
            else
                $Width = $this->w - $this->lMargin-$this->rMargin;
            $this->Ln(2);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.4);
            $this->Line($x,$y,$x+$Width,$y);
            $this->SetLineWidth(0.2);
            $this->Ln(2);
        }
    }

    function CloseTag($tag)
    {
        //Closing tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='P')
            $this->ALIGN='';
    }

    function SetStyle($tag,$enable)
    {
        //Modify style and select corresponding font
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach(array('B','I','U') as $s)
            if($this->$s>0)
                $style.=$s;
        $this->SetFont('',$style);
    }

    function PutLink($URL,$txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }

    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
        $xc = $x+$w-$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));

        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
        $xc = $x+$w-$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x+$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }

    function SetDash($black=null, $white=null)
    {
        if($black!==null)
            $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }
}
