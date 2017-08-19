<?php

class PDFController extends BaseController {

	public function turn_dates($date){
		$date = new DateTime($date);
		$date->setTimezone(new DateTimeZone('America/Santiago'));
		return $date->format('j F, Y, g:i a');
	}
	public function exportToPdf()
	{
		$m = new MongoClient();//obsoleta desde mongo 1.0.0
		$db = $m->SenditForm;
		$collW = $db->Works;
		$docRepor = $collW->find();
		$docRepor = $docRepor->sort(['Dsr' => 1]);

		$collf = $db->works_filter;
		$fixData = $collf->find();
		$seg = iterator_to_array($fixData,false);
		$pdf = new Fpdf();
		$pdf::AddPage('P','A3');
		$pdf::SetFont('Arial','B',11);
		$pdf::SetY(10);//posicion inicial eje y
		$pdf::Ln();

		/**LOGOS**/

		//FILA 1
		$pdf::SetX(7);
		$pdf::Cell(285,7,utf8_decode("Fabricación, Montaje, Mantención y Reparación"),1,0,'C');
		$pdf::Image('/var/www/senditlaravel42/public/photos/logos/cmpc.png',17.5,11,0,19,'PNG');
		$pdf::Image('/var/www/senditlaravel42/public/photos/logos/rudel.jpg',245.5,11.5,42,0,'JPG');
		$pdf::Ln();
		//FILA 2
		$pdf::SetX(7);
		$pdf::Cell(285,7,utf8_decode("Productos Metálicos de uso estructural y Obras Civiles"),1,0,'C');
		$pdf::Ln();
		//Fila3
		$pdf::SetX(7);
		$pdf::SetTextColor(182,44,44);
		$pdf::Cell(285,7,'WWW.RUDEL.CL',1,0,'C');
		$pdf::SetTextColor(0);
		//$pdf::Cell(49,7,'',1,0,'C');
		$pdf::Ln();
		//Fila4
		$pdf::SetX(7);
		$pdf::Cell(285,5,'',1,0,'C');

		$pdf::Ln();
		/**DATOS FIJOS*/

		//Titulos

		$pdf::SetX(7);
		$pdf::SetFillColor(145,210,91);
		$pdf::Cell(43,9,'codigo',1,0,'C',true);
		$pdf::Cell(154.5,9,utf8_decode("Ubicacion"),1,0,'C',true);
		$pdf::Cell(87.5,9,'Cambio 60cu Caps y 47cu Recapes',1,0,'C',true);
		$pdf::Ln();

		//Fila 1

		$pdf::SetX(7);
		$loc = utf8_decode($seg[0]['Loc']);
		$std = $seg[0]['Std'];

		$pdf::Cell(43,10,utf8_decode("Ubicación del Equipo:"),1,0,'C');
		$pdf::Cell(99.5,9,$loc,'L',0,'C');
		$pdf::Cell(55,5,utf8_decode("Supervisor Turno Día:"),1,0,'C');
		$pdf::Cell(87.5,5,$std,1,0,'C');
		$pdf::Ln();
		//fILA 2
		$pdf::SetX(7);
		$stn = $seg[0]['Stn'];
		$pdf::Cell(142.5,5,'',0,0,'C');
		$pdf::Cell(55,5,'Supervisores Turno Noche:',1,0,'C');
		$pdf::Cell(87.5,5,$stn,1,0,'C');
		$pdf::Ln();
		//fILA 3
		$pdf::SetX(7);
		$blk = $seg[0]['Blk'];
		$itd = $seg[0]['Itd'];
		$pdf::Cell(43,10,'Sistema Bloqueado:',1,0,'C');
		$pdf::Cell(99.5,10,$blk,1,0,'C');
		$pdf::Cell(55,5,utf8_decode("Ito Planta Turno Día:"),1,0,'C');
		$pdf::Cell(87.5,5,$itd,1,0,'C');
		$pdf::Ln();
		//fila 4
		$pdf::SetX(7);
		$itn = $seg[0]['Itn'];
		$pdf::Cell(142.5,5,'',0,0,'C');
		$pdf::Cell(55,5,'Ito Planta Turno Noche:',1,0,'C');
		$pdf::Cell(87.5,5,$itn,1,0,'C');
		$pdf::Ln();

		//***Horas Datos Fijos***//

		//Titulos Horas

		$pdf::SetX(7);
		$pdf::SetFillColor(145,210,91);
		$pdf::Cell(142.5,5,'Horas Programadas',1,0,'C',true);
		$pdf::Cell(142.5,5,'Horas Reales',1,0,'C',true);
		$pdf::Ln();
		//fila 1

		$pdf::SetX(7);
		$dsp = $seg[0]['Dsp'];

		$pdf::Cell(45,5,'Inicio:',1,0,'L');
		$pdf::Cell(97.5,5,$dsp,1,0,'C');
		$pdf::Cell(55,5,'Hora Inicio:',1,0,'C');
		$pdf::Cell(87.5,5,'',1,0,'C');
		$pdf::Ln();

		//fila 2

		$pdf::SetX(7);
		$dep = $seg[0]['Dep'];

		$pdf::Cell(45,5,utf8_decode("Término:"),1,0,'L');
		$pdf::Cell(97.5,5,$dep,1,0,'C');
		$pdf::Cell(55,5,utf8_decode("Hora Término:"),1,0,'C');
		$pdf::Cell(87.5,5,'',1,0,'C');
		$pdf::Ln();

		//fila 3

		$pdf::SetX(7);
		$hp = $seg[0]['Hp'];

		$pdf::Cell(45,5,'Horas Programadas:',1,0,'L');
		$pdf::Cell(97.5,5,$hp,1,0,'C');
		$pdf::Cell(55,5,'Tiempo Real:',1,0,'C');
		$pdf::Cell(87.5,5,'',1,0,'C');
		$pdf::Ln();

		/** Imprimiendo Trabajos**/

		//Titulo

		$pdf::SetX(7);
		$pdf::SetFillColor(145,210,91);
		$pdf::Cell(285,9,'Itemizado Trabajos',1,1,'C',true);

		//Header Tabla

		$pdf::SetX(7);
		$header = [utf8_decode("N°"),'Detalle Actividad','Inicio','Termino','Avance'];
		$w = array(10, 140, 45, 45,45);//anchuras de las columnas
		//Colores, ancho de línea y fuente en negrita
		$pdf::SetFillColor(255,255,255);
		$pdf::SetTextColor(0);
		$pdf::SetDrawColor(0,0,0);
		$pdf::SetLineWidth(.3);
		$pdf::SetFont('','B');
		for ($i=0; $i <count($header) ; $i++) {
			$pdf::Cell($w[$i],7,$header[$i],1,0,'C',true);
		}
		$pdf::Ln();


		//*****Itemizado*****//

		//Primer Trabajo

		//Restauración de colores y fuentes
		//$pdf::SetX(6);
		$pdf::SetFillColor(224,235,255);
		$pdf::SetTextColor(0);
		$pdf::SetFont('');

		foreach ($docRepor as $key => $v) {
			$pdf::SetX(7);
			$pdf::Cell(150,10,$v['Subwork'],1,'C');
			$pdf::Cell(45,10,$this->turn_dates($v['Dsr']),1,'C');
			$pdf::Cell(45,10,$this->turn_dates($v['Der']),1,'C');
			$pdf::Cell(45,10,$v['Poop'],1,'R');
			$pdf::Ln();

		}
		$j=0;
		//genero tablas vacias para el alto del reporte
		while ($j <= 3) {
			$pdf::SetX(7);
			$pdf::Cell(150,8,'',1,'C');
			$pdf::Cell(45,8,'',1,'C');
			$pdf::Cell(45,8,'',1,'C');
			$pdf::Cell(45,8,'',1,'R');
			$pdf::Ln();
			$j++;
		}
		//***Registro fotografico***//

		//Header

		$pdf::SetX(7);
		$pdf::SetFont('Arial','B',11);
		$pdf::SetFillColor(145,210,91);
		$pdf::SetTextColor(0);
		$pdf::Cell(285,5,'REGISTRO FOTOGRAFICO',1,0,'C',true);
		$pdf::Ln();

		$photos = iterator_to_array($docRepor,false);

		switch (count($photos)) {
			case 1:
				$pdf = new Fpdf();
				$pdf::SetX(7);
				$l1 = ($photos[0]['Photo']!=null) ? $photos[0]['Leyend'] : "" ;
				$desc = ['Foto 1:',$l1,'Foto 2:','','Foto 3:',''];
				$w =[15,80,15,80,15,80];
				for ($i=0; $i <count($desc) ; $i++) {
					$pdf::Cell($w[$i],5,$desc[$i],1,0,'C',false);
				}
				$pdf::Ln();
				//Borde Fotos

				$pdf::SetX(7);
				$bFoto = ['','',''];
				$w =[95,95,95];
				for ($i=0; $i <count($bFoto) ; $i++) {
					$pdf::Cell($w[$i],56,$bFoto[$i],1,0,'C',false);
				}
				$pdf::Ln();
				//Fotos SEGUNDA fila

				//Leyendas

				$pdf::SetX(7);

				$desc = ['Foto 4:','','Foto 5:','','Foto 6:',''];
				$w =[15,80,15,80,15,80];
				for ($i=0; $i <count($desc) ; $i++) {
					$pdf::Cell($w[$i],5,$desc[$i],1,0,'C',false);
				}
				$pdf::Ln();
				//Borde Fotos

				$pdf::SetX(7);
				$bFoto = ['','',''];
				$w =[95,95,95];
				for ($i=0; $i <count($bFoto) ; $i++) {
					$pdf::Cell($w[$i],56,$bFoto[$i],1,0,'C',false);
				}
				//Foto 1
				if ($photos[0]['Photo']!=null) {
					$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
					$pdf::Image($foto1,7.5,153.5,94,55);
				}
			break;
			case 2:
				$pdf = new Fpdf();
				$pdf::SetX(7);
				$l1 = ($photos[0]['Photo']!=null) ? $photos[0]['Leyend'] : "" ;
				$l2 = ($photos[1]['Photo']!=null) ? $photos[1]['Leyend'] : "" ;
				$desc = ['Foto 1:',$l1,'Foto 2:',$l2,'Foto 3:',''];
				$w =[15,80,15,80,15,80];
				for ($i=0; $i <count($desc) ; $i++) {
					$pdf::Cell($w[$i],5,$desc[$i],1,0,'C',false);
				}
				$pdf::Ln();
				//Borde Fotos

				$pdf::SetX(7);
				$bFoto = ['','',''];
				$w =[95,95,95];
				for ($i=0; $i <count($bFoto) ; $i++) {
					$pdf::Cell($w[$i],56,$bFoto[$i],1,0,'C',false);
				}
				$pdf::Ln();
				//Fotos SEGUNDA fila

				//Leyendas

				$pdf::SetX(7);

				$desc = ['Foto 4:','','Foto 5:','','Foto 6:',''];
				$w =[15,80,15,80,15,80];
				for ($i=0; $i <count($desc) ; $i++) {
					$pdf::Cell($w[$i],5,$desc[$i],1,0,'C',false);
				}
				$pdf::Ln();
				//Borde Fotos

				$pdf::SetX(7);
				$bFoto = ['','',''];
				$w =[95,95,95];
				for ($i=0; $i <count($bFoto) ; $i++) {
					$pdf::Cell($w[$i],56,$bFoto[$i],1,0,'C',false);
				}
				//Foto 1
				if ($photos[0]['Photo']!=null) {
					$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
					$pdf::Image($foto1,7.5,163.5,94,55);
				}

				//Foto2
				if ($photos[1]['Photo']!=null) {
					$foto2 = "/var/www/senditlaravel42/public/photos/".substr($photos[1]['Photo'], -22);
					$pdf::Image($foto2,102.5,163.5,94,55);
				}


				$pdf::Ln();
			break;
			case 3:
				$pdf = new Fpdf();
				$pdf::SetX(7);
				$l1 = ($photos[0]['Photo']!=null) ? $photos[0]['Leyend'] : "" ;
				$l2 = ($photos[1]['Photo']!=null) ? $photos[1]['Leyend'] : "" ;
				$l3 = ($photos[2]['Photo']!=null) ? $photos[2]['Leyend'] : "" ;
				$desc = ['Foto 1:',$l1,'Foto 2:',$l2,'Foto 3:',$l3];
				$w =[15,80,15,80,15,80];
				for ($i=0; $i <count($desc) ; $i++) {
					$pdf::Cell($w[$i],5,$desc[$i],1,0,'C',false);
				}
				$pdf::Ln();
				//Borde Fotos

				$pdf::SetX(7);
				$bFoto = ['','',''];
				$w =[95,95,95];
				for ($i=0; $i <count($bFoto) ; $i++) {
					$pdf::Cell($w[$i],56,$bFoto[$i],1,0,'C',false);
				}
				$pdf::Ln();
				//Fotos SEGUNDA fila

				//Leyendas

				$pdf::SetX(7);

				$desc = ['Foto 4:','','Foto 5:','','Foto 6:',''];
				$w =[15,80,15,80,15,80];
				for ($i=0; $i <count($desc) ; $i++) {
					$pdf::Cell($w[$i],5,$desc[$i],1,0,'C',false);
				}
				$pdf::Ln();
				//Borde Fotos

				$pdf::SetX(7);
				$bFoto = ['','',''];
				$w =[95,95,95];
				for ($i=0; $i <count($bFoto) ; $i++) {
					$pdf::Cell($w[$i],56,$bFoto[$i],1,0,'C',false);
				}
				//Foto 1
				if ($photos[0]['Photo']!=null) {
					$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
					$pdf::Image($foto1,7.5,173.5,94,55);
				}

				//Foto2
				if ($photos[1]['Photo']!=null) {
					$foto2 = "/var/www/senditlaravel42/public/photos/".substr($photos[1]['Photo'], -22);
					$pdf::Image($foto2,102.5,173.5,94,55);
				}

				//Foto3
				if ($photos[2]['Photo']!=null) {
					$foto3 = "/var/www/senditlaravel42/public/photos/".substr($photos[2]['Photo'], -22);
					$pdf::Image($foto3,197.5,173.5,94,55);
				}

				$pdf::Ln();
				break;
				case 4:
					$pdf = new Fpdf();
					$pdf::SetX(7);
					$l1 = ($photos[0]['Photo']!=null) ? $photos[0]['Leyend'] : "" ;
					$l2 = ($photos[1]['Photo']!=null) ? $photos[1]['Leyend'] : "" ;
					$l3 = ($photos[2]['Photo']!=null) ? $photos[2]['Leyend'] : "" ;

					$desc = ['Foto 1:',$l1,'Foto 2:',$l2,'Foto 3:',$l3];
					$w =[15,80,15,80,15,80];
					for ($i=0; $i <count($desc) ; $i++) {
						$pdf::Cell($w[$i],5,$desc[$i],1,0,'C',false);
					}
					$pdf::Ln();
					//Borde Fotos

					$pdf::SetX(7);
					$bFoto = ['','',''];
					$w =[95,95,95];
					for ($i=0; $i <count($bFoto) ; $i++) {
						$pdf::Cell($w[$i],56,$bFoto[$i],1,0,'C',false);
					}
					$pdf::Ln();
					//Fotos SEGUNDA fila

					//Leyendas

					$pdf::SetX(7);
					$l4 = ($photos[3]['Photo']!=null) ? $photos[3]['Leyend'] : "" ;
					$desc = ['Foto 4:',$l4,'Foto 5:','','Foto 6:',''];
					$w =[15,80,15,80,15,80];
					for ($i=0; $i <count($desc) ; $i++) {
						$pdf::Cell($w[$i],5,$desc[$i],1,0,'C',false);
					}
					$pdf::Ln();
					//Borde Fotos

					$pdf::SetX(7);
					$bFoto = ['','',''];
					$w =[95,95,95];
					for ($i=0; $i <count($bFoto) ; $i++) {
						$pdf::Cell($w[$i],56,$bFoto[$i],1,0,'C',false);
					}
					//Foto 1
					if ($photos[0]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,7.5,183.5,94,55);
					}

					//Foto2
					if ($photos[1]['Photo']!=null) {
						$foto2 = "/var/www/senditlaravel42/public/photos/".substr($photos[1]['Photo'], -22);
						$pdf::Image($foto2,102.5,183.5,94,55);
					}

					//Foto3
					if ($photos[2]['Photo']!=null) {
						$foto3 = "/var/www/senditlaravel42/public/photos/".substr($photos[2]['Photo'], -22);
						$pdf::Image($foto3,197.5,183.5,94,55);
					}
					//Foto4
					if ($photos[3]['Photo']!=null) {
						$foto3 = "/var/www/senditlaravel42/public/photos/".substr($photos[3]['Photo'], -22);
						$pdf::Image($foto3,7.5,244.6,94,55);
					}


					$pdf::Ln();
				break;
				case 5:
					$pdf = new Fpdf();
					$pdf::SetX(7);
					$l1 = ($photos[0]['Photo']!=null) ? $photos[0]['Leyend'] : "" ;
					$l2 = ($photos[1]['Photo']!=null) ? $photos[1]['Leyend'] : "" ;
					$l3 = ($photos[2]['Photo']!=null) ? $photos[2]['Leyend'] : "" ;

					$desc = ['Foto 1:',$l1,'Foto 2:',$l2,'Foto 3:',$l3];
					$w =[15,80,15,80,15,80];
					for ($i=0; $i <count($desc) ; $i++) {
						$pdf::Cell($w[$i],5,$desc[$i],1,0,'C',false);
					}
					$pdf::Ln();
					//Borde Fotos

					$pdf::SetX(7);
					$bFoto = ['','',''];
					$w =[95,95,95];
					for ($i=0; $i <count($bFoto) ; $i++) {
						$pdf::Cell($w[$i],56,$bFoto[$i],1,0,'C',false);
					}
					$pdf::Ln();
					//Fotos SEGUNDA fila

					//Leyendas

					$pdf::SetX(7);
					$l4 = ($photos[3]['Photo']!=null) ? $photos[3]['Leyend'] : "" ;
					$l5 = ($photos[4]['Photo']!=null) ? $photos[4]['Leyend'] : "" ;
					$desc = ['Foto 4:',$l4,'Foto 5:',$l5,'Foto 6:',''];
					$w =[15,80,15,80,15,80];
					for ($i=0; $i <count($desc) ; $i++) {
						$pdf::Cell($w[$i],5,$desc[$i],1,0,'C',false);
					}
					$pdf::Ln();
					//Borde Fotos

					$pdf::SetX(7);
					$bFoto = ['','',''];
					$w =[95,95,95];
					for ($i=0; $i <count($bFoto) ; $i++) {
						$pdf::Cell($w[$i],56,$bFoto[$i],1,0,'C',false);
					}
					//Foto 1
					if ($photos[0]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,7.5,193.5,94,55);
					}

					//Foto2
					if ($photos[1]['Photo']!=null) {
						$foto2 = "/var/www/senditlaravel42/public/photos/".substr($photos[1]['Photo'], -22);
						$pdf::Image($foto2,102.5,193.5,94,55);
					}

					//Foto3
					if ($photos[2]['Photo']!=null) {
						$foto3 = "/var/www/senditlaravel42/public/photos/".substr($photos[2]['Photo'], -22);
						$pdf::Image($foto3,197.5,193.5,94,55);
					}
					//Foto4
					if ($photos[3]['Photo']!=null) {
						$foto3 = "/var/www/senditlaravel42/public/photos/".substr($photos[3]['Photo'], -22);
						$pdf::Image($foto3,7.5,254.6,94,55);
					}
					//Foto5
					if ($photos[4]['Photo']!=null) {
						$foto3 = "/var/www/senditlaravel42/public/photos/".substr($photos[4]['Photo'], -22);
						$pdf::Image($foto3,102.5,254.6,94,55);
					}


					$pdf::Ln();
				break;
				case 6:
					$pdf = new Fpdf();
					$pdf::SetX(7);
					$l1 = ($photos[0]['Photo']!=null) ? $photos[0]['Leyend'] : "" ;
					$l2 = ($photos[1]['Photo']!=null) ? $photos[1]['Leyend'] : "" ;
					$l3 = ($photos[2]['Photo']!=null) ? $photos[2]['Leyend'] : "" ;

					$desc = ['Foto 1:',$l1,'Foto 2:',$l2,'Foto 3:',$l3];
					$w =[15,80,15,80,15,80];
					for ($i=0; $i <count($desc) ; $i++) {
						$pdf::Cell($w[$i],5,$desc[$i],1,0,'C',false);
					}
					$pdf::Ln();
					//Borde Fotos

					$pdf::SetX(7);
					$bFoto = ['','',''];
					$w =[95,95,95];
					for ($i=0; $i <count($bFoto) ; $i++) {
						$pdf::Cell($w[$i],56,$bFoto[$i],1,0,'C',false);
					}
					$pdf::Ln();
					//Fotos SEGUNDA fila

					//Leyendas

					$pdf::SetX(7);
					$l4 = ($photos[3]['Photo']!=null) ? $photos[3]['Leyend'] : "" ;
					$l5 = ($photos[4]['Photo']!=null) ? $photos[4]['Leyend'] : "" ;
					$l6 = ($photos[5]['Photo']!=null) ? $photos[5]['Leyend'] : "" ;
					$desc = ['Foto 4:',$l4,'Foto 5:',$l5,'Foto 6:',$l6];
					$w =[15,80,15,80,15,80];
					for ($i=0; $i <count($desc) ; $i++) {
						$pdf::Cell($w[$i],5,$desc[$i],1,0,'C',false);
					}
					$pdf::Ln();
					//Borde Fotos

					$pdf::SetX(7);
					$bFoto = ['','',''];
					$w =[95,95,95];
					for ($i=0; $i <count($bFoto) ; $i++) {
						$pdf::Cell($w[$i],56,$bFoto[$i],1,0,'C',false);
					}
					//Foto 1
					if ($photos[0]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,7.5,203.5,94,55);
					}

					//Foto2
					if ($photos[1]['Photo']!=null) {
						$foto2 = "/var/www/senditlaravel42/public/photos/".substr($photos[1]['Photo'], -22);
						$pdf::Image($foto2,102.5,203.5,94,55);
					}

					//Foto3
					if ($photos[2]['Photo']!=null) {
						$foto3 = "/var/www/senditlaravel42/public/photos/".substr($photos[2]['Photo'], -22);
						$pdf::Image($foto3,197.5,203.5,94,55);
					}
					//Foto4
					if ($photos[3]['Photo']!=null) {
						$foto3 = "/var/www/senditlaravel42/public/photos/".substr($photos[3]['Photo'], -22);
						$pdf::Image($foto3,7.5,264.6,94,55);
					}
					//Foto5
					if ($photos[4]['Photo']!=null) {
						$foto3 = "/var/www/senditlaravel42/public/photos/".substr($photos[4]['Photo'], -22);
						$pdf::Image($foto3,102.5,264.6,94,55);
					}
					//Foto6
					if ($photos[5]['Photo']!=null) {
						$foto3 = "/var/www/senditlaravel42/public/photos/".substr($photos[5]['Photo'], -22);
						$pdf::Image($foto3,197.5,264.6,94,55);
					}


					$pdf::Ln();
				break;

				default:
					$pdf = new Fpdf();
					$pdf::SetX(7);
					$l1 = ($photos[0]['Photo']!=null) ? $photos[0]['Leyend'] : "" ;
					$l2 = ($photos[1]['Photo']!=null) ? $photos[1]['Leyend'] : "" ;
					$l3 = ($photos[2]['Photo']!=null) ? $photos[2]['Leyend'] : "" ;

					$desc = ['Foto 1:',$l1,'Foto 2:',$l2,'Foto 3:',$l3];
					$w =[15,80,15,80,15,80];
					for ($i=0; $i <count($desc) ; $i++) {
						$pdf::Cell($w[$i],5,$desc[$i],1,0,'C',false);
					}
					$pdf::Ln();
					//Borde Fotos

					$pdf::SetX(7);
					$bFoto = ['','',''];
					$w =[95,95,95];
					for ($i=0; $i <count($bFoto) ; $i++) {
						$pdf::Cell($w[$i],56,$bFoto[$i],1,0,'C',false);
					}
					$pdf::Ln();
					//Fotos SEGUNDA fila

					//Leyendas

					$pdf::SetX(7);
					$l4 = ($photos[3]['Photo']!=null) ? $photos[3]['Leyend'] : "" ;
					$l5 = ($photos[4]['Photo']!=null) ? $photos[4]['Leyend'] : "" ;
					$l6 = ($photos[5]['Photo']!=null) ? $photos[5]['Leyend'] : "" ;
					$desc = ['Foto 4:',$l4,'Foto 5:',$l5,'Foto 6:',$l6];
					$w =[15,80,15,80,15,80];
					for ($i=0; $i <count($desc) ; $i++) {
						$pdf::Cell($w[$i],5,$desc[$i],1,0,'C',false);
					}
					$pdf::Ln();
					//Borde Fotos

					$pdf::SetX(7);
					$bFoto = ['','',''];
					$w =[95,95,95];
					for ($i=0; $i <count($bFoto) ; $i++) {
						$pdf::Cell($w[$i],56,$bFoto[$i],1,0,'C',false);
					}
					//Foto 1
					if (count($photos) == 7) {
						if ($photos[0]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,7.5,213.5,94,55);
						}
					}elseif (count($photos) == 8) {
						if ($photos[0]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,7.5,223.5,94,55);
						}
					}elseif (count($photos) == 9) {
						if ($photos[0]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,7.5,233.5,94,55);
						}
					}elseif (count($photos) == 10) {
						if ($photos[0]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,7.5,243.5,94,55);
						}
					}


					//Foto2

					if (count($photos) == 7) {
						if ($photos[1]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,102.5,213.5,94,55);
						}
					}elseif (count($photos) == 8) {
						if ($photos[1]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,102.5,223.5,94,55);
						}
					}elseif (count($photos) == 9) {
						if ($photos[1]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,102.5,233.5,94,55);
						}
					}elseif (count($photos) == 10) {
						if ($photos[1]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,102.5,243.5,94,55);
						}
					}

					//Foto3

					if (count($photos) == 7) {
						if ($photos[2]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,197.5,213.5,94,55);
						}
					}elseif (count($photos) == 8) {
						if ($photos[2]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,197.5,223.5,94,55);
						}
					}elseif (count($photos) == 9) {
						if ($photos[2]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,197.5,233.5,94,55);
						}
					}elseif (count($photos) == 10) {
						if ($photos[2]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,197.5,243.5,94,55);
						}
					}
					//Foto4

					if (count($photos) == 7) {
						if ($photos[3]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,7.5,274.5,94,55);
						}
					}elseif (count($photos) == 8) {
						if ($photos[3]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,7.5,284.5,94,55);
						}
					}elseif (count($photos) == 9) {
						if ($photos[3]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,7.5,294.5,94,55);
						}
					}elseif (count($photos) == 10) {
						if ($photos[3]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,7.5,304.5,94,55);
						}
					}

					//Foto5
					if ($photos[4]['Photo']!=null) {
						$foto3 = "/var/www/senditlaravel42/public/photos/".substr($photos[4]['Photo'], -22);
						$pdf::Image($foto3,102.5,302.5,94,55);
					}
					if (count($photos) == 7) {
						if ($photos[4]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,102.5,274.5,94,55);
						}
					}elseif (count($photos) == 8) {
						if ($photos[4]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,102.5,284.5,94,55);
						}
					}elseif (count($photos) == 9) {
						if ($photos[4]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,102.5,294.5,94,55);
						}
					}elseif (count($photos) == 10) {
						if ($photos[4]['Photo']!=null) {
						$foto1 = "/var/www/senditlaravel42/public/photos/".substr($photos[0]['Photo'], -22);
						$pdf::Image($foto1,102.5,304.5,94,55);
						}
					}
					//Foto6
					if ($photos[5]['Photo']!=null) {
						$foto3 = "/var/www/senditlaravel42/public/photos/".substr($photos[5]['Photo'], -22);
						$pdf::Image($foto3,197.5,302.5,94,55);
					}


					$pdf::Ln();
				break;
		}



		$pdf::Ln();

		$pdf::Output();
		exit;
	}
}