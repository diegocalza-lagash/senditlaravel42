<?php

class ExcelController extends \BaseController {

	public function turn_dates($date){
		$date = new DateTime($date);
		$date->setTimezone(new DateTimeZone('America/Santiago'));
		return $date->format('j F, Y, g:i a');
	}
	/**
	 * easy image resize function
	 * @param  $file - file name to resize
	 * @param  $string - The image data, as a string
	 * @param  $width - new image width
	 * @param  $height - new image height
	 * @param  $proportional - keep image proportional, default is no
	 * @param  $output - name of the new file (include path if needed)
	 * @param  $delete_original - if true the original image will be deleted
	 * @param  $use_linux_commands - if set to true will use "rm" to delete the image, if false will use PHP unlink
	 * @param  $quality - enter 1-100 (100 is best quality) default is 100
	 * @return boolean|resource
	 */
	  function smart_resize_image($file,
	                              $string             = null,
	                              $width              = 0,
	                              $height             = 0,
	                              $proportional       = false,
	                              $output             = 'file',
	                              $delete_original    = true,
	                              $use_linux_commands = false,
	  							  $quality = 100
	  		 ) {

	    if ( $height <= 0 && $width <= 0 ) return false;
	    if ( $file === null && $string === null ) return false;

	    # Setting defaults and meta
	    $info                         = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
	    $image                        = '';
	    $final_width                  = 0;
	    $final_height                 = 0;
	    list($width_old, $height_old) = $info;
		$cropHeight = $cropWidth = 0;

	    # Calculating proportionality
	    if ($proportional) {
	      if      ($width  == 0)  $factor = $height/$height_old;
	      elseif  ($height == 0)  $factor = $width/$width_old;
	      else                    $factor = min( $width / $width_old, $height / $height_old );

	      $final_width  = round( $width_old * $factor );
	      $final_height = round( $height_old * $factor );
	    }
	    else {
	      $final_width = ( $width <= 0 ) ? $width_old : $width;
	      $final_height = ( $height <= 0 ) ? $height_old : $height;
		  $widthX = $width_old / $width;
		  $heightX = $height_old / $height;

		  $x = min($widthX, $heightX);
		  $cropWidth = ($width_old - $width * $x) / 2;
		  $cropHeight = ($height_old - $height * $x) / 2;
	    }

	    # Loading image to memory according to type
	    switch ( $info[2] ) {
	      case IMAGETYPE_JPEG:  $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);  break;
	      case IMAGETYPE_GIF:   $file !== null ? $image = imagecreatefromgif($file)  : $image = imagecreatefromstring($string);  break;
	      case IMAGETYPE_PNG:   $file !== null ? $image = imagecreatefrompng($file)  : $image = imagecreatefromstring($string);  break;
	      default: return false;
	    }


	    # This is the resizing/resampling/transparency-preserving magic
	    $image_resized = imagecreatetruecolor( $final_width, $final_height );
	    if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
	      $transparency = imagecolortransparent($image);
	      $palletsize = imagecolorstotal($image);

	      if ($transparency >= 0 && $transparency < $palletsize) {
	        $transparent_color  = imagecolorsforindex($image, $transparency);
	        $transparency       = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
	        imagefill($image_resized, 0, 0, $transparency);
	        imagecolortransparent($image_resized, $transparency);
	      }
	      elseif ($info[2] == IMAGETYPE_PNG) {
	        imagealphablending($image_resized, false);
	        $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
	        imagefill($image_resized, 0, 0, $color);
	        imagesavealpha($image_resized, true);
	      }
	    }
	    imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);


	    # Taking care of original, if needed
	    if ( $delete_original ) {
	      if ( $use_linux_commands ) exec('rm '.$file);
	      else @unlink($file);
	    }

	    # Preparing a method of providing result
	    switch ( strtolower($output) ) {
	      case 'browser':
	        $mime = image_type_to_mime_type($info[2]);
	        header("Content-type: $mime");
	        $output = NULL;
	      break;
	      case 'file':
	        $output = $file;
	      break;
	      case 'return':
	        return $image_resized;
	      break;
	      default:
	      break;
	    }

	    # Writing image according to type to the output destination and image quality
	    switch ( $info[2] ) {
	      case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
	      case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
	      case IMAGETYPE_PNG:
	        $quality = 9 - (int)((0.9*$quality)/10.0);
	        imagepng($image_resized, $output, $quality);
	        break;
	      default: return false;
	    }

	    return true;
	}

	function resize_image($file, $w, $h, $crop=FALSE) {
	    list($width, $height) = getimagesize($file);
	    $r = $width / $height;
	    if ($crop) {
	        if ($width > $height) {
	            $width = ceil($width-($width*abs($r-$w/$h)));
	        } else {
	            $height = ceil($height-($height*abs($r-$w/$h)));
	        }
	        $newwidth = $w;
	        $newheight = $h;
	    } else {
	        if ($w/$h > $r) {
	            $newwidth = $h*$r;
	            $newheight = $h;
	        } else {
	            $newheight = $w/$r;
	            $newwidth = $w;
	        }
	    }
	    $src = imagecreatefromjpeg($file);
	    $dst = imagecreatetruecolor($newwidth, $newheight);
	    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

	    return $dst;
	}



	public function exportarToExcel($requestId)
	{

		$m = new MongoClient();
		$db = $m->SenditForm;
		$collwf = $db->works_filter;
		$docRepor =$collwf->find(["RequestId" => $requestId]);

		$seg = iterator_to_array($docRepor,false);

		switch (count($seg)) {

			case 1:
				$objPHPExcel = new PHPExcel();
				$objReader = PHPExcel_IOFactory::createReader('Excel2007');
				$objPHPExcel = $objReader->load("/var/www/senditlaravel42/public/reporteRudel.xlsx");
				$objWorksheet= $objPHPExcel->setActiveSheetIndex(0);

				//Exporto Datos Fijos
				$objPHPExcel->getActiveSheet()->SetCellValue('H9', $seg[0]['Loc']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H11', $seg[0]['Blk']);
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
				//Observaciones
				$objPHPExcel->getActiveSheet()->SetCellValue('D45', $seg[0]['Subwork'].": ".$seg[0]['Obs']);

				//					Imagenes                    //

				//Primera Foto
				$objPHPExcel->getActiveSheet()->SetCellValue('D52', $seg[0]['Leyend']);

				$name_photo = substr($seg[0]['Photo'],-22);
				$foto1 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 1');
				$objDrawing->setDescription('Trabajo 1');

				list($width, $height) = getimagesize($foto1);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('B53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header('Content-Disposition: attachment; filename="ReportOut.xlsx"');
				header("Cache-Control: max-age=0");
				$objWriter->save("ReportOut.xlsx");
				$objWriter->save("php://output");

			break;

			case 2:
				$objPHPExcel = new PHPExcel();
				$objReader = PHPExcel_IOFactory::createReader('Excel2007');
				$objPHPExcel = $objReader->load("/var/www/senditlaravel42/public/reporteRudel.xlsx");
				$objWorksheet= $objPHPExcel->setActiveSheetIndex(0);

				//Exporto Datos Fijos
				$objPHPExcel->getActiveSheet()->SetCellValue('H9', $seg[0]['Loc']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H11', $seg[0]['Blk']);
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
				//Observaciones

				$objPHPExcel->getActiveSheet()->SetCellValue('D45', $seg[0]['Subwork'].": ".$seg[0]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D46', $seg[1]['Subwork'].": ".$seg[1]['Obs']);

				//					Imagenes                    //

				//Primera Foto
				$objPHPExcel->getActiveSheet()->SetCellValue('D52', $seg[0]['Leyend']);

				$name_photo = substr($seg[0]['Photo'],-22);
				$foto1 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 1');
				$objDrawing->setDescription('Trabajo 1');

				list($width, $height) = getimagesize($foto1);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('B53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Segunda foto

				$objPHPExcel->getActiveSheet()->SetCellValue('R52', $seg[1]['Leyend']);
				$name_photo = substr( $seg[1]['Photo'],-22);
				$foto2 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 2');
				$objDrawing->setDescription('Trabajo 2');

				list($width, $height) = getimagesize($foto2);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('P53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header('Content-Disposition: attachment; filename="ReportOut.xlsx"');
				header("Cache-Control: max-age=0");
				$objWriter->save("ReportOut.xlsx");
				$objWriter->save("php://output");

			break;

			case 3:
				$objPHPExcel = new PHPExcel();
				$objReader = PHPExcel_IOFactory::createReader('Excel2007');
				$objPHPExcel = $objReader->load("/var/www/senditlaravel42/public/reporteRudel.xlsx");
				$objWorksheet= $objPHPExcel->setActiveSheetIndex(0);

				//Exporto Datos Fijos
				$objPHPExcel->getActiveSheet()->SetCellValue('H9', $seg[0]['Loc']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H11', $seg[0]['Blk']);
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
				//Observaciones

				$objPHPExcel->getActiveSheet()->SetCellValue('D45', $seg[0]['Subwork'].": ".$seg[0]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D46', $seg[1]['Subwork'].": ".$seg[1]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D47', $seg[2]['Subwork'].": ".$seg[2]['Obs']);
				//					Imagenes                    //

				//Primera Foto
				$objPHPExcel->getActiveSheet()->SetCellValue('D52', $seg[0]['Leyend']);

				$name_photo = substr($seg[0]['Photo'],-22);
				$foto1 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 1');
				$objDrawing->setDescription('Trabajo 1');

				list($width, $height) = getimagesize($foto1);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('B53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Segunda foto
				$objPHPExcel->getActiveSheet()->SetCellValue('R52', $seg[1]['Leyend']);

				$name_photo = substr( $seg[1]['Photo'],-22);
				$foto2 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 2');
				$objDrawing->setDescription('Trabajo 2');

				list($width, $height) = getimagesize($foto2);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('P53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Tercera foto
				$objPHPExcel->getActiveSheet()->SetCellValue('AF52', $seg[2]['Leyend']);

				$name_photo = substr( $seg[2]['Photo'],-22);
				$foto3 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 3');
				$objDrawing->setDescription('Trabajo 3');

				list($width, $height) = getimagesize($foto3);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('AD53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header('Content-Disposition: attachment; filename="ReportOut.xlsx"');
				header("Cache-Control: max-age=0");
				$objWriter->save("ReportOut.xlsx");
				$objWriter->save("php://output");

			break;

			case 4:
				$objPHPExcel = new PHPExcel();
				$objReader = PHPExcel_IOFactory::createReader('Excel2007');
				$objPHPExcel = $objReader->load("/var/www/senditlaravel42/public/reporteRudel.xlsx");
				$objWorksheet= $objPHPExcel->setActiveSheetIndex(0);

				//Exporto Datos Fijos
				$objPHPExcel->getActiveSheet()->SetCellValue('H9', $seg[0]['Loc']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H11', $seg[0]['Blk']);
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
				//Observaciones

				$objPHPExcel->getActiveSheet()->SetCellValue('D45', $seg[0]['Subwork'].": ".$seg[0]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D46', $seg[1]['Subwork'].": ".$seg[1]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D47', $seg[2]['Subwork'].": ".$seg[2]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D48', $seg[3]['Subwork'].": ".$seg[3]['Obs']);
				//					Imagenes                    //

				//Primera Foto
				$objPHPExcel->getActiveSheet()->SetCellValue('D52', $seg[0]['Leyend']);

				$name_photo = substr($seg[0]['Photo'],-22);
				$foto1 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 1');
				$objDrawing->setDescription('Trabajo 1');

				list($width, $height) = getimagesize($foto1);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('B53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Segunda foto
				$objPHPExcel->getActiveSheet()->SetCellValue('R52', $seg[1]['Leyend']);

				$name_photo = substr( $seg[1]['Photo'],-22);
				$foto2 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 2');
				$objDrawing->setDescription('Trabajo 2');

				list($width, $height) = getimagesize($foto2);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('P53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Tercera foto
				$objPHPExcel->getActiveSheet()->SetCellValue('AF52', $seg[2]['Leyend']);

				$name_photo = substr( $seg[2]['Photo'],-22);
				$foto3 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 3');
				$objDrawing->setDescription('Trabajo 3');

				list($width, $height) = getimagesize($foto3);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('AD53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Cuarta foto
				$objPHPExcel->getActiveSheet()->SetCellValue('D67', $seg[3]['Leyend']);

				$name_photo = substr($seg[3]['Photo'],-22);
				$foto4 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 4');
				$objDrawing->setDescription('Trabajo 4');

				list($width, $height) = getimagesize($foto4);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('B68');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header('Content-Disposition: attachment; filename="ReportOut.xlsx"');
				header("Cache-Control: max-age=0");
				$objWriter->save("ReportOut.xlsx");
				$objWriter->save("php://output");

			break;

			case 5:

				$objPHPExcel = new PHPExcel();
				$objReader = PHPExcel_IOFactory::createReader('Excel2007');
				$objPHPExcel = $objReader->load("/var/www/senditlaravel42/public/reporteRudel.xlsx");
				$objWorksheet= $objPHPExcel->setActiveSheetIndex(0);

				// estilos
				$styleArray = array(
				    'font'  => array(
				        'bold'  => true,
				        'color' => array('rgb' => 'FF0000'),
				        'size'  => 35,
				        'name'  => 'Verdana'
				    ));
				$objPHPExcel->getActiveSheet()->getStyle('B50')->applyFromArray($styleArray);
				//$objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);

				//Exporto Datos Fijos

				$objPHPExcel->getActiveSheet()->SetCellValue('H9', $seg[0]['Loc']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H11', $seg[0]['Blk']);
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
				//Observaciones

				$objPHPExcel->getActiveSheet()->SetCellValue('D45', $seg[0]['Subwork'].": ".$seg[0]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D46', $seg[1]['Subwork'].": ".$seg[1]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D47', $seg[2]['Subwork'].": ".$seg[2]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D48', $seg[3]['Subwork'].": ".$seg[3]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D49', $seg[4]['Subwork'].": ".$seg[4]['Obs']);

				//					Imagenes                    //

				//Primera Foto


				//echo $width, $height;

			    //indicate the path and name for the new resized file
			    //$foto1Resized = '/var/www/senditlaravel42/public/photos/'.$name_photo;

			   // $foto1 = $this->resize_image('/var/www/senditlaravel42/public/photos/'.$name_photo, 456, 556);
			    //$output ='/var/www/senditlaravel42/public/photos/resized/'.$name_photo;
			   // imagejpeg($foto1, $output, 100);

			    //call the function (when passing path to pic)
			    //$this->smart_resize_image($foto1, null, 800, 800, false, $foto1Resized, true, false, 100 );

			    //call the function (when passing pic as string)
			    //smart_resize_image(null , file_get_contents($file), SET_YOUR_WIDTH , SET_YOUR_HIGHT , false , $resizedFile , false , false ,100 );

				$objPHPExcel->getActiveSheet()->SetCellValue('D52', $seg[0]['Leyend']);
				$name_photo = substr($seg[0]['Photo'],-22);
				$foto1 = '/var/www/senditlaravel42/public/photos/'.$name_photo;


				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 1');
				$objDrawing->setDescription('Trabajo 1');

				list($width, $height) = getimagesize($foto1);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setRotation(25);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('B53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				/*$gdImage = imagecreatefromjpeg('/var/www/senditlaravel42/public/photos/'.$name_photo);

				$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
				$objDrawing->setName('Sample image');
				$objDrawing->setDescription('Sample image');
				$objDrawing->setImageResource($gdImage);
				$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
				$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
				$objDrawing->setOffsetX(100);
				$objDrawing->setOffsetY(963.5);
				//$objDrawing->setWidth(826);
				//$objDrawing->setHeight(257);
				$objDrawing->setWidthAndHeight(800,257);
				$objDrawing->setResizeProportional(true);
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());*/

				//Segunda foto
				$objPHPExcel->getActiveSheet()->SetCellValue('R52', $seg[1]['Leyend']);

				$name_photo = substr( $seg[1]['Photo'],-22);
				$foto2 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 2');
				$objDrawing->setDescription('Trabajo 2');

				list($width, $height) = getimagesize($foto2);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('P53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Tercera foto
				$objPHPExcel->getActiveSheet()->SetCellValue('AF52', $seg[2]['Leyend']);

				$name_photo = substr( $seg[2]['Photo'],-22);
				$foto3 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 3');
				$objDrawing->setDescription('Trabajo 3');

				list($width, $height) = getimagesize($foto3);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('AD53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Cuarta foto
				$objPHPExcel->getActiveSheet()->SetCellValue('D67', $seg[3]['Leyend']);

				$name_photo = substr($seg[3]['Photo'],-22);
				$foto4 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 4');
				$objDrawing->setDescription('Trabajo 4');

				list($width, $height) = getimagesize($foto4);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('B68');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Quinta foto
				$objPHPExcel->getActiveSheet()->SetCellValue('R67', $seg[4]['Leyend']);

				$name_photo = substr($seg[4]['Photo'],-22);
				$foto5 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 5');
				$objDrawing->setDescription('Trabajo 5');

				list($width, $height) = getimagesize($foto5);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('P68');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());


				/* Guardo y Descargo*/

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header('Content-Disposition: attachment; filename="ReportOut.xlsx"');
				header("Cache-Control: max-age=0");
				$objWriter->save("ReportOut.xlsx");
				$objWriter->save("php://output");

			break;

			case 6:
				$objPHPExcel = new PHPExcel();
				$objReader = PHPExcel_IOFactory::createReader('Excel2007');
				$objPHPExcel = $objReader->load("/var/www/senditlaravel42/public/reporteRudel.xlsx");
				$objWorksheet= $objPHPExcel->setActiveSheetIndex(0);

				//Exporto Datos Fijos
				$objPHPExcel->getActiveSheet()->SetCellValue('H9', $seg[0]['Loc']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H11', $seg[0]['Blk']);
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
				//Observaciones

				$objPHPExcel->getActiveSheet()->SetCellValue('D45', $seg[0]['Subwork'].": ".$seg[0]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D46', $seg[1]['Subwork'].": ".$seg[1]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D47', $seg[2]['Subwork'].": ".$seg[2]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D48', $seg[3]['Subwork'].": ".$seg[3]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D49', $seg[4]['Subwork'].": ".$seg[4]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D50', $seg[5]['Subwork'].": ".$seg[5]['Obs']);
				//					Imagenes                    //

				//Primera Foto
				$objPHPExcel->getActiveSheet()->SetCellValue('D52', $seg[0]['Leyend']);
				$name_photo = substr($seg[0]['Photo'],-22);
				$foto1 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 1');
				$objDrawing->setDescription('Trabajo 1');

				list($width, $height) = getimagesize($foto1);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('B53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Segunda foto
				$objPHPExcel->getActiveSheet()->SetCellValue('R52', $seg[1]['Leyend']);
				$name_photo = substr( $seg[1]['Photo'],-22);
				$foto2 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 2');
				$objDrawing->setDescription('Trabajo 2');

				list($width, $height) = getimagesize($foto2);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('P53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Tercera foto
				$objPHPExcel->getActiveSheet()->SetCellValue('AF52', $seg[2]['Leyend']);
				$name_photo = substr( $seg[2]['Photo'],-22);
				$foto3 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 3');
				$objDrawing->setDescription('Trabajo 3');

				list($width, $height) = getimagesize($foto3);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('AD53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Cuarta foto
				$objPHPExcel->getActiveSheet()->SetCellValue('D67', $seg[3]['Leyend']);
				$name_photo = substr($seg[3]['Photo'],-22);
				$foto4 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 4');
				$objDrawing->setDescription('Trabajo 4');

				list($width, $height) = getimagesize($foto4);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('B68');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Quinta foto
				$objPHPExcel->getActiveSheet()->SetCellValue('R67', $seg[4]['Leyend']);
				$name_photo = substr($seg[4]['Photo'],-22);
				$foto5 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 5');
				$objDrawing->setDescription('Trabajo 5');

				list($width, $height) = getimagesize($foto5);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('P68');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Sexta foto
				$objPHPExcel->getActiveSheet()->SetCellValue('AF67', $seg[5]['Leyend']);
				$name_photo = substr($seg[5]['Photo'],-22);
				$foto6 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 6');
				$objDrawing->setDescription('Trabajo 6');

				list($width, $height) = getimagesize($foto6);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('AD68');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());


				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header('Content-Disposition: attachment; filename="ReportOut.xlsx"');
				header("Cache-Control: max-age=0");
				$objWriter->save("ReportOut.xlsx");
				$objWriter->save("php://output");

			break;

			default:
				$objPHPExcel = new PHPExcel();
				$objReader = PHPExcel_IOFactory::createReader('Excel2007');
				$objPHPExcel = $objReader->load("/var/www/senditlaravel42/public/reporteRudel.xlsx");
				$objWorksheet= $objPHPExcel->setActiveSheetIndex(0);

				//Exporto Datos Fijos
				$objPHPExcel->getActiveSheet()->SetCellValue('H9', $seg[0]['Loc']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H11', $seg[0]['Blk']);
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
				for ($i=1; $i <count($seg)-1 ; $i++) {
					$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $seg[$i]['Subwork']);
					$objPHPExcel->getActiveSheet()->SetCellValue('AB'.$row, $this->turn_dates($seg[$i]['Dsr']));
					$objPHPExcel->getActiveSheet()->SetCellValue('AH'.$row,$this->turn_dates($seg[$i]['Der']));
					$objPHPExcel->getActiveSheet()->SetCellValue('AN'.$row, $seg[$i]['Poop']);
					$row++;
				}
				//Observaciones

				$objPHPExcel->getActiveSheet()->SetCellValue('D45', $seg[0]['Subwork'].": ".$seg[0]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D46', $seg[1]['Subwork'].": ".$seg[1]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D47', $seg[2]['Subwork'].": ".$seg[2]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D48', $seg[3]['Subwork'].": ".$seg[3]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D49', $seg[4]['Subwork'].": ".$seg[4]['Obs']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D50', $seg[5]['Subwork'].": ".$seg[5]['Obs']);
				//					Imagenes                    //

				//Primera Foto
				$objPHPExcel->getActiveSheet()->SetCellValue('D52', $seg[0]['Leyend']);
				$name_photo = substr($seg[0]['Photo'],-22);
				$foto1 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 1');
				$objDrawing->setDescription('Trabajo 1');

				list($width, $height) = getimagesize($foto1);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('B53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Segunda foto
				$objPHPExcel->getActiveSheet()->SetCellValue('R52', $seg[1]['Leyend']);
				$name_photo = substr( $seg[1]['Photo'],-22);
				$foto2 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 2');
				$objDrawing->setDescription('Trabajo 2');

				list($width, $height) = getimagesize($foto2);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('P53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Tercera foto
				$objPHPExcel->getActiveSheet()->SetCellValue('AF52', $seg[2]['Leyend']);
				$name_photo = substr( $seg[2]['Photo'],-22);
				$foto3 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 3');
				$objDrawing->setDescription('Trabajo 3');

				list($width, $height) = getimagesize($foto3);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('AD53');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Cuarta foto
				$objPHPExcel->getActiveSheet()->SetCellValue('D67', $seg[3]['Leyend']);
				$name_photo = substr($seg[3]['Photo'],-22);
				$foto4 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 4');
				$objDrawing->setDescription('Trabajo 4');

				list($width, $height) = getimagesize($foto4);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('B68');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Quinta foto
				$objPHPExcel->getActiveSheet()->SetCellValue('R67', $seg[4]['Leyend']);
				$name_photo = substr($seg[4]['Photo'],-22);
				$foto5 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 5');
				$objDrawing->setDescription('Trabajo 5');

				list($width, $height) = getimagesize($foto5);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('P68');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

				//Sexta foto
				$objPHPExcel->getActiveSheet()->SetCellValue('AF67', $seg[5]['Leyend']);
				$name_photo = substr($seg[5]['Photo'],-22);
				$foto6 = '/var/www/senditlaravel42/public/photos/'.$name_photo;

				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto Trabajo 6');
				$objDrawing->setDescription('Trabajo 6');

				list($width, $height) = getimagesize($foto6);

				if ($height > $width) {
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					//$objDrawing->setRotation(90);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetX(80);
					$objDrawing->setOffsetY(4);
				}else{
					$objDrawing->setPath('/var/www/senditlaravel42/public/photos/'.$name_photo);
					$objDrawing->setHeight(345);
					$objDrawing->setOffsetY(4);
					$objDrawing->setOffsetX(1);
				}
				$objDrawing->setCoordinates('AD68');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());


				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header('Content-Disposition: attachment; filename="ReportOut.xlsx"');
				header("Cache-Control: max-age=0");
				$objWriter->save("ReportOut.xlsx");
				$objWriter->save("php://output");
				break;
		}


	}





}//end class
