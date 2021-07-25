<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// require('Fpdf_gen.php');
require_once APPPATH.'third_party/fpdf181/fpdf.php';

class Reporte_recibo extends FPDF{

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
        $this->SetFont('Arial','B',18);
        $this->SetLineWidth(0.4);
        $this->SetFillColor(255);
        $this->SetDrawColor(39,153,8);
        $this->RoundedRect(13, 10, 185, 147, 3.5, 'DF');
		$this->Image('assets/images/logo_segel.png',30,12,85,27);
        $this->RoundedRect(125, 20, 68, 30, 3.5, 'DF');
        $this->SetFillColor(39,153,8);
        $this->Rect(125,29,68,12,'F');
        $this->SetTextColor(255);
        $this->Text(148,37,'RECIBO');
        $this->SetFont('Times','',18);
        $this->SetTextColor(255,0,0);
        $this->SetXY(125,40);
        $this->Cell(68,12,utf8_decode('Nº '). str_pad($id, 5, "0", STR_PAD_LEFT),0,1,'C');
        $this->SetXY(33,41);
        $this->SetTextColor(86,132,210);
        $this->SetFont('Arial','',9);
        $this->Cell(40,4,'Limpieza de alfombras',0,0,'C');
        $this->Cell(40,4,'Fumigaciones',0,1,'C');
        $this->SetX(33);
        $this->Cell(40,4,'Limpieza de muebles',0,0,'C');
        $this->Cell(40,4,'Limpieza en general',0,1,'C');
        $this->SetX(33);
        $this->Cell(80,4,utf8_decode('Nº de Cuenta BCP: 19329965788070'),0,1,'C');
        $this->SetX(33);
        $this->Cell(80,4,utf8_decode('Jr. Enrique Barron Nº 1323 Int. B-Urb. Santa Beatriz Lima-Lima-Lima'),0,1,'C');
        $this->SetX(33);
        $this->Cell(80,4,utf8_decode('Telf.: 471 8938 / Cel: 975 365 954'),0,1,'C');
		// $this->Image('assets/images/reporte_perfiles.png',139,22,50);
		$this->Ln(10);
	}

    public function setDatosRecibo($data)
    {
        $cellMargin = 2 * 1.000125;
        $this->SetFont('Arial','',12);
        $this->SetTextColor(64,117,204);
        $this->SetDrawColor(86,132,210);
	    $this->setX($this->margin_left);
	    $this->Cell(45, 8, 'Hemos recibido de:',0,0);
	    $this->Cell(126, 8, utf8_decode($data->client),0,1,'L');

        $this->setX($this->margin_left);
	    $this->Cell(45, 8, 'La suma de:',0,0);
        $this->Cell(126, 8, 'S/  ' . number_format($data->price, 2, ',', '.'),0,1,'L');
        
        $this->SetFont('Arial','',11);
        $y = $this->GetY();
        $this->setX($this->margin_left);
        $concepto = $data->concept;
        $width = $this->GetStringWidth($concepto);
        $rows = ceil($width / (126 - $cellMargin));
        $h = 6 * $rows;
        $this->MultiCell(45, $h, utf8_decode('Por concepto de:'), 0, 'L');
        $this->SetXY($this->margin_left + 45, $y);
        $this->MultiCell(126, 8, utf8_decode($concepto), 0, 'L');

        $this->SetFont('Arial','',12);

        $this->setX($this->margin_left);
	    $this->Cell(45, 8, 'Realizado en:',0,0);
        $this->Cell(126, 8, utf8_decode($data->made_in),0,1,'L');
        
        $this->setX($this->margin_left);
	    $this->Cell(45, 8, utf8_decode('Nº Acta Conformidad:'),0,0);
        $this->Cell(126, 8, utf8_decode($data->certificate_number),0,1,'L');
        $this->Ln(10);

        $this->setX($this->margin_left);
	    $this->Cell(45, 8, utf8_decode('Pague conforme'),0,0,'C');
        $this->Cell(10, 8, '',0,0,'C');
        $this->Cell(45, 8, utf8_decode('Recibí conforme'),0,1,'C');

        $this->setX($this->margin_left + 61);
        $this->Cell(45, 5, utf8_decode('Nombre:'),0,1,'L');
        $this->setX($this->margin_left + 61);
        $this->Cell(45, 5, utf8_decode('DNI:'),0,0,'L');


        $fecha_recibo = explode("-", $data->date_receipt);
        $this->Cell(65, 8, utf8_decode('Lima, ' . $fecha_recibo[2] . ' de ' . $fecha_recibo[1] . ' del ' . $fecha_recibo[0]),0,1,'R');
        
        $this->SetLineWidth(0.1);
        $this->SetDash(1,1); //5mm on, 5mm off

        $lineas = ($rows > 1 ) ? 7 : 6;
        for ($i=1; $i < $lineas; $i++) { 
            $this->Line(65,69 + ($i*8),190, 69 + ($i*8));
        }

        $h1 = ($rows > 1 ) ? 130 : 122;
        $this->Line($this->margin_left,$h1,65,$h1);
        $this->Line($this->margin_left + 57,$h1,118,$h1);
    }

	function setDatosFicha($codigo,$revision,$vigencia)
	{
	    $this->SetFont('Arial','B',15);
	    $this->setX($this->margin_left);
	    $this->Cell(35, 24, '',1,0);
	    $this->Cell(100, 12, 'FICHA DE DATOS PERSONALES','TBR',2,'C');
	    $this->Cell(100, 12, 'RECLUTAMIENTO','BR',0,'C');
	    $getY = $this->GetY();
	    $getX = $this->GetX();
	    $this->SetFont('Arial',null,9);
	    $this->SetXY($getX,$getY-12);
	    $this->Cell(35, 8, 'CODIGO: '.$codigo,'TR',2,'C');
	    $this->Cell(35, 8, 'REVISION: '.$revision,'R',2,'C');
	    $this->Cell(35, 8, 'VIGENCIA: '.$vigencia,'BR',0,'C');
	   	$this->SetMargins($this->margin_left,10); 
	    $this->Ln(10);
	}

	function setInstrucciones()
	{
	    $this->SetFont('Arial',null,10);
	    $this->Cell(0,13,'INSTRUCCIONES ANTES DE LLENAR',0,2);
	    $this->Cell(0,6,utf8_decode('1. Se debe completar los datos con letra imprenta y mayúscula.'),0,2);
	    $this->Cell(0,6,utf8_decode('2. Para completar los datos de las casillas se debe marcar con una X o un check.'),0,2);
	    $this->Cell(0,6,'3. El postulante se responsabiliza de llenar todos los campos correspondientes.',0,2);
	}

	function setDatosGenerale(
		$ap_paterno,$ap_materno,$nombres,$url_foto,$dni,$edad,
		$nacionalidad,$sexo,$email,$fecha_nacimiento,$departamento,
		$provincia,$distrito)
	{
	    $this->Ln(10);
	    $this->SetFillColor(187,214,238);
	    $this->Cell(130,9,' 1. DATOS GENERALES',1,2,'L',true);
	    $imgY = $this->GetY();
	    $imgX = $this->GetX();
	    $this->Ln(5);
	    $this->setX(25);
	    $this->Cell(40,9,'APELLIDO PATERNO:',0,2);
	    $this->Cell(40,9,'APELLIDO MATERNO:',0,2);
	    $this->Cell(40,9,'NOMBRES:',0,0);
	    $posY = $this->GetY();
	    $posX = $this->GetX();
	    $this->SetXY($posX,$posY-18);
	    // variables datos personales
	    $this->Cell(85,9,utf8_decode($ap_paterno),1,2);
	    $this->Cell(85,9,utf8_decode($ap_materno),1,2);
	    $this->Cell(85,9,utf8_decode($nombres),1,2);
	    // $this->Image('assets/images/reporte_foto_perfil.png',$imgX+140,$imgY-9,30);
	    if(!empty($url_foto)){
			$this->Image($url_foto,$imgX+137,$imgY-9,33,44);
		}
	    $this->Ln(8);
	    $this->setX(25);
	    $this->Cell(10,9,'DNI:',0,0);
	    $this->Cell(35,9,$dni,1,0);
	    // Espacio
	    $this->Cell(5,9,'',0,0);
	    $this->Cell(13,9,'EDAD:',0,0);
	    $this->Cell(20,9,$edad,1,0);
	    // Espacio
	    $this->Cell(5,9,'',0,0);
	    $this->Cell(30,9,'NACIONALIDAD:',0,0);
	    $this->Cell(47,9,$nacionalidad,1,0);

	    $this->Ln(17);
	    $this->setX(25);
	    $this->Cell(15,9,'SEXO:',0,0);
	    $this->Cell(20,9,$sexo,1,0);
	    // Espacio
	    $this->Cell(10,9,'',0,0);
	    $this->Cell(48,9,'CORREO ELECTRONICO:',0,0);
	    $this->Cell(72,9,utf8_decode($email),1,0);

	    $this->Ln(17);
	    $this->setX(25);
	    $this->Cell(44,9,'FECHA DE NACIMIENTO:',0,0);
	    $this->Cell(36,9,$fecha_nacimiento,1,0);
	    // Espacio
	    $this->Cell(2,9,'',0,0);
	    $this->Cell(33,9,'DEPARTAMENTO:',0,0);
	    $this->Cell(50,9,utf8_decode($departamento),1,1);
	    $this->setX(25);
	    $this->Cell(44,9,'PROVINCIA:',0,0);
	    $this->Cell(36,9,utf8_decode($provincia),1,0);
	    // Espacio
	    $this->Cell(2,9,'',0,0);
	    $this->Cell(33,9,'DISTRITO:',0,0);
	    $this->Cell(50,9,utf8_decode($distrito),1,1);
	    $this->Ln(10);
	}

	function setNivelEducativo($primaria,$secundaria,$superior,$carrera,$titulo)
	{
	    $this->SetFillColor(187,214,238);
	    $this->Cell(0,9,' 2. NIVEL EDUCATIVO',1,2,'L',true);
		$this->Ln(5);
		
		$this->Cell(50,11,'PRIMARIA:',1,0);	    
		$this->Cell(0,11,utf8_decode($primaria),1,2);
		$this->Ln(0);

		$this->Cell(50,11,'SECUNDARIA:',1,0);	    
		$this->Cell(0,11,utf8_decode($secundaria),1,2);
		$this->Ln(0);
		
		$this->Cell(50,7,'SUPERIOR:','LR',2);
		$this->SetFont('Arial',null,7);
		$this->Cell(50,4,'(TECNICO/UNIVERSITARIO):','LBR',0);
		$this->SetFont('Arial',null,10);
		$posY = $this->GetY();
		$this->SetY($posY-7);
		$this->SetX(70);
		$this->Cell(0,11,utf8_decode($superior),1,2);
		$this->Ln(0);

		$this->Cell(50,7,'CARRERA:','LR',2);
		$this->SetFont('Arial',null,7);
		$this->Cell(50,4,'(TECNICO/UNIVERSITARIO):','LBR',0);
		$this->SetFont('Arial',null,10);
		$posY = $this->GetY();
		$this->SetY($posY-7);
		$this->SetX(70);
		$this->Cell(0,11,utf8_decode($carrera),1,2);
		$this->Ln(0);

		$this->Cell(50,11,'TITULO OBTENIDO:',1,0);	    
		$this->Cell(0,11,utf8_decode($titulo),1,2);
		$this->Ln(0);
	}

	function setExperienciaLaboral($datos_experiencia)
	{
		$this->SetFont('Arial',null,9);
		$exp = json_decode($datos_experiencia);

	    $this->Ln(7);
	    $this->SetFillColor(187,214,238);
	    $this->Cell(0,9,' 3. EXPERIENCIA LABORAL',1,2,'L',true);
	    $this->Ln(5);

	    $this->Cell(35,8,'EMPRESA:','LT',0);
	    $this->Cell(47,8,utf8_decode($exp[0]->empresa),'LTR',0);
	    // Espacio
	    $this->Cell(5,9,'',0,0);
	    $this->Cell(35,8,'EMPRESA:','LT',0);
	    $this->Cell(47,8,utf8_decode($exp[1]->empresa),'LTR',2);
	    $this->Ln(0);

	    $this->Cell(35,8,'CARGO:','LT',0);
	    $this->Cell(47,8,utf8_decode($exp[0]->cargo),'LTR',0);
	    // Espacio
	    $this->Cell(5,9,'',0,0);
	    $this->Cell(35,8,'CARGO:','LT',0);
	    $this->Cell(47,8,utf8_decode($exp[1]->cargo),'LTR',2);
	    $this->Ln(0);

	    $this->Cell(35,8,'DURACION:','LT',0);
	    $this->Cell(47,8,utf8_decode($exp[0]->duracion),'LTR',0);
	    // Espacio
	    $this->Cell(5,9,'',0,0);
	    $this->Cell(35,8,'DURACION:','LT',0);
	    $this->Cell(47,8,utf8_decode($exp[1]->duracion),'LTR',2);
	    $this->Ln(0);

	    $this->Cell(35,8,'JEFE INMEDIATO:','LT',0);
	    $this->Cell(47,8,utf8_decode($exp[0]->jefe),'LTR',0);
	    // Espacio
	    $this->Cell(5,9,'',0,0);
	    $this->Cell(35,8,'JEFE INMEDIATO:','LT',0);
	    $this->Cell(47,8,utf8_decode($exp[1]->jefe),'LTR',2);
	    $this->Ln(0);

	    $this->Cell(35,8,'TELEFONO:','LT',0);
	    $this->Cell(47,8,utf8_decode($exp[0]->telefono),'LTR',0);
	    // Espacio
	    $this->Cell(5,9,'',0,0);
	    $this->Cell(35,8,'TELEFONO:','LT',0);
	    $this->Cell(47,8,utf8_decode($exp[1]->telefono),'LTR',2);
	    $this->Ln(0);

	    $this->Cell(35,8,'MOTIVO DE RETIRO:','LT',0);
	    $this->Cell(47,8,utf8_decode($exp[0]->motivoretiro),'LTR',0);
	    // Espacio
	    $this->Cell(5,9,'',0,0);
	    $this->Cell(35,8,'MOTIVO DE RETIRO:','LT',0);
	    $this->Cell(47,8,utf8_decode($exp[1]->motivoretiro),'LTR',2);
	    $this->Ln(0);

	    $this->Cell(35,8,'REMUNERACION:','LTB',0);
	    $this->Cell(47,8,utf8_decode($exp[0]->remuneracion),'LTRB',0);
	    // Espacio
	    $this->Cell(5,9,'',0,0);
	    $this->Cell(35,8,'REMUNERACION:','LTB',0);
	    $this->Cell(47,8,utf8_decode($exp[1]->remuneracion),'LTRB',2);
	    $this->Ln(10);
	}

	function setDatosConyugue($datos_conyugue)
	{
		$conyugue = json_decode($datos_conyugue);
	    $this->SetFillColor(187,214,238);
	    $this->Cell(0,9,' 4. DATOS DE CONYUGUE O CONVIVIENTE',1,2,'L',true);
	    $this->Ln(5);

	    $this->Cell(45,8,'VINCULO FAMILIAR:',0,0);
	    $this->Cell(40,8,$conyugue->vinculo,'LTRB',0);
	    // Espacio
	    $this->Cell(45,9,'',0,0);
	    $this->Cell(18,8,'SEXO:',0,0);
	    $this->Cell(20,8,$conyugue->sexo,1,2);
	    $this->Ln(10);

	    $this->Cell(50,9,'NOMBRES Y APELLIDOS:',0,0,'R');	    
		$this->Cell(0,9,$conyugue->nombrecompleto,1,2);
		$this->Ln(0);
		$this->Cell(50,9,'DNI:',0,0,'R');	    
		$this->Cell(0,9,$conyugue->dni,1,2);
		$this->Ln(0);
		$this->Cell(50,9,'FECHA DE NACIMIENTO:',0,0,'R');	    
		$this->Cell(0,9,$conyugue->fecha_nacimiento,1,2);
		$this->Ln(0);
		$this->Cell(50,9,'NACIONALIDAD:',0,0,'R');	    
		$this->Cell(0,9,$conyugue->nacionalidad,1,2);
		$this->Ln(0);
		$this->Cell(50,9,'TELEFONO:',0,0,'R');	    
		$this->Cell(0,9,$conyugue->telefono,1,2);
		$this->Ln(0);
		$this->Cell(50,9,'OCUPACION:',0,0,'R');	    
		$this->Cell(0,9,$conyugue->ocupacion,1,2);
		$this->Ln(0);
		$this->Cell(50,9,'LUGAR DE TRABAJO:',0,0,'R');	    
		$this->Cell(0,9,$conyugue->lugartrabajo,1,2);
		$this->Ln(10);
	}

	function setDatosHijos($datos_hijos)
	{
		$hijos = json_decode($datos_hijos);
	    $this->SetFillColor(187,214,238);
	    $this->Cell(0,9,' 5. DATOS DE LOS HIJOS',1,2,'L',true);
	    $this->Ln(5);

	    // Cabecera
	    $this->SetFont('Arial','B',8);

	    $this->Cell(6,12,'Nr','LTB',0,'C',0);
	    $this->Cell(37,6,'APELLIDOS Y','LT',2);
		$this->Cell(37,6,'NOMBRES','LB',0);
		$y = $this->GetY()-6;
		$x = $this->GetX(); 
		$this->SetXY($x,$y);
		$this->Cell(21,6,'FECHA DE','LT',2);
		$this->Cell(21,6,'NACIMIENTO','LB',0);
		$y = $this->GetY()-6;
		$x = $this->GetX(); 
		$this->SetXY($x,$y);
		$this->Cell(20,12,'DNI','LTB',0,'C',0);
		$this->Cell(22,6,'GRADO DE','LT',2);
		$this->Cell(22,6,'INSTRUCCION','LB',0);
		$y = $this->GetY()-6;
		$x = $this->GetX(); 
		$this->SetXY($x,$y);
		$this->Cell(27,12,'OCUPACION','LTB',0,'C',0);
		$this->Cell(15,6,'VIVE CON','LT',2,'C');
		$this->Cell(15,6,'USTED','LB',0,'C');
		$y = $this->GetY()-6;
		$x = $this->GetX(); 
		$this->SetXY($x,$y);
		$this->Cell(22,6,'ASEGURADO','LTR',2,'C');
		$this->Cell(22,6,'EN ESSALUD','LBR',0,'C');
		$y = $this->GetY()-6;
		$x = $this->GetX(); 
		$this->SetXY($x,$y);
		$this->Ln(12);

		$this->SetFont('Arial','',8);

		$w = [6,37,21,20,22,27,15,22];
		$x = $this->GetX(); //20

		foreach ($hijos as $key=>$hijo)
		{
			$h = $this->calculateLineH($hijo,$w); // 12
			$i = 1;
			
			$x = $this->GetX();
	        $y = $this->GetY();			
	        //Draw the border
	        $this->Rect($x,$y,$w[0],$h);
			$this->MultiCell($w[0],6,$key+1,0,'C');
			$this->SetXY($x+$w[0],$y);

			foreach ($hijo as $prop) {
				$width = $w[$i];

				$x = $this->GetX();
	        	$y = $this->GetY();
	        	$a = 'C';
	        	$a= ($i==1) ? 'L' : 'C';
	        	//Draw the border
	        	$this->Rect($x,$y,$width,$h);

				$this->MultiCell($width,6,utf8_decode($prop),0,$a);

	        	$this->SetXY($x+$width,$y);
	        	$i+=1;
			}
			$this->Ln($h);
		}
	    $this->Ln(7);
	}

	function setInformacionAdicional
		(
			$antencedentes,$sindicato,$emergencias,$nroEmergencias,
			$direccion,$padre_nombres,$padre_ocupacion,$madre_nombres,$madre_ocupacion
		)
	{
	    $this->SetFont('Arial',null,9);
	    $this->SetFillColor(187,214,238);
	    $this->Cell(0,9,' 6. INFORMACION ADICIONAL',1,2,'L',true);
	    $this->Ln(5);

	    $this->Cell(56,9,'ANTECEDENTES POLICIALES:',0,0);	    
		$this->Cell(13,9,$antencedentes,1,2);
		$this->Ln(5);

		$this->SetFont('Arial',null,9);
		$this->Cell(75,9,utf8_decode('¿A QUE SINDICATO SE ENCUENTRA AFILIADO?'),1,0,'R');	    
		$this->Cell(0,9,$sindicato,1,2);
		$this->Ln(0);
		$this->Cell(75,9,'EN CASO DE EMERGENCIA LLAMAR A',1,0,'R');	    
		$this->Cell(0,9,utf8_decode($emergencias),1,2);
		$this->Ln(0);
		$this->Cell(75,9,'TELEFONO/CELULAR',1,0,'R');	    
		$this->Cell(0,9,$nroEmergencias,1,2);
		$this->Ln(0);
		$this->Cell(75,9,'DIRECCION',1,0,'R');	    
		$this->Cell(0,9,utf8_decode($direccion),1,2);
		$this->Ln(5);

		$this->SetFont('Arial','B',9);
		$this->Cell(56,9,'INFORMACION DE LOS PADRES',0,2);	    
		$this->Ln(4);
		$this->SetFont('Arial',null,9);
		$this->Cell(63,9,'NOMBRES Y APELLIDOS DEL PADRE',1,0,'R');	    
		$this->Cell(0,9,utf8_decode($padre_nombres),1,2);
		$this->Ln(0);
		$this->Cell(63,9,'OCUPACION',1,0,'R');	    
		$this->Cell(0,9,utf8_decode($padre_ocupacion),1,2);
		$this->Ln(7);
		$this->Cell(63,9,'NOMBRES Y APELLIDOS DE LA MADRE',1,0,'R');	    
		$this->Cell(0,9,utf8_decode($madre_nombres),1,2);
		$this->Ln(0);
		$this->Cell(63,9,'OCUPACION',1,0,'R');	    
		$this->Cell(0,9,utf8_decode($madre_ocupacion),1,2);
		$this->Ln(6);
	}

	function setUbicacionDomicilio($direccion_actual,$telefono_casa,$distrito,$provincia,$departamento,$url_foto_direccion)
	{
		$this->SetFont('Arial',null,9);
	    $this->SetFillColor(187,214,238);
	    $this->Cell(0,9,' 7. UBICACION DE DOMICILIO',1,2,'L',true);
	    $this->Ln(5);

	    $this->Cell(63,7,'DIRECCION ACTUAL',1,0,'L');	    
		$this->Cell(0,7,utf8_decode($direccion_actual),1,2);
		$this->Ln(0);
		$this->Cell(63,7,'TELEFONO/CELULAR DE LA CASA',1,0,'L');	    
		$this->Cell(0,7,utf8_decode($telefono_casa),1,2);
		$this->Ln(0);
		$this->Cell(63,7,'DISTRITO',1,0,'L');	    
		$this->Cell(0,7,utf8_decode($distrito),1,2);
		$this->Ln(0);
		$this->Cell(63,7,'PROVINCIA',1,0,'L');	    
		$this->Cell(0,7,utf8_decode($provincia),1,2);
		$this->Ln(0);
		$this->Cell(63,7,'DEPARTAMENTO',1,0,'L');	    
		$this->Cell(0,7,utf8_decode($departamento),1,2);
		$this->Ln(10);
		$x = $this->GetX();
		$y = $this->GetY();
		// $this->Image('assets/images/reporte_foto_direccion.png',$x,$y,170);
		if(!empty($url_foto_direccion)){
			$this->Image($url_foto_direccion,$x+6,$y,158,53);
		}
	}

	function setMedidasIndumentaria($talla_camisa,$talla_pantalon,$talla_calzado)
	{
	    $this->Ln(7);
	    $this->SetFont('Arial',null,9);
	    $this->SetFillColor(187,214,238);
	    $this->Cell(0,9,' 8. MEDIDAS PARA INDUMENTARIA',1,2,'L',true);
	    $this->SetMargins(60,10);
	    $this->Ln(7);

	    $this->Cell(40,7,'TALLA DE CAMISA',1,0,'L');	    
		$this->Cell(40,7,$talla_camisa,1,2,'C');
		$this->Ln(0);
		$this->Cell(40,7,'TALLA DE PANTALON',1,0,'L');	    
		$this->Cell(40,7,$talla_pantalon,1,2,'C');
		$this->Ln(0);
		$this->Cell(40,7,'TALLA DE CALZADO',1,0,'L');	    
		$this->Cell(40,7,$talla_calzado,1,2,'C');
		$this->Ln(0);
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

	function setFicha2Header()
	{
		$this->SetFont('Arial',null,9);
		$this->MultiCell(0,8,utf8_decode('LEY N° 28882'),0,'C');
		$this->MultiCell(0,8,utf8_decode('LEY DE SIMPLIFICACIÓN DE LA CERTIFICACIÓN DOMICILIARIA'),0,'C');
		$this->SetFont('Arial','UB',9);
		$this->MultiCell(0,8,utf8_decode('DECLARACIÓN JURADA DE DOMICILIO'),0,'C');
		$this->SetFont('Arial',null,9);
		$this->MultiCell(0,8,utf8_decode('(Ley de Derogación de Atribución de la PNP a expedir "Certificados Domiciliarios")'),0,'C');
		$this->MultiCell(0,8,utf8_decode('(Ley de Procedimientos Administrativos N° 27444)'),0,'C');
		$this->SetMargins($this->margin_left,10);
	}

	function setBodyFicha2($nombre,$edad,$tipo_documento,$nro_documento,$direccion,$distrito,$provincia,$departamento,$url_foto_firma, $fecha_ingreso)
	{
		$this->SetMargins(24,10,24);
		$this->Ln(5);
		$this->SetFont('Arial',null,10);
		$this->WriteHTML(utf8_decode('Yo, '.$nombre.' de '.$edad.' años de edad, identificado con '.$tipo_documento.' Nro. '.$nro_documento.', en pleno ejercicio de mis Derechos de Ciudadano y Conformidad con lo dispuesto en la Ley de Procedimientos Administrativos Nro. 27444. <b>DECLARO BAJO JURAMENTO:</b> que mi domicilio actualmente se encuentra ubicado en '.$direccion.', Distrito de '.$distrito.' Provincia de '.$provincia.', Departamento de '.$departamento.'.'));
		$this->Ln(8);
		$this->WriteHTML(utf8_decode('Realizo la presente declaración jurada manifestando que la información proporcionada es verdadera y autorizo la verificación de lo declarado.'));
		$this->Ln(8);
		$this->WriteHTML(utf8_decode('En caso de falsedad declaro haber incurrido en el delito Contra la Fe Pública, falsificación de Documentos (Artículo 427 del Código Penal, en concordancia con el Artículo IV inciso 1.7) "Principio de Presunción de Veracidad" del Título Preliminar de la Ley de Procedimientos Administrativo General, Ley Nro. 27444.'));
		$this->Ln(8);
		$this->WriteHTML(utf8_decode('Formulo la siguiente declaración jurada para los fines legales de trabajo.'));
		$this->Ln(8);
		$this->WriteHTML(utf8_decode('Para mayor validez y cumplimiento firmo e imprimo mi huella digital al pie del presente documento para fines legales correspondientes.'));

		$this->Ln(18);
		$this->Cell(0,6,'Lima, '.$fecha_ingreso);
		$x = $this->GetX();
		$y = $this->Gety();
		$this->Ln(52);
		if(!empty($url_foto_firma)){
			$this->Image($url_foto_firma,47,$y+10,120,48);
		}
		// $this->Cell(0,6,'FIRMA Y HUELLA DACTILAR',0,0,'C');
	}

	function setFicha4Header()
	{
		$this->Ln(10);
		$this->SetFont('Arial','UB',10);
		$this->MultiCell(0,8,utf8_decode('DECLARACIÓN JURADA DE CONFIDENCIALIDAD'),0,'C');
	}

	public function setBodyFicha4($nombre,$edad,$tipo_documento,$nro_documento,$nacionalidad,$url_foto_firma, $fecha_ingreso)
	{
		$this->SetMargins(24,10,24);
		$this->Ln(12);
		$this->SetFont('Arial',null,10);
		$this->WriteHTML(utf8_decode('Conste <b>por el presente</b> documento; al que brindo mayor fuerza legal;'));
		$this->Ln(8);

		$this->WriteHTML(utf8_decode('Yo, ' . $nombre . ' de ' . $edad . ' años de edad, de nacionalidad ' . $nacionalidad . '; identificado con ' . $tipo_documento . ' Nº ' . $nro_documento . ' a quien en adelante se le conocerá como <b>EL TRABAJADOR</b> declaro bajo juramento tener pleno conocimiento y aceptar lo que se indica:'));
		$this->Ln(8);

		$this->WriteHTML(utf8_decode('Toda la información que maneje <b>EL TRABAJADOR</b> para efectos de realizar sus labores tales como la base de datos de clientes, perfiles de clientes, manejos comerciales, movimientos, estrategias comerciales, políticas comerciales y planeamiento estratégico , será de estricto confidenciales, no pudiendo divulgarla a personal de <b>LA EMPRESA</b> o ajenas a ella. Esta prohibición se extiende inclusive luego de finalizado el contrato de trabajo. La violación a esta norma acarreará las sanciones correspondientes, sean laborales, civiles y/o penales.'));
		$this->Ln(8);

		$this->WriteHTML(utf8_decode('Las condiciones contractuales, remunerativas y/o de beneficios laborales, deberán permanecer en estricto confidenciales, constituyendo una <b>FALTA GRAVE</b> el compartir dicha información con algún compañero de trabajo o que mediante terceros se haga conocer estas tratativas.'));
		$this->Ln(8);

		$this->WriteHTML(utf8_decode('Para mayor constancia y validez y en cumplimiento firmo y pongo mi huella digital al pie del presente documento para fines legales correspondientes.'));
		$this->Ln(18);
		
		$this->Cell(0,6,'Lima, '. $fecha_ingreso);
		$x = $this->GetX();
		$y = $this->Gety();
		$this->Ln(52);
		if(!empty($url_foto_firma)){
			$this->Image($url_foto_firma,47,$y+10, 120, 48);
		}
		// $this->Cell(0,6,'FIRMA Y HUELLA DACTILAR',0,0,'C');
	}

	function setFicha3Header($tipo_documento,$nro_documento,$edad,$telefono,$nombres)
	{
		$this->SetMargins(24, 10, 24);
		$this->Ln(5);
		$this->SetFont('Arial', 'B', 13);
		$this->MultiCell(0, 6, utf8_decode('DECLARACIÓN DE SALUD'), 0, 'C');
		$this->SetFont('Arial', null, 10);
		$this->MultiCell(0, 6, utf8_decode('Nombres y Apellidos: '.$nombres), 0, 'C');
		$this->Cell(54, 6, utf8_decode($tipo_documento.' Nº: '.$nro_documento), 0, 0,'C');
		$this->Cell(54, 6, utf8_decode(' Edad: ' . $edad . ' años'), 0, 0, 'C');
		$this->Cell(54, 6, utf8_decode(' Telefono/Celular: ' . $telefono), 0, 2, 'C');
		$this->Ln(0);
	}

	function setBodyFicha3($url_foto_firma, $fecha_ingreso)
	{
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(136,10, utf8_decode('1. ¿Ha padecido o padece alguna de las enfermedades descritas a continuación?'), 0, 0, 'L');
		$this->Cell(13,10,'SI',0,0,'C');
		$this->Cell(13, 10, 'NO', 0, 2, 'C');
		$this->Ln(0);

		$this->SetFont('Arial', null, 9);
		$this->Cell(10, 12,'01',1,0,'C');
		$this->Cell(126, 12, utf8_decode('Cáncer, tumores, desorden glandular, quistes , anemia, enfermedades de sangre'), 1, 0, 'L');
		$this->Cell(13, 12, '', 1, 0, 'C');
		$this->Cell(13, 12, 'X', 1, 2, 'C');
		$this->Ln(0);

		$this->Cell(10, 12, '02', 1, 0, 'C');
		$x = $this->GetX(); $y = $this->GetY();
		$this->MultiCell(126, 6, utf8_decode('Trastornos cardiacos o circulatorios, dolores en el pecho, presión alta, dificultad en la respiración o fiebre reumática'), 1, 'L');
		$this->SetXY($x+126,$y);
		$this->Cell(13, 12, '', 1, 0, 'C');
		$this->Cell(13, 12, 'X', 1, 2, 'C');
		$this->Ln(0);

		$this->Cell(10, 12, '03', 1, 0, 'C');
		$x = $this->GetX(); $y = $this->GetY();
		$this->MultiCell(126, 6, utf8_decode('Aneurisma u otras enfermedades del cerebro o del sistema nervioso, desmayo, epilepsia, convulsiones o parálisis, enfermedades a los ojos, oídos, nariz, boca'), 1, 'L');
		$this->SetXY($x + 126, $y);
		$this->Cell(13, 12, '', 1, 0, 'C');
		$this->Cell(13, 12, 'X', 1, 2, 'C');
		$this->Ln(0);

		$this->Cell(10, 12, '04', 1, 0, 'C');
		$this->Cell(126, 12, utf8_decode('Afección pulmonar, asma, enfermedades de la piel, tuberculosis, tiroides'), 1, 0, 'L');
		$this->Cell(13, 12, '', 1, 0, 'C');
		$this->Cell(13, 12, 'X', 1, 2, 'C');
		$this->Ln(0);

		$this->Cell(10, 12, '05', 1, 0, 'C');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->MultiCell(126, 6, utf8_decode('Enfermedad a los huesos, articulaciones, músculos, espalda, artritis, reumatismo, gota, amputaciones o deformación de algún miembro.'), 1, 'L');
		$this->SetXY($x + 126, $y);
		$this->Cell(13, 12, '', 1, 0, 'C');
		$this->Cell(13, 12, 'X', 1, 2, 'C');
		$this->Ln(0);

		$this->Cell(10, 12, '06', 1, 0, 'C');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->MultiCell(126, 6, utf8_decode('Trastorno de los riñones, del sistema urinario, próstata, sífilis, diabetes azúcar, o albumina en la orina, del recto o colon.'), 1, 'L');
		$this->SetXY($x + 126, $y);
		$this->Cell(13, 12, '', 1, 0, 'C');
		$this->Cell(13, 12, 'X', 1, 2, 'C');
		$this->Ln(0);

		$this->Cell(10, 12, '07', 1, 0, 'C');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->MultiCell(126, 6, utf8_decode('Afecciones estomacales, ulceras intestinales, enfermedades del hígado, vesícula o páncreas, cálculos, colitis, cirrosis.'), 1, 'L');
		$this->SetXY($x + 126, $y);
		$this->Cell(13, 12, '', 1, 0, 'C');
		$this->Cell(13, 12, 'X', 1, 2, 'C');
		$this->Ln(0);

		$this->Cell(10, 12, '08', 1, 0, 'C');
		$this->Cell(126, 12, utf8_decode('Enfermedad mental o nerviosa, adicción a drogas o alcoholismo'), 1, 0, 'L');
		$this->Cell(13, 12, '', 1, 0, 'C');
		$this->Cell(13, 12, 'X', 1, 2, 'C');
		$this->Ln(0);

		$this->Cell(10, 12, '09', 1, 0, 'C');
		$this->Cell(126, 12, utf8_decode('Tiene VIH y es tratado con TARGA'), 1, 0, 'L');
		$this->Cell(13, 12, '', 1, 0, 'C');
		$this->Cell(13, 12, 'X', 1, 2, 'C');
		$this->Ln(0);

		$this->Cell(10, 12, '10', 1, 0, 'C');
		$this->Cell(126, 12, utf8_decode('Alguna enfermedad congénita y otra dolencia no indicada anteriormente'), 1, 0, 'L');
		$this->Cell(13, 12, '', 1, 0, 'C');
		$this->Cell(13, 12, 'X', 1, 2, 'C');
		$this->Ln(0);

		$this->Cell(10, 12, '10', 1, 0, 'C');
		$this->Cell(126, 12, utf8_decode('Se encuentra en proceso de GESTACIÓN'), 1, 0, 'L');
		$this->Cell(13, 12, '', 1, 0, 'C');
		$this->Cell(13, 12, 'X', 1, 2, 'C');
		$this->Ln(0);
		$this->Cell(0,6,utf8_decode('Marque con una ASPA "X"; en cada casilla según corresponda "SI" o "NO"'),0,2,'C');
		$this->Ln(0);

		$this->SetFont('Arial', 'B', 9);
		$this->Cell(136, 10, utf8_decode('2. En los últimos tres años usted ha requerido de los servicios indicados a continuación:'), 0, 0, 'L');
		$this->Cell(13, 10, 'SI', 0, 0, 'C');
		$this->Cell(13, 10, 'NO', 0, 2, 'C');
		$this->Ln(0);

		$this->SetFont('Arial', null, 9);

		$this->Cell(10, 6, '01', 1, 0, 'C');
		$this->Cell(126, 6, utf8_decode('Una intervención o tratamiento quirúrgico'), 1, 0, 'L');
		$this->Cell(13, 6, '', 1, 0, 'C');
		$this->Cell(13, 6, 'X', 1, 2, 'C');
		$this->Ln(0);

		$this->Cell(10, 6, '02', 1, 0, 'C');
		$this->Cell(126, 6, utf8_decode('Ser internado en una clínica y hospital'), 1, 0, 'L');
		$this->Cell(13, 6, '', 1, 0, 'C');
		$this->Cell(13, 6, 'X', 1, 2, 'C');
		$this->Ln(0);

		$this->Cell(10, 6, '03', 1, 0, 'C');
		$this->Cell(126, 6, utf8_decode('Exámenes médicos o especializadas'), 1, 0, 'L');
		$this->Cell(13, 6, '', 1, 0, 'C');
		$this->Cell(13, 6, 'X', 1, 2, 'C');
		$this->Ln(0);

		$this->Cell(10, 6, '04', 1, 0, 'C');
		$this->Cell(126, 6, utf8_decode('Tomar algún medicamento, en períodos prolongados'), 1, 0, 'L');
		$this->Cell(13, 6, '', 1, 0, 'C');
		$this->Cell(13, 6, 'X', 1, 2, 'C');
		$this->Ln(0);

		$this->Cell(10, 6, '05', 1, 0, 'C');
		$this->Cell(126, 6, utf8_decode('Tratamiento psiquiátrico'), 1, 0, 'L');
		$this->Cell(13, 6, '', 1, 0, 'C');
		$this->Cell(13, 6, 'X', 1, 2, 'C');
		$this->Ln(1);

		$this->MultiCell(0,5,utf8_decode('Realizo la presente declaración manifestando que la información proporcionada es verdadera y autorizo la verificación de lo declarado.'), 0, 'L');
		$this->Ln(3);

		$this->SetFont('Arial', null, 10);
		$this->Cell(0, 6, 'Lima, ' . $fecha_ingreso);
		$x = $this->GetX();
		$y = $this->Gety();
		$this->Ln(34);
		if(!empty($url_foto_firma)){
			$this->Image($url_foto_firma, 53, $y, 120, 48);
		}
		// $this->Cell(0, 4, 'FIRMA Y HUELLA DACTILAR', 0, 0, 'C');
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
