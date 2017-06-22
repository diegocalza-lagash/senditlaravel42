<?php

class ExcelController extends \BaseController {

	public function turn_dates($date){
		$date = new DateTime($date);
		$date->setTimezone(new DateTimeZone('America/Santiago'));
		return $date->format('j F, Y, g:i a');
	}

	public function exportarToExcel($requestId)
	{

		$m = new MongoClient();
		$db = $m->SenditForm;
		$collwf = $db->works_filter;
		$docRepor =$collwf->find(["RequestId" => $requestId]);

		$seg = iterator_to_array($docRepor,false);

		switch (count($seg)) {
			case 6:
				$objPHPExcel = new PHPExcel();
				$objReader = PHPExcel_IOFactory::createReader('Excel2007');
				$objPHPExcel = $objReader->load("reporteRudel.xlsx");
				$objWorksheet= $objPHPExcel->setActiveSheetIndex(0);

				//Exporto Datos Fijos
				$objPHPExcel->getActiveSheet()->SetCellValue('H9', $seg[0]['Loc']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H11', $seg[1]['Blk']);
				$objPHPExcel->getActiveSheet()->SetCellValue('I14', $seg[0]['Dsp']);
				$objPHPExcel->getActiveSheet()->SetCellValue('I15', $seg[0]['Dep']);
				$objPHPExcel->getActiveSheet()->SetCellValue('I16', $seg[0]['Hp']);
				$objPHPExcel->getActiveSheet()->SetCellValue('AD9', $seg[0]['Std']);
				$objPHPExcel->getActiveSheet()->SetCellValue('AD10', $seg[0]['Stn']);
				$objPHPExcel->getActiveSheet()->SetCellValue('AD11', $seg[0]['Itd']);
				$objPHPExcel->getActiveSheet()->SetCellValue('AD12', $seg[0]['Itn']);
				//Primer Trabajo
				$objPHPExcel->getActiveSheet()->SetCellValue('D20', $seg[0]['Work']);
				$objPHPExcel->getActiveSheet()->SetCellValue('E21', $seg[0]['Subwork']);
				$objPHPExcel->getActiveSheet()->SetCellValue('AB21', $this->turn_dates($seg[0]['Dsr']));
				$objPHPExcel->getActiveSheet()->SetCellValue('AH21',$this->turn_dates($seg[0]['Der']));
				$objPHPExcel->getActiveSheet()->SetCellValue('AN21', $seg[0]['Poop']);
				$objPHPExcel->getActiveSheet()->setShowGridlines(true);
				//Itero los otro Trabajos
				$row = 22;
				for ($i=1; $i <count($seg) ; $i++) {
					$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $seg[$i]['Subwork']);
					$objPHPExcel->getActiveSheet()->SetCellValue('AB'.$row, $this->turn_dates($seg[$i]['Dsr']));
					$objPHPExcel->getActiveSheet()->SetCellValue('AH'.$row,$this->turn_dates($seg[$i]['Der']));
					$objPHPExcel->getActiveSheet()->SetCellValue('AN'.$row, $seg[$i]['Poop']);
					$row++;
				}
				//					Imagenes                    //

				//Guardo imagenes que esta en la nube y la guardo en m i carpeta local
			/*	for ($i=0; $i <count($seg) ; $i++) {
					$name_photo = substr($seg[$i]['Photo'],-22);
					copy($seg[$i]['Photo'], '/var/www/senditlaravel42/public/photos/'.$name_photo);
				}*/

				//Primera Foto

				$name_photo = substr($seg[0]['Photo'],-22);

				//$gdImage = copy($seg[0]['Photo'], '/var/www/senditlaravel42/public/photos/'.$name_photo);
				$gdImage = imagecreatefromjpeg('/var/www/senditlaravel42/public/photos/'.$name_photo);

				$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
				$objDrawing->setName('Sample image');
				$objDrawing->setDescription('Sample image');
				$objDrawing->setImageResource($gdImage);
				$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
				$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
				$objDrawing->setOffsetX(100);
				$objDrawing->setOffsetY(750);
				$objDrawing->setHeight(106);
				//$objDrawing->setWidth(826);
				$objDrawing->setHeight(234);
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Segunda foto

				$name_photo = substr( $seg[1]['Photo'],-22);
				//$gdImage = copy($seg[1]['Photo'], '/var/www/senditlaravel42/public/photos/'.$name_photo);
				$gdImage = imagecreatefromjpeg('/var/www/senditlaravel42/public/photos/'.$name_photo);

				$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
				$objDrawing->setName('Sample image');
				$objDrawing->setDescription('Sample image');
				$objDrawing->setImageResource($gdImage);
				$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
				$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
				$objDrawing->setOffsetX(350);
				$objDrawing->setOffsetY(950);
				$objDrawing->setHeight(106);
				//$objDrawing->setWidth(826);
				$objDrawing->setHeight(234);
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Tercera foto

				$name_photo = substr( $seg[2]['Photo'],-22);
				//$gdImage = copy($seg[2]['Photo'], '/var/www/senditlaravel42/public/photos/'.$name_photo);
				$gdImage = imagecreatefromjpeg('/var/www/senditlaravel42/public/photos/'.$name_photo);

				$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
				$objDrawing->setName('Sample image');
				$objDrawing->setDescription('Sample image');
				$objDrawing->setImageResource($gdImage);
				$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
				$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
				$objDrawing->setOffsetX(600);
				$objDrawing->setOffsetY(950);
				$objDrawing->setHeight(106);
				//$objDrawing->setWidth(826);
				$objDrawing->setHeight(234);
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Cuarta foto

				$name_photo = substr($seg[3]['Photo'],-22);
				//$gdImage = copy($seg[3]['Photo'], '/var/www/senditlaravel42/public/photos/'.$name_photo);
				$gdImage = imagecreatefromjpeg('/var/www/senditlaravel42/public/photos/'.$name_photo);

				$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
				$objDrawing->setName('Sample image');
				$objDrawing->setDescription('Sample image');
				$objDrawing->setImageResource($gdImage);
				$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
				$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
				$objDrawing->setOffsetX(100);
				$objDrawing->setOffsetY(1050);
				$objDrawing->setHeight(106);
				//$objDrawing->setWidth(826);
				$objDrawing->setHeight(234);
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Quinta foto

				$name_photo = substr($seg[4]['Photo'],-22);
				//$gdImage = copy($seg[4]['Photo'], '/var/www/senditlaravel42/public/photos/'.$name_photo);
				$gdImage = imagecreatefromjpeg('/var/www/senditlaravel42/public/photos/'.$name_photo);

				$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
				$objDrawing->setName('Sample image');
				$objDrawing->setDescription('Sample image');
				$objDrawing->setImageResource($gdImage);
				$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
				$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
				$objDrawing->setOffsetX(500);
				$objDrawing->setOffsetY(1050);
				$objDrawing->setHeight(106);
				//$objDrawing->setWidth(826);
				$objDrawing->setHeight(234);
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Sexta foto

				$name_photo = substr($seg[5]['Photo'],-22);
				//$gdImage = copy($seg[5]['Photo'], '/var/www/senditlaravel42/public/photos/'.$name_photo);
				$gdImage = imagecreatefromjpeg('/var/www/senditlaravel42/public/photos/'.$name_photo);

				$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
				$objDrawing->setName('Sample image');
				$objDrawing->setDescription('Sample image');
				$objDrawing->setImageResource($gdImage);
				$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
				$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
				$objDrawing->setOffsetX(600);
				$objDrawing->setOffsetY(1050);
				$objDrawing->setHeight(106);
				//$objDrawing->setWidth(826);
				$objDrawing->setHeight(234);
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				/*$gdImage = imagecreatefromjpeg('/var/www/senditlaravel42/public/photos/170417012702917577.jpg');
				$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
				$sheet = $objPHPExcel->getSheet(0);
				$objDrawing->setName('sdsd');
				$objDrawing->setDescription('PHOTO');
				$objDrawing->setImageResource($gdImage);
				//$objDrawing->getPath('/var/www/senditlaravel42/public/photos/170417012702917577.jpg');
				$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
				$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
				//$objDrawing->setWidth(426);
				$objDrawing->setHeight(234);

				//$objDrawing->setWidthAndHeight($wpoints,$points);
				$objDrawing->setCoordinates('B50');
				//$objDrawing->setWorksheet($sheet);
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());*/


				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header('Content-Disposition: attachment; filename="ReportOut.xlsx"');
				header("Cache-Control: max-age=0");
				$objWriter->save("ReportOut.xlsx");
				$objWriter->save("php://output");

				break;

			default:
				# code...
				break;
		}


	}





}//end class
