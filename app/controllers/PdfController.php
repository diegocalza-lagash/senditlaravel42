<?php

class PdfController extends BaseController {


	public function turn_dates($date){
		$date = new DateTime($date);
		$date->setTimezone(new DateTimeZone('America/Santiago'));
		return $date->format('j F, Y, g:i a');
	}
	public function exportarToPdf($requestId)
	{
		$m = new MongoClient();//obsoleta desde mongo 1.0.0
		$db = $m->SenditForm;
		$collwf = $db->works_filter;

		$docRepor =$collwf->find(["RequestId" => $requestId]);

		foreach ($docRepor as $k) {
			//var_dump($k['Subwork']);
		}
		//var_dump($docRepor);
		$seg = iterator_to_array($docRepor,false);
		//echo count($seg);
		switch (count($seg)) {

			case 1:
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
				$pdf::SetX(7);
				$pdf::SetFillColor(224,235,255);
				$pdf::SetTextColor(0);
				$pdf::SetFont('');
				$fw = ['',$seg[0]['Work'],'','',''];
				for ($i=0; $i <count($fw) ; $i++) {
					$pdf::Cell($w[$i],7,$fw[$i],1,0,'L');
				}
				$pdf::Ln();

				//Primer Subtrabajo

				$pdf::SetX(7);
				$sw = $seg[0]['Subwork'];
				$dsr = $this->turn_dates($seg[0]['Dsr']);
				$der = $this->turn_dates($seg[0]['Der']);
				$poop = $seg[0]['Poop'];
				$pdf::Cell(10,7,'',1);
				$pdf::Cell(10,7,'','B');
				$pdf::Cell(129.99, 7, $sw,'B',0,'L');
				$pdf::Cell(45,7,$dsr,1,0,'C',0);
				$pdf::Cell(45,7,$der,1,0,'C',0);
				$pdf::Cell(45,7,$poop,1,0,'C',0);
				$pdf::Ln();

				//Itero demas trabajos
				$pdf::SetX(7);
				for ($i=1; $i < count($seg); $i++) {
					$sw = $seg[$i]['Subwork'];
					$dsr = $this->turn_dates($seg[$i]['Dsr']);
					$der = $this->turn_dates($seg[$i]['Der']);
					$poop = $seg[$i]['Poop'];
					$pdf::SetX(7);
					$pdf::Cell(10,7,'',1);
					$pdf::Cell(10,7,'','B');
					$pdf::Cell(129.99, 7, $sw,'B',0,'L');
					$pdf::Cell(45,7,$dsr,1,0,'C',0);
					$pdf::Cell(45,7,$der,1,0,'C',0);
					$pdf::Cell(45,7,$poop,1,0,'C',0);
					$pdf::Ln();
				}
				$j=0;
				//genero tablas vacias para el alto del reporte
				while ($j <= 5) {
					$pdf::SetX(7);
					$pdf::Cell(10,7,'',1);
					$pdf::Cell(10,7,'','B');
					$pdf::Cell(129.99,7,'','B',0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Ln();
					$j++;
				}
				//OBSERVACIONES

				//Detalles
				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',8);
				$pdf::SetFillColor(145,210,91);
				$pdf::SetTextColor(192,44,44);
				$pdf::Cell(285,5,'PARA DETALLES VER EN ESQUEMAS PROXIMA PAGINA',1,1,'C');

				//obs

				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',11);
				$pdf::SetTextColor(0);

				$obs =['Observaciones:','Turno'];
				$w =[218,67];
				for ($i=0; $i <count($obs) ; $i++)
					$pdf::Cell($w[$i],5,$obs[$i],1,0,'C',true);
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'1.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'2.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'3.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(285,7,'','T');

				//***Registro fotografico***//

				//Header

				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',11);
				$pdf::SetFillColor(145,210,91);
				$pdf::SetTextColor(0);
				$pdf::Cell(285,5,'REGISTRO FOTOGRAFICO',1,0,'C',true);
				$pdf::Ln();

				//Fotos PRIMERA fila

				//Leyendas

				$pdf::SetX(7);
				$l1 = ($seg[0]['Photo']!=null) ? $seg[0]['Leyend'] : "" ;
				//$l1 = $seg[0]['Leyend'];
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
				//Foto 1
				if ($seg[0]['Photo']!=null) {
					$foto1 = "/var/www/senditlaravel42/public/photos/".substr($seg[0]['Photo'], -22);

					$pdf::Image($foto1,7.5,198.5,94,55);
				}


				$pdf::Ln();
				//Espacio entre filas

				$pdf::SetX(7);
				$pdf::Cell(285,5,'',1,0,'C',false);
				$pdf::Ln();
				//Fotos SEGUNDA fila

				//Leyendas

				$pdf::SetX(7);
				//$l4 = $seg[3]['Leyend'];
				$desc = ['Foto 4:', '','Foto 5: ','', 'Foto 6: ',''];
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

				$pdf::Output();
				exit;

			break;

			case 2:
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
				$blk = $seg[1]['Blk'];
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
				$pdf::SetX(7);
				$pdf::SetFillColor(224,235,255);
				$pdf::SetTextColor(0);
				$pdf::SetFont('');
				$fw = ['',$seg[0]['Work'],'','',''];
				for ($i=0; $i <count($fw) ; $i++) {
					$pdf::Cell($w[$i],7,$fw[$i],1,0,'L');
				}
				$pdf::Ln();

				//Primer Subtrabajo

				$pdf::SetX(7);
				$sw = $seg[0]['Subwork'];
				$dsr = $this->turn_dates($seg[0]['Dsr']);
				$der = $this->turn_dates($seg[0]['Der']);
				$poop = $seg[0]['Poop'];
				$pdf::Cell(10,7,'',1);
				$pdf::Cell(10,7,'','B');
				$pdf::Cell(129.99, 7, $sw,'B',0,'L');
				$pdf::Cell(45,7,$dsr,1,0,'C',0);
				$pdf::Cell(45,7,$der,1,0,'C',0);
				$pdf::Cell(45,7,$poop,1,0,'C',0);
				$pdf::Ln();

				//Itero demas trabajos
				$pdf::SetX(7);
				for ($i=1; $i < count($seg); $i++) {
					$sw = $seg[$i]['Subwork'];
					$dsr = $this->turn_dates($seg[$i]['Dsr']);
					$der = $this->turn_dates($seg[$i]['Der']);
					$poop = $seg[$i]['Poop'];
					$pdf::SetX(7);
					$pdf::Cell(10,7,'',1);
					$pdf::Cell(10,7,'','B');
					$pdf::Cell(129.99, 7, $sw,'B',0,'L');
					$pdf::Cell(45,7,$dsr,1,0,'C',0);
					$pdf::Cell(45,7,$der,1,0,'C',0);
					$pdf::Cell(45,7,$poop,1,0,'C',0);
					$pdf::Ln();
				}
				$j=0;
				//genero tablas vacias para el alto del reporte
				while ($j <= 5) {
					$pdf::SetX(7);
					$pdf::Cell(10,7,'',1);
					$pdf::Cell(10,7,'','B');
					$pdf::Cell(129.99,7,'','B',0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Ln();
					$j++;
				}
				//OBSERVACIONES

				//Detalles
				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',8);
				$pdf::SetFillColor(145,210,91);
				$pdf::SetTextColor(192,44,44);
				$pdf::Cell(285,5,'PARA DETALLES VER EN ESQUEMAS PROXIMA PAGINA',1,1,'C');

				//obs

				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',11);
				$pdf::SetTextColor(0);

				$obs =['Observaciones:','Turno'];
				$w =[218,67];
				for ($i=0; $i <count($obs) ; $i++)
					$pdf::Cell($w[$i],5,$obs[$i],1,0,'C',true);
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'1.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'2.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'3.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(285,7,'','T');

				//***Registro fotografico***//

				//Header

				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',11);
				$pdf::SetFillColor(145,210,91);
				$pdf::SetTextColor(0);
				$pdf::Cell(285,5,'REGISTRO FOTOGRAFICO',1,0,'C',true);
				$pdf::Ln();

				//Fotos PRIMERA fila

				//Leyendas

				$pdf::SetX(7);
				$l1 = ($seg[0]['Photo']!=null) ? $seg[0]['Leyend'] : "" ;
				$l2 = ($seg[1]['Photo']!=null) ? $seg[1]['Leyend'] : "" ;

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
				//Foto 1
				if ($seg[0]['Photo']!=null) {
					$foto1 = "/var/www/senditlaravel42/public/photos/".substr($seg[0]['Photo'], -22);

					$pdf::Image($foto1,7.5,205.5,94,55);
				}

				//Foto2
				if ($seg[1]['Photo']!=null) {
					$foto2 = "/var/www/senditlaravel42/public/photos/".substr($seg[1]['Photo'], -22);

					$pdf::Image($foto2,102.5,205.5,94,55);
				}
				//$foto2 = "/var/www/senditlaravel42/public/photos/".substr($seg[1]['Photo'], -22);


				$pdf::Ln();
				//Espacio entre filas

				$pdf::SetX(7);
				$pdf::Cell(285,5,'',1,0,'C',false);
				$pdf::Ln();
				//Fotos SEGUNDA fila

				//Leyendas

				$pdf::SetX(7);
				//$l4 = $seg[3]['Leyend'];
				$desc = ['Foto 4:', '','Foto 5: ','', 'Foto 6: ',''];
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

				$pdf::Output();
				exit;

			break;

			case 3:
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
				$blk = $seg[1]['Blk'];
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
				$pdf::SetX(7);
				$pdf::SetFillColor(224,235,255);
				$pdf::SetTextColor(0);
				$pdf::SetFont('');
				$fw = ['',$seg[0]['Work'],'','',''];
				for ($i=0; $i <count($fw) ; $i++) {
					$pdf::Cell($w[$i],7,$fw[$i],1,0,'L');
				}
				$pdf::Ln();

				//Primer Subtrabajo

				$pdf::SetX(7);
				$sw = $seg[0]['Subwork'];
				$dsr = $this->turn_dates($seg[0]['Dsr']);
				$der = $this->turn_dates($seg[0]['Der']);
				$poop = $seg[0]['Poop'];
				$pdf::Cell(10,7,'',1);
				$pdf::Cell(10,7,'','B');
				$pdf::Cell(129.99, 7, $sw,'B',0,'L');
				$pdf::Cell(45,7,$dsr,1,0,'C',0);
				$pdf::Cell(45,7,$der,1,0,'C',0);
				$pdf::Cell(45,7,$poop,1,0,'C',0);
				$pdf::Ln();

				//Itero demas trabajos
				$pdf::SetX(7);
				for ($i=1; $i < count($seg); $i++) {
					$sw = $seg[$i]['Subwork'];
					$dsr = $this->turn_dates($seg[$i]['Dsr']);
					$der = $this->turn_dates($seg[$i]['Der']);
					$poop = $seg[$i]['Poop'];
					$pdf::SetX(7);
					$pdf::Cell(10,7,'',1);
					$pdf::Cell(10,7,'','B');
					$pdf::Cell(129.99, 7, $sw,'B',0,'L');
					$pdf::Cell(45,7,$dsr,1,0,'C',0);
					$pdf::Cell(45,7,$der,1,0,'C',0);
					$pdf::Cell(45,7,$poop,1,0,'C',0);
					$pdf::Ln();
				}
				$j=0;
				//genero tablas vacias para el alto del reporte
				while ($j <= 5) {
					$pdf::SetX(7);
					$pdf::Cell(10,7,'',1);
					$pdf::Cell(10,7,'','B');
					$pdf::Cell(129.99,7,'','B',0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Ln();
					$j++;
				}
				//OBSERVACIONES

				//Detalles
				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',8);
				$pdf::SetFillColor(145,210,91);
				$pdf::SetTextColor(192,44,44);
				$pdf::Cell(285,5,'PARA DETALLES VER EN ESQUEMAS PROXIMA PAGINA',1,1,'C');

				//obs

				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',11);
				$pdf::SetTextColor(0);

				$obs =['Observaciones:','Turno'];
				$w =[218,67];
				for ($i=0; $i <count($obs) ; $i++)
					$pdf::Cell($w[$i],5,$obs[$i],1,0,'C',true);
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'1.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'2.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'3.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(285,7,'','T');

				//***Registro fotografico***//

				//Header

				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',11);
				$pdf::SetFillColor(145,210,91);
				$pdf::SetTextColor(0);
				$pdf::Cell(285,5,'REGISTRO FOTOGRAFICO',1,0,'C',true);
				$pdf::Ln();

				//Fotos PRIMERA fila

				//Leyendas

				$pdf::SetX(7);
				$l1 = ($seg[0]['Photo']!=null) ? $seg[0]['Leyend'] : "" ;
				$l2 = ($seg[1]['Photo']!=null) ? $seg[1]['Leyend'] : "" ;
				$l3 = ($seg[2]['Photo']!=null) ? $seg[2]['Leyend'] : "" ;

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
				//Foto 1
				if ($seg[0]['Photo']!=null) {
					$foto1 = "/var/www/senditlaravel42/public/photos/".substr($seg[0]['Photo'], -22);
					$pdf::Image($foto1,7.5,212.5,94,55);
				}

				//Foto2
				if ($seg[1]['Photo']!=null) {
					$foto2 = "/var/www/senditlaravel42/public/photos/".substr($seg[1]['Photo'], -22);
					$pdf::Image($foto2,102.5,212.5,94,55);
				}

				//Foto3
				if ($seg[2]['Photo']!=null) {
					$foto3 = "/var/www/senditlaravel42/public/photos/".substr($seg[2]['Photo'], -22);
					$pdf::Image($foto3,197.5,212.5,94,55);
				}

				$pdf::Ln();
				//Espacio entre filas

				$pdf::SetX(7);
				$pdf::Cell(285,5,'',1,0,'C',false);
				$pdf::Ln();
				//Fotos SEGUNDA fila

				//Leyendas

				$pdf::SetX(7);
				//$l4 = $seg[3]['Leyend'];
				$desc = ['Foto 4:', '','Foto 5: ','', 'Foto 6: ',''];
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

				$pdf::Output();
				exit;

			break;

			case 4:
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
				$blk = $seg[1]['Blk'];
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
				$pdf::SetX(7);
				$pdf::SetFillColor(224,235,255);
				$pdf::SetTextColor(0);
				$pdf::SetFont('');
				$fw = ['',$seg[0]['Work'],'','',''];
				for ($i=0; $i <count($fw) ; $i++) {
					$pdf::Cell($w[$i],7,$fw[$i],1,0,'L');
				}
				$pdf::Ln();

				//Primer Subtrabajo

				$pdf::SetX(7);
				$sw = $seg[0]['Subwork'];
				$dsr = $this->turn_dates($seg[0]['Dsr']);
				$der = $this->turn_dates($seg[0]['Der']);
				$poop = $seg[0]['Poop'];
				$pdf::Cell(10,7,'',1);
				$pdf::Cell(10,7,'','B');
				$pdf::Cell(129.99, 7, $sw,'B',0,'L');
				$pdf::Cell(45,7,$dsr,1,0,'C',0);
				$pdf::Cell(45,7,$der,1,0,'C',0);
				$pdf::Cell(45,7,$poop,1,0,'C',0);
				$pdf::Ln();

				//Itero demas trabajos
				$pdf::SetX(7);
				for ($i=1; $i < count($seg); $i++) {
					$sw = $seg[$i]['Subwork'];
					$dsr = $this->turn_dates($seg[$i]['Dsr']);
					$der = $this->turn_dates($seg[$i]['Der']);
					$poop = $seg[$i]['Poop'];
					$pdf::SetX(7);
					$pdf::Cell(10,7,'',1);
					$pdf::Cell(10,7,'','B');
					$pdf::Cell(129.99, 7, $sw,'B',0,'L');
					$pdf::Cell(45,7,$dsr,1,0,'C',0);
					$pdf::Cell(45,7,$der,1,0,'C',0);
					$pdf::Cell(45,7,$poop,1,0,'C',0);
					$pdf::Ln();
				}
				$j=0;
				//genero tablas vacias para el alto del reporte
				while ($j <= 5) {
					$pdf::SetX(7);
					$pdf::Cell(10,7,'',1);
					$pdf::Cell(10,7,'','B');
					$pdf::Cell(129.99,7,'','B',0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Ln();
					$j++;
				}
				//OBSERVACIONES

				//Detalles
				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',8);
				$pdf::SetFillColor(145,210,91);
				$pdf::SetTextColor(192,44,44);
				$pdf::Cell(285,5,'PARA DETALLES VER EN ESQUEMAS PROXIMA PAGINA',1,1,'C');

				//obs

				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',11);
				$pdf::SetTextColor(0);

				$obs =['Observaciones:','Turno'];
				$w =[218,67];
				for ($i=0; $i <count($obs) ; $i++)
					$pdf::Cell($w[$i],5,$obs[$i],1,0,'C',true);
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'1.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'2.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'3.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(285,7,'','T');

				//***Registro fotografico***//

				//Header

				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',11);
				$pdf::SetFillColor(145,210,91);
				$pdf::SetTextColor(0);
				$pdf::Cell(285,5,'REGISTRO FOTOGRAFICO',1,0,'C',true);
				$pdf::Ln();

				//Fotos PRIMERA fila

				//Leyendas

				$pdf::SetX(7);
				$l1 = ($seg[0]['Photo']!=null) ? $seg[0]['Leyend'] : "" ;
				$l2 = ($seg[1]['Photo']!=null) ? $seg[1]['Leyend'] : "" ;
				$l3 = ($seg[2]['Photo']!=null) ? $seg[2]['Leyend'] : "" ;

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
				//Foto 1
				if ($seg[0]['Photo']!=null) {
					$foto1 = "/var/www/senditlaravel42/public/photos/".substr($seg[0]['Photo'], -22);
					$pdf::Image($foto1,7.5,219.5,94,55);
				}

				//Foto2
				if ($seg[1]['Photo']!=null) {
					$foto2 = "/var/www/senditlaravel42/public/photos/".substr($seg[1]['Photo'], -22);
					$pdf::Image($foto2,102.5,219.5,94,55);
				}

				//Foto3
				if ($seg[2]['Photo']!=null) {
					$foto3 = "/var/www/senditlaravel42/public/photos/".substr($seg[2]['Photo'], -22);
					$pdf::Image($foto3,197.5,219.5,94,55);
				}

				$pdf::Ln();
				//Espacio entre filas

				$pdf::SetX(7);
				$pdf::Cell(285,5,'',1,0,'C',false);
				$pdf::Ln();
				//Fotos SEGUNDA fila

				//Leyendas

				$pdf::SetX(7);
				$l4 = ($seg[3]['Photo']!=null) ? $seg[3]['Leyend'] : "" ;
				//$l4 = $seg[3]['Leyend'];
				$desc = ['Foto 4:',$l4,'Foto 5:' , '','Foto 6: ', ''];
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
				//Foto 4
				if ($seg[3]['Photo']!=null) {
					$foto4 = "/var/www/senditlaravel42/public/photos/".substr($seg[3]['Photo'], -22);
					$pdf::Image($foto4,7.5,285.5,94,55);
				}


				$pdf::Output();
				exit;

			break;

			case 5:
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
				$blk = $seg[1]['Blk'];
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
				$pdf::SetX(7);
				$pdf::SetFillColor(224,235,255);
				$pdf::SetTextColor(0);
				$pdf::SetFont('');
				$fw = ['',$seg[0]['Work'],'','',''];
				for ($i=0; $i <count($fw) ; $i++) {
					$pdf::Cell($w[$i],7,$fw[$i],1,0,'L');
				}
				$pdf::Ln();

				//Primer Subtrabajo

				$pdf::SetX(7);
				$sw = $seg[0]['Subwork'];
				$dsr = $this->turn_dates($seg[0]['Dsr']);
				$der = $this->turn_dates($seg[0]['Der']);
				$poop = $seg[0]['Poop'];
				$pdf::Cell(10,7,'',1);
				$pdf::Cell(10,7,'','B');
				$pdf::Cell(129.99, 7, $sw,'B',0,'L');
				$pdf::Cell(45,7,$dsr,1,0,'C',0);
				$pdf::Cell(45,7,$der,1,0,'C',0);
				$pdf::Cell(45,7,$poop,1,0,'C',0);
				$pdf::Ln();

				//Itero demas trabajos
				$pdf::SetX(7);
				for ($i=1; $i < count($seg); $i++) {
					$sw = $seg[$i]['Subwork'];
					$dsr = $this->turn_dates($seg[$i]['Dsr']);
					$der = $this->turn_dates($seg[$i]['Der']);
					$poop = $seg[$i]['Poop'];
					$pdf::SetX(7);
					$pdf::Cell(10,7,'',1);
					$pdf::Cell(10,7,'','B');
					$pdf::Cell(129.99, 7, $sw,'B',0,'L');
					$pdf::Cell(45,7,$dsr,1,0,'C',0);
					$pdf::Cell(45,7,$der,1,0,'C',0);
					$pdf::Cell(45,7,$poop,1,0,'C',0);
					$pdf::Ln();
				}
				$j=0;
				//genero tablas vacias para el alto del reporte
				while ($j <= 5) {
					$pdf::SetX(7);
					$pdf::Cell(10,7,'',1);
					$pdf::Cell(10,7,'','B');
					$pdf::Cell(129.99,7,'','B',0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Ln();
					$j++;
				}
				//OBSERVACIONES

				//Detalles
				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',8);
				$pdf::SetFillColor(145,210,91);
				$pdf::SetTextColor(192,44,44);
				$pdf::Cell(285,5,'PARA DETALLES VER EN ESQUEMAS PROXIMA PAGINA',1,1,'C');

				//obs

				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',11);
				$pdf::SetTextColor(0);

				$obs =['Observaciones:','Turno'];
				$w =[218,67];
				for ($i=0; $i <count($obs) ; $i++)
					$pdf::Cell($w[$i],5,$obs[$i],1,0,'C',true);
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'1.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'2.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'3.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(285,7,'','T');

				//***Registro fotografico***//

				//Header

				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',11);
				$pdf::SetFillColor(145,210,91);
				$pdf::SetTextColor(0);
				$pdf::Cell(285,5,'REGISTRO FOTOGRAFICO',1,0,'C',true);
				$pdf::Ln();

				//Fotos PRIMERA fila

				//Leyendas

				$pdf::SetX(7);
				$l1 = ($seg[0]['Photo']!=null) ? $seg[0]['Leyend'] : "" ;
				$l2 = ($seg[1]['Photo']!=null) ? $seg[1]['Leyend'] : "" ;
				$l3 = ($seg[2]['Photo']!=null) ? $seg[2]['Leyend'] : "" ;

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
				//Foto 1
				if ($seg[0]['Photo']!=null) {
					$foto1 = "/var/www/senditlaravel42/public/photos/".substr($seg[0]['Photo'], -22);
					$pdf::Image($foto1,7.5,226.5,94,55);
				}

				//Foto2
				if ($seg[1]['Photo']) {
					$foto2 = "/var/www/senditlaravel42/public/photos/".substr($seg[1]['Photo'], -22);
					$pdf::Image($foto2,102.5,226.5,94,55);
				}

				//Foto3
				if ($seg[2]['Photo']) {
					$foto3 = "/var/www/senditlaravel42/public/photos/".substr($seg[2]['Photo'], -22);
					$pdf::Image($foto3,197.5,226.5,94,55);
				}

				$pdf::Ln();
				//Espacio entre filas

				$pdf::SetX(7);
				$pdf::Cell(285,5,'',1,0,'C',false);
				$pdf::Ln();
				//Fotos SEGUNDA fila

				//Leyendas

				$pdf::SetX(7);
				$l4 = ($seg[3]['Photo']!=null) ? $seg[3]['Leyend'] : "" ;
				$l5 = ($seg[4]['Photo']!=null) ? $seg[4]['Leyend'] : "" ;

				$desc = ['Foto 4:',$l4,'Foto 5:',$l5,'Foto 6: ', ''];
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
				//Foto 4
				if ($seg[3]['Photo']!=null) {
					$foto4 = "/var/www/senditlaravel42/public/photos/".substr($seg[3]['Photo'], -22);
					$pdf::Image($foto4,7.5,292.5,94,55);
				}

				//Foto5
				if ($seg[4]['Photo']!=null) {
					$foto5 = "/var/www/senditlaravel42/public/photos/".substr($seg[4]['Photo'], -22);
					$pdf::Image($foto5,102.5,292.5,94,55);
				}


				$pdf::Output();
				exit;

			break;//case 5

			case 6:
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
				$blk = $seg[1]['Blk'];
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
				$pdf::SetX(7);
				$pdf::SetFillColor(224,235,255);
				$pdf::SetTextColor(0);
				$pdf::SetFont('');
				$fw = ['',$seg[0]['Work'],'','',''];
				for ($i=0; $i <count($fw) ; $i++) {
					$pdf::Cell($w[$i],7,$fw[$i],1,0,'L');
				}
				$pdf::Ln();

				//Primer Subtrabajo

				$pdf::SetX(7);
				$sw = $seg[0]['Subwork'];
				$dsr = $this->turn_dates($seg[0]['Dsr']);
				$der = $this->turn_dates($seg[0]['Der']);
				$poop = $seg[0]['Poop'];
				$pdf::Cell(10,7,'',1);
				$pdf::Cell(10,7,'','B');
				$pdf::Cell(129.99, 7, $sw,'B',0,'L');
				$pdf::Cell(45,7,$dsr,1,0,'C',0);
				$pdf::Cell(45,7,$der,1,0,'C',0);
				$pdf::Cell(45,7,$poop,1,0,'C',0);
				$pdf::Ln();

				//Itero demas trabajos
				$pdf::SetX(7);
				for ($i=1; $i < count($seg); $i++) {
					$sw = $seg[$i]['Subwork'];
					$dsr = $this->turn_dates($seg[$i]['Dsr']);
					$der = $this->turn_dates($seg[$i]['Der']);
					$poop = $seg[$i]['Poop'];
					$pdf::SetX(7);
					$pdf::Cell(10,7,'',1);
					$pdf::Cell(10,7,'','B');
					$pdf::Cell(129.99, 7, $sw,'B',0,'L');
					$pdf::Cell(45,7,$dsr,1,0,'C',0);
					$pdf::Cell(45,7,$der,1,0,'C',0);
					$pdf::Cell(45,7,$poop,1,0,'C',0);
					$pdf::Ln();
				}
				$j=0;
				//genero tablas vacias para el alto del reporte
				while ($j <= 5) {
					$pdf::SetX(7);
					$pdf::Cell(10,7,'',1);
					$pdf::Cell(10,7,'','B');
					$pdf::Cell(129.99,7,'','B',0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Ln();
					$j++;
				}
				//OBSERVACIONES

				//Detalles
				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',8);
				$pdf::SetFillColor(145,210,91);
				$pdf::SetTextColor(192,44,44);
				$pdf::Cell(285,5,'PARA DETALLES VER EN ESQUEMAS PROXIMA PAGINA',1,1,'C');

				//obs

				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',11);
				$pdf::SetTextColor(0);

				$obs =['Observaciones:','Turno'];
				$w =[218,67];
				for ($i=0; $i <count($obs) ; $i++)
					$pdf::Cell($w[$i],5,$obs[$i],1,0,'C',true);
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'1.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'2.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'3.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(285,7,'','T');

				//***Registro fotografico***//

				//Header

				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',11);
				$pdf::SetFillColor(145,210,91);
				$pdf::SetTextColor(0);
				$pdf::Cell(285,5,'REGISTRO FOTOGRAFICO',1,0,'C',true);
				$pdf::Ln();

				//Fotos PRIMERA fila

				//Leyendas

				$pdf::SetX(7);
				$l1 = ($seg[0]['Photo']!=null) ? $seg[0]['Leyend'] : "" ;
				$l2 = ($seg[1]['Photo']!=null) ? $seg[1]['Leyend'] : "" ;
				$l3 = ($seg[2]['Photo']!=null) ? $seg[2]['Leyend'] : "" ;

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
				//Foto 1
				if ($seg[0]['Photo']!=null) {
					$foto1 = "/var/www/senditlaravel42/public/photos/".substr($seg[0]['Photo'], -22);
					$pdf::Image($foto1,7.5,233.5,94,55);
				}

				//Foto2
				if ($seg[1]['Photo']!=null) {
					$foto2 = "/var/www/senditlaravel42/public/photos/".substr($seg[1]['Photo'], -22);
					$pdf::Image($foto2,102.5,233.5,94,55);
				}

				//Foto3
				if ($seg[2]['Photo']!=null) {
					$foto3 = "/var/www/senditlaravel42/public/photos/".substr($seg[2]['Photo'], -22);
					$pdf::Image($foto3,197.5,233.5,94,55);
				}

				$pdf::Ln();
				//Espacio entre filas

				$pdf::SetX(7);
				$pdf::Cell(285,5,'',1,0,'C',false);
				$pdf::Ln();
				//Fotos SEGUNDA fila

				//Leyendas

				$pdf::SetX(7);
				$l4 = ($seg[3]['Photo']!=null) ? $seg[3]['Leyend'] : "" ;
				$l5 = ($seg[4]['Photo']!=null) ? $seg[4]['Leyend'] : "" ;
				$l6 = ($seg[5]['Photo']!=null) ? $seg[5]['Leyend'] : "" ;

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
				//Foto 4
				if ($seg[3]['Photo']!=null) {
					$foto4 = "/var/www/senditlaravel42/public/photos/".substr($seg[3]['Photo'], -22);
					$pdf::Image($foto4,7.5,299.5,94,55);
				}

				//Foto5
				if ($seg[4]['Photo']!=null) {
					$foto5 = "/var/www/senditlaravel42/public/photos/".substr($seg[4]['Photo'], -22);
					$pdf::Image($foto5,102.5,299.5,94,55);
				}

				//Foto6
				if ($seg[5]['Photo']!=null) {
					$foto6 = "/var/www/senditlaravel42/public/photos/".substr($seg[5]['Photo'], -22);
					$pdf::Image($foto6,197.5,299.5,94,55);
				}

				$pdf::Output();
				exit;

			break;

			default:
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
				$blk = $seg[1]['Blk'];
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
				$pdf::SetX(7);
				$pdf::SetFillColor(224,235,255);
				$pdf::SetTextColor(0);
				$pdf::SetFont('');
				$fw = ['',$seg[0]['Work'],'','',''];
				for ($i=0; $i <count($fw) ; $i++) {
					$pdf::Cell($w[$i],7,$fw[$i],1,0,'L');
				}
				$pdf::Ln();

				//Primer Subtrabajo

				$pdf::SetX(7);
				$sw = $seg[0]['Subwork'];
				$dsr = $this->turn_dates($seg[0]['Dsr']);
				$der = $this->turn_dates($seg[0]['Der']);
				$poop = $seg[0]['Poop'];
				$pdf::Cell(10,7,'',1);
				$pdf::Cell(10,7,'','B');
				$pdf::Cell(129.99, 7, $sw,'B',0,'L');
				$pdf::Cell(45,7,$dsr,1,0,'C',0);
				$pdf::Cell(45,7,$der,1,0,'C',0);
				$pdf::Cell(45,7,$poop,1,0,'C',0);
				$pdf::Ln();

				//Itero demas trabajos
				$pdf::SetX(7);
				for ($i=1; $i < count($seg)-1; $i++) {
					$sw = $seg[$i]['Subwork'];
					$dsr = $this->turn_dates($seg[$i]['Dsr']);
					$der = $this->turn_dates($seg[$i]['Der']);
					$poop = $seg[$i]['Poop'];
					$pdf::SetX(7);
					$pdf::Cell(10,7,'',1);
					$pdf::Cell(10,7,'','B');
					$pdf::Cell(129.99, 7, $sw,'B',0,'L');
					$pdf::Cell(45,7,$dsr,1,0,'C',0);
					$pdf::Cell(45,7,$der,1,0,'C',0);
					$pdf::Cell(45,7,$poop,1,0,'C',0);
					$pdf::Ln();
				}
				$j=0;
				//genero tablas vacias para el alto del reporte
				while ($j <= 5) {
					$pdf::SetX(7);
					$pdf::Cell(10,7,'',1);
					$pdf::Cell(10,7,'','B');
					$pdf::Cell(129.99,7,'','B',0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Cell(45,7,'',1,0,'C',0);
					$pdf::Ln();
					$j++;
				}
				//OBSERVACIONES

				//Detalles
				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',8);
				$pdf::SetFillColor(145,210,91);
				$pdf::SetTextColor(192,44,44);
				$pdf::Cell(285,5,'PARA DETALLES VER EN ESQUEMAS PROXIMA PAGINA',1,1,'C');

				//obs

				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',11);
				$pdf::SetTextColor(0);

				$obs =['Observaciones:','Turno'];
				$w =[218,67];
				for ($i=0; $i <count($obs) ; $i++)
					$pdf::Cell($w[$i],5,$obs[$i],1,0,'C',true);
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'1.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'2.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(10,7,'','L',0);
				$pdf::Cell(208,7,'3.-','R',0);
				$pdf::Cell(67,7,'','R');
				$pdf::Ln();
				$pdf::SetX(7);
				$pdf::Cell(285,7,'','T');

				//***Registro fotografico***//

				//Header

				$pdf::SetX(7);
				$pdf::SetFont('Arial','B',11);
				$pdf::SetFillColor(145,210,91);
				$pdf::SetTextColor(0);
				$pdf::Cell(285,5,'REGISTRO FOTOGRAFICO',1,0,'C',true);
				$pdf::Ln();

				//Fotos PRIMERA fila

				//Leyendas

				$pdf::SetX(7);
				$l1 = ($seg[0]['Photo']!=null) ? $seg[0]['Leyend'] : "" ;
				$l2 = ($seg[1]['Photo']!=null) ? $seg[1]['Leyend'] : "" ;
				$l3 = ($seg[2]['Photo']!=null) ? $seg[2]['Leyend'] : "" ;

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

				//Foto 1
				if ($seg[0]['Photo']!=null) {
					$foto1 = "/var/www/senditlaravel42/public/photos/".substr($seg[0]['Photo'], -22);
					$pdf::Image($foto1,7.5,233.5,94,55);
				}

				//Foto2
				if ($seg[1]['Photo']!=null) {
					$foto2 = "/var/www/senditlaravel42/public/photos/".substr($seg[1]['Photo'], -22);
					$pdf::Image($foto2,102.5,233.5,94,55);
				}

				//Foto3
				if ($seg[2]['Photo']!=null) {
					$foto3 = "/var/www/senditlaravel42/public/photos/".substr($seg[2]['Photo'], -22);
					$pdf::Image($foto3,197.5,233.5,94,55);
				}
				$pdf::Ln();
				//Espacio entre filas

				$pdf::SetX(7);
				$pdf::Cell(285,5,'',1,0,'C',false);
				$pdf::Ln();
				//Fotos SEGUNDA fila

				//Leyendas

				$pdf::SetX(7);
				$l4 = ($seg[3]['Photo']!=null) ? $seg[3]['Leyend'] : "" ;
				$l5 = ($seg[4]['Photo']!=null) ? $seg[4]['Leyend'] : "" ;
				$l6 = ($seg[5]['Photo']!=null) ? $seg[5]['Leyend'] : "" ;
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
				//Foto 4
				if ($seg[3]['Photo']!=null) {
					$foto4 = "/var/www/senditlaravel42/public/photos/".substr($seg[3]['Photo'], -22);
					$pdf::Image($foto4,7.5,299.5,94,55);
				}

				//Foto5
				if ($seg[4]['Photo']!=null) {
					$foto5 = "/var/www/senditlaravel42/public/photos/".substr($seg[4]['Photo'], -22);
					$pdf::Image($foto5,102.5,299.5,94,55);
				}

				//Foto6
				if ($seg[5]['Photo']!=null) {
					$foto6 = "/var/www/senditlaravel42/public/photos/".substr($seg[5]['Photo'], -22);
					$pdf::Image($foto6,197.5,299.5,94,55);
				}
				$pdf::Output();
				exit;
			break;
		}
	}


}