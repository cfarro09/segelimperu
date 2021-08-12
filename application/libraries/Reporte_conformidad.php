<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// require('Fpdf_gen.php');
require_once APPPATH.'third_party/fpdf181/fpdf.php';

class Reporte_conformidad extends FPDF{

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
        $this->SetFont('Arial','B',12);
		$this->Image('assets/images/logo-grande.png',34,5,140);
        $this->SetTextColor(62,105,167);
        $this->SetDrawColor(62,105,167);
        $this->Ln(39);

        $this->Cell(49,5,'',0,0,'R');
        $this->Cell(82,4,utf8_decode('ACTA DE CONFORMIDAD DE SERVICIO'),'B',0,'C');
        $this->SetTextColor(255,0,0);
        $this->Cell(59,5,utf8_decode('Nº ') . str_pad($id, 5, "0", STR_PAD_LEFT),0,0,'C');
        $this->SetTextColor(62,105,167);
        $this->Ln(20);
	}

    public function setData($data)
    {
        $cellMargin = 2 * 1.000125;
        $this->SetTextColor(62,105,167);
        $this->SetFont('Arial','',10);
        $this->Cell(53,6,'NUMERO DE ORDEN',0,0,'L');
        $this->Cell(4,6,':',0,0,'C');
        $this->SetTextColor(0);
        $this->Cell(40,6,utf8_decode($data->numeroorden),1,1,'L');

        $this->SetTextColor(62,105,167);
        $this->Cell(53,6,'FECHA DE SERVICIO',0,0,'L');
        $this->Cell(4,6,':',0,0,'C');
        $this->SetTextColor(0);
        $this->Cell(40,6,$data->fechaservicio,1,1,'L');
        $this->Ln(2);

        $servicio = utf8_decode($data->servicio);
        $servicio_w = $this->GetStringWidth($servicio);
        $rows = ceil($servicio_w / (135 - $cellMargin));
        $h = 5 * $rows;
        $this->SetTextColor(62,105,167);
        $this->Cell(53,$h,'SERVICIO',0,0,'L');
        $this->Cell(4,$h,':',0,0,'C');
        $this->SetTextColor(0);
        $this->MultiCell(135,5,utf8_decode($servicio),'B','L');
        $this->Ln(2);

        $area = utf8_decode($data->area);
        $area_w = $this->GetStringWidth($area);
        $rows = ceil($area_w / (135 - $cellMargin));
        $h = 5 * $rows;
        $this->SetTextColor(62,105,167);
        $this->Cell(53,$h,'AREA',0,0,'L');
        $this->Cell(4,$h,':',0,0,'C');
        $this->SetTextColor(0);
        $this->MultiCell(135,5,utf8_decode($area),'B','L');
        $this->Ln(2);

        $cliente = utf8_decode($data->cliente);
        $cliente_w = $this->GetStringWidth($cliente);
        $rows = ceil($cliente_w / (135 - $cellMargin));
        $h = 5 * $rows;
        $this->SetTextColor(62,105,167);
        $this->Cell(53,$h,'CLIENTE',0,0,'L');
        $this->Cell(4,$h,':',0,0,'C');
        $this->SetTextColor(0);
        $this->MultiCell(135,5,utf8_decode($cliente),'B','L');
        $this->Ln(2);

        $this->SetTextColor(62,105,167);
        $this->Cell(53,7,'RUC',0,0,'L');
        $this->Cell(4,7,':',0,0,'C');
        $this->SetTextColor(0);
        $this->Cell(135,7,utf8_decode($data->ruc),'B',1,'L');
        $this->Ln(2);

        $this->SetTextColor(62,105,167);
        $this->Cell(53,7,'RESPONSABLE DEL SERVICIO',0,0,'L');
        $this->Cell(4,7,':',0,0,'C');
        $this->SetTextColor(0);
        $this->Cell(135,7,utf8_decode($data->responsable),'B',1,'L');
        $this->Ln(8);

        setlocale(LC_TIME, "es_ES");
        $mes = strftime("%B", strtotime($data->fechaservicio));
        $date = explode('-', $data->fechaservicio);
        $this->SetTextColor(62,105,167);
        $this->Cell(21,7,'En la fecha ',0,0,'L');
        $this->SetTextColor(0);
        $this->Cell(10,5,$date[2],'B',0,'C');
        $this->SetTextColor(62,105,167);
        $this->Cell(7,7,'de',0,0,'C');
        $this->SetTextColor(0);
        $this->Cell(70,5,strtoupper($mes),'B',0,'C');
        $this->SetTextColor(62,105,167);
        $this->Cell(4,7,',',0,0,'C');
        $this->SetTextColor(0);
        $this->Cell(40,5,$date[0],'B',0,'C');
        $this->SetTextColor(62,105,167);
        $this->Cell(60,7,', se ha constatado que el',0,1,'L');

        $this->Cell(21,7,'servicio de:',0,0,'L');
        $this->SetTextColor(0);
        $this->Cell(171,5,utf8_decode('LIMPIEZA INTEGRAL'),'B',1,'C');
        $this->Ln(3);

        $direccion = utf8_decode($data->ubicacion);
        $direccion_w = $this->GetStringWidth($direccion);
        $rows = ceil($direccion_w / (171 - $cellMargin));
        $h = 5 * $rows;
        $this->SetTextColor(62,105,167);
        $this->Cell(21,$h,'Ubicado en:',0,0,'L');
        $this->SetTextColor(0);
        $this->MultiCell(171,5,$direccion,'B','C');
        $this->Ln(3);

        $this->SetTextColor(62,105,167);
        $this->MultiCell(192,7,utf8_decode('Se ha concluido satisfactoriamente cumpliéndose con el presupuesto y las especificaciones del contrato inicial, por tanto se EMITE LA CONFORMIDAD DEL SERVICIO.'),0,'L');
        $this->Ln(29);

        $this->Cell(70,7,'','B',0,'L');
        $this->Cell(50,7,'',0,0,'L');
        $this->Cell(70,7,'','B',1,'L');

        $this->Cell(70,7,'NOMBRE:',0,0,'L');
        $this->Cell(50,7,'',0,0,'L');
        $this->Cell(70,7,'SUPERVISOR DE SERVICIO',0,1,'C');

        $this->Cell(70,6,'DNI:',0,1,'L');
        $this->Cell(12,6,'GIRO: ',0,0,'L');
        $this->SetTextColor(0);
        $this->Cell(70,6,utf8_decode($data->giro),0,1,'L');
        $this->SetTextColor(62,105,167);
        $this->Cell(27,6,'VENCIMIENTO: ',0,0,'L');
        $this->SetTextColor(0);
        $this->Cell(70,6,utf8_decode($data->vencimiento),0,1,'L');
        $this->SetTextColor(62,105,167);
        $this->Ln(9);

        
    }

	function setFooter()
	{
		$this->SetXY(11,255);
        $this->MultiCell(192,7,utf8_decode('Dirección: Calle Enrique Barrón Nº 1323, Urb. Santa Beatriz - Lima Teléfono: 472-5307'),0,'C');
        $this->SetX(11);
        $this->MultiCell(192,7,utf8_decode('Cel: 97536-5954'),0,'C');
        $this->SetX(11);
        $this->MultiCell(192,7,utf8_decode('Web: www.segelimpereu.com Email: servicioalcliente@segelimperu.com'),0,'C');
        $this->Image('assets/images/logo-grande.png',132,215,60);
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
