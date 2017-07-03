<?php


class ReportTechController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function connectMongo(){
		$m = new MongoClient();//obsoleta desde mongo 1.0.0
		$db = $m->SenditForm;

		return $db;
	}
	public function generateLinkPhotos($id,$pId,$photo)
	{
		if ($photo == null) {
			return null;
		}else{
	 	$Id = substr($id, 0, 8).'-'.substr($id, 8, 4).'-'.substr($id, 12, 4).'-'.substr($id, 16, 4).'-'.substr($id, 20, 32);
		$link = 'https://app.sendit.cl/Files/FormEntry/'.$pId.'-'.$Id.$photo.'';
	 	return $link;
	 }

 	}
	public function getIndex()
	{

		$collRTech = $this->connectMongo()->RTech;
		$docRTech = $collRTech->find();
		$docRTech = $docRTech->sort(['Entry.StartTime' => -1]);
		return View::make('RT.index', array("docRTech" => $docRTech));
		//$this->layout->content = View::make('ReportTech.index');
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$aRequest = json_decode(file_get_contents('php://input'),true);
		$m = new MongoClient();//obsoleta desde mongo 1.0.0
		$db = $m->SenditForm;
		$collRTech = $db->RTech;
		$db->Json->insert($aRequest);//guardo json original

		/*$Id = substr($id, 0, 8).'-'.substr($id, 8, 4).'-'.substr($id, 12, 4).'-'.substr($id, 16, 4).'-'.substr($id, 20, 32);
		$photo = 'https://app.sendit.cl/Files/FormEntry/'.$aRequest['ProviderId'].'-'.$Id.$aRequest['Entry']['AnswersJson']['PHOTOS']['PHOTO1'].'';*/
		$array = array(
			"ProviderId" => $aRequest['ProviderId'],
			"IntegrationKey" => $aRequest['IntegrationKey'],
			"Entry" => array(
				 "Id" => $aRequest['Entry']['Id'],
				 /*"FormCode" => $aRequest['Entry']['FormCode'],*/
				 "FormVersion" => $aRequest['Entry']['FormVersion'],
				 "UserFirstName" => $aRequest['Entry']['UserFirstName'],
				 "UserLastName" => $aRequest['Entry']['UserLastName'],
				 "UserEmail" => $aRequest['Entry']['UserEmail'],
				 "Latitude" => $aRequest['Entry']['Latitude'],
				 "Longitude" => $aRequest['Entry']['Longitude'],
				 "StartTime" => $aRequest['Entry']['StartTime'],
				 "ReceivedTime" => $aRequest['Entry']['ReceivedTime'],
				 "CompleteTime" => $aRequest['Entry']['CompleteTime'],
				 "Trabajo" => $aRequest['Entry']['AnswersJson']['report_technical']['Trabajo'],
				 "SubTrabajo" => $aRequest['Entry']['AnswersJson']['report_technical']['SubTrabajo']
				),
			"AFAL" => array(
				"Order_manag" => $aRequest['Entry']['AnswersJson']['report_technical']['order_manag'],
				"Mode_fail" =>	$aRequest['Entry']['AnswersJson']['report_technical']['mode_fail'],
				"Equipment" => $aRequest['Entry']['AnswersJson']['report_technical']['equipment'],
				"Equipment_desc" => $aRequest['Entry']['AnswersJson']['report_technical']['equipment_desc'],
				"Code" => $aRequest['Entry']['AnswersJson']['report_technical']['code'],
				"Date" => $aRequest['Entry']['AnswersJson']['report_technical']['date_report_tech'],
				"Report_by" => $aRequest['Entry']['AnswersJson']['report_technical']['report_by'],
				"Company" => $aRequest['Entry']['AnswersJson']['report_technical']['company_exec'],
				"Supervisor" => $aRequest['Entry']['AnswersJson']['report_technical']['supervisor_plant'],
				"Loc" => $aRequest['Entry']['AnswersJson']['report_technical']['Loc_technical']
				),
			"As_found" => array(
				"Parte" => $aRequest['Entry']['AnswersJson']['state_i']['equipment_comp_i'],
				"Findings" => $aRequest['Entry']['AnswersJson']['state_i']['findings_i'],
				"Photo1" => $this->generateLinkPhotos($aRequest['Entry']['Id'],$aRequest['ProviderId'],$aRequest['Entry']['AnswersJson']['state_i']['photo1_i']),
				"Leyend1" => $aRequest['Entry']['AnswersJson']['state_i']['leyend1_i'],
				"Photo2" => $this->generateLinkPhotos($aRequest['Entry']['Id'],$aRequest['ProviderId'],$aRequest['Entry']['AnswersJson']['state_i']['photo2_i']),
				"Leyend2" => $aRequest['Entry']['AnswersJson']['state_i']['leyend2_i'],
				"Med" => $aRequest['Entry']['AnswersJson']['state_i']['med_i'],
				"Pieza" => $aRequest['Entry']['AnswersJson']['state_i']['part_i'],
				"Param" => $aRequest['Entry']['AnswersJson']['state_i']['param_i'],
				"Value" => $aRequest['Entry']['AnswersJson']['state_i']['value_med_i'],
				"Value_ref" => $aRequest['Entry']['AnswersJson']['state_i']['value_ref_i'],
				"Obs" => $aRequest['Entry']['AnswersJson']['state_i']['Obs_i']
				),
			"As_left" => array(
				"Parte" => $aRequest['Entry']['AnswersJson']['equiment_state_final']['equipment_comp_f'],
				"Findings" => $aRequest['Entry']['AnswersJson']['equiment_state_final']['findings_f'],
				"Photo1" => $this->generateLinkPhotos($aRequest['Entry']['Id'],$aRequest['ProviderId'],$aRequest['Entry']['AnswersJson']['equiment_state_final']['photo1_f']),
				"Leyend1" => $aRequest['Entry']['AnswersJson']['equiment_state_final']['leyend1_f'],
				"Photo2" => $this->generateLinkPhotos($aRequest['Entry']['Id'],$aRequest['ProviderId'],$aRequest['Entry']['AnswersJson']['equiment_state_final']['photo2_f']),
				"Leyend2" => $aRequest['Entry']['AnswersJson']['equiment_state_final']['leyend2_f'],
				"Med" => $aRequest['Entry']['AnswersJson']['equiment_state_final']['med_f'],
				"Pieza" => $aRequest['Entry']['AnswersJson']['equiment_state_final']['part_f'],
				"Param" => $aRequest['Entry']['AnswersJson']['equiment_state_final']['param_f'],
				"Value" => $aRequest['Entry']['AnswersJson']['equiment_state_final']['value_med_f'],
				"Value_ref" => $aRequest['Entry']['AnswersJson']['equiment_state_final']['value_ref_f'],
				"Obs" => $aRequest['Entry']['AnswersJson']['equiment_state_final']['obs_f']
				),
			"Comments" => array(
				"Replacement" => $aRequest['Entry']['AnswersJson']['comments']['replacement'],
				"Count" => $aRequest['Entry']['AnswersJson']['comments']['count'],
				"Desc" => $aRequest['Entry']['AnswersJson']['comments']['desc'],
				"Supplied" => $aRequest['Entry']['AnswersJson']['comments']['supplied'],
				"Repairs" => $aRequest['Entry']['AnswersJson']['comments']['repairs'],
				"Manage_insp" => $aRequest['Entry']['AnswersJson']['comments']['manage_insp'],
				"Recommend"=> $aRequest['Entry']['AnswersJson']['comments']["recommend"]
				),
			"Anex" => array(
				"Photo1" => $this->generateLinkPhotos($aRequest['Entry']['Id'],$aRequest['ProviderId'],$aRequest['Entry']['AnswersJson']['Anex']['photo1']),
				"Leyend1" => $aRequest['Entry']['AnswersJson']['Anex']['leyend1'],
				"Photo2"  => $this->generateLinkPhotos($aRequest['Entry']['Id'],$aRequest['ProviderId'],$aRequest['Entry']['AnswersJson']['Anex']['photo2']),
				"Leyend2" => $aRequest['Entry']['AnswersJson']['Anex']['leyend2'],
				"Photo3"  => $this->generateLinkPhotos($aRequest['Entry']['Id'],$aRequest['ProviderId'],$aRequest['Entry']['AnswersJson']['Anex']['photo3']),
				"Leyend3" => $aRequest['Entry']['AnswersJson']['Anex']['leyend3'],
				"Photo4"  => $this->generateLinkPhotos($aRequest['Entry']['Id'],$aRequest['ProviderId'],$aRequest['Entry']['AnswersJson']['Anex']['photo4']),
				"Leyend4" => $aRequest['Entry']['AnswersJson']['Anex']['leyend4'],
				"Photo5"  => $this->generateLinkPhotos($aRequest['Entry']['Id'],$aRequest['ProviderId'],$aRequest['Entry']['AnswersJson']['Anex']['photo5']),
				"Leyend5" => $aRequest['Entry']['AnswersJson']['Anex']['leyend5'],
				"Photo6"  => $this->generateLinkPhotos($aRequest['Entry']['Id'],$aRequest['ProviderId'],$aRequest['Entry']['AnswersJson']['Anex']['photo6']),
				"Leyend6" => $aRequest['Entry']['AnswersJson']['Anex']['leyend6'],
				"Photo7"  => $this->generateLinkPhotos($aRequest['Entry']['Id'],$aRequest['ProviderId'],$aRequest['Entry']['AnswersJson']['Anex']['photo7']),
				"Leyend7" => $aRequest['Entry']['AnswersJson']['Anex']['leyend7'],
				"Photo8"  => $this->generateLinkPhotos($aRequest['Entry']['Id'],$aRequest['ProviderId'],$aRequest['Entry']['AnswersJson']['Anex']['photo8']),
				"Leyend8" => $aRequest['Entry']['AnswersJson']['Anex']['leyend8']
				)
			);

		$collRTech->insert($array);
		echo "Insertado en RTech Collection";
	}



	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
		//echo "hola show ".$id;
		$m = new MongoClient();//obsoleta desde mongo 1.0.0
		$db = $m->SenditForm;
		$collRTech = $db->RTech;
		$rt = $collRTech->findOne(['Entry.Id' => $id]);

		$l = $this->generateLinkPhotos($rt['Entry']['Id'],$rt['ProviderId'],$rt['Entry']['AnswersJson']['state_i']['photo1_i']);
		//echo $l;
		//return Redirect::route('rtech', array("r" => $r));
		return View::make('RT.rtech', array("rt" => $rt));

	}
	public function crearPDF($rt,$vistaurl,$tipo){

		$view =  \View::make("RT.rtech", compact('rt'))->render();
       $pdf = \App::make('dompdf');
       //$pdf = PDF::loadView($view);
        $pdf->loadHTML($view);
        if ($tipo == 1) {
			return $pdf->stream();
			//return View::make('RT.rtech', array("rt" => $rt));
		}else return $pdf->download('reporte.pdf');



	}
	public function exportarToExcel($id)
	{
		//
		//echo "hola PDF ".$id;
		//echo "tipo ".$tipo;
		$m = new MongoClient();//obsoleta desde mongo 1.0.0
		$db = $m->SenditForm;
		$collRTech = $db->RTech;
		$rt = $collRTech->findOne(['Entry.Id' => $id]);

		$objPHPExcel = new PHPExcel();
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$objPHPExcel = $objReader->load("/var/www/senditlaravel42/public/ReportTech.xlsx");
		$objWorksheet= $objPHPExcel->setActiveSheetIndex(0);
		//echo $rt['AFAL']['Mode_fail'];

		//Header

		$objPHPExcel->getActiveSheet()->SetCellValue('B6', $rt['AFAL']['Order_manag']);
		$objPHPExcel->getActiveSheet()->SetCellValue('J6', $rt['AFAL']['Mode_fail']);
		$objPHPExcel->getActiveSheet()->SetCellValue('B8', $rt['AFAL']['Equipment']);
		$objPHPExcel->getActiveSheet()->SetCellValue('J8', $rt['AFAL']['Equipment_desc']);
		$objPHPExcel->getActiveSheet()->SetCellValue('AC6', $rt['AFAL']['Code']);
		$objPHPExcel->getActiveSheet()->SetCellValue('AC8', $rt['AFAL']['Date']);
		$objPHPExcel->getActiveSheet()->SetCellValue('B10', $rt['AFAL']['Report_by']);
		$objPHPExcel->getActiveSheet()->SetCellValue('J10', $rt['AFAL']['Company']);
		$objPHPExcel->getActiveSheet()->SetCellValue('S10', $rt['AFAL']['Supervisor']);
		$objPHPExcel->getActiveSheet()->SetCellValue('AC10', $rt['AFAL']['Loc']);

		//As Found

		$objPHPExcel->getActiveSheet()->SetCellValue('H14', $rt['As_found']['Parte']);
		$objPHPExcel->getActiveSheet()->SetCellValue('S14', $rt['As_found']['Findings']);

		//Photo 1
		if ($rt['As_found']['Photo1']!=null) {
			$objPHPExcel->getActiveSheet()->SetCellValue('D33', $rt['As_found']['Leyend1']);

			$name_photo = substr($rt['As_found']['Photo1'],-22);
			try {
				copy($rt['As_found']['Photo1'],'/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$foto1 = '/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo;
			} catch (Exception $e) {
				return Redirect::to('/report_tech')
                ->with('mensaje_error', 'Intente nuevamente en unos minutos');
			}


			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Foto1 As Found');
			$objDrawing->setDescription('Foto1 As Found 1');

			list($width, $height) = getimagesize($foto1);

			if ($height > $width) {
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				//$objDrawing->setRotation(90);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetX(80);
				$objDrawing->setOffsetY(4);
			}else{
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetY(4);
				$objDrawing->setOffsetX(1);
			}
			$objDrawing->setCoordinates('B18');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}

		//Photo 2

		if ($rt['As_found']['Photo2']!=null) {

			$objPHPExcel->getActiveSheet()->SetCellValue('U33', $rt['As_found']['Leyend2']);
			$name_photo = substr($rt['As_found']['Photo2'],-22);

			try {
				copy($rt['As_found']['Photo2'],'/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$foto2 = '/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo;
			} catch (Exception $e) {
				return Redirect::to('/report_tech')
                ->with('mensaje_error', 'Intente nuevamente en unos minutos');
			}


			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Foto1 As Found');
			$objDrawing->setDescription('Foto1 As Found 1');

			list($width, $height) = getimagesize($foto2);

			if ($height > $width) {
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				//$objDrawing->setRotation(90);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetX(80);
				$objDrawing->setOffsetY(4);
			}else{
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetY(4);
				$objDrawing->setOffsetX(1);
			}
			$objDrawing->setCoordinates('S18');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}

		$objPHPExcel->getActiveSheet()->SetCellValue('B37', $rt['As_found']['Pieza']);
		$objPHPExcel->getActiveSheet()->SetCellValue('H37', $rt['As_found']['Param']);
		$objPHPExcel->getActiveSheet()->SetCellValue('N37', $rt['As_found']['Value']);
		$objPHPExcel->getActiveSheet()->SetCellValue('S37', $rt['As_found']['Value_ref']);
		$objPHPExcel->getActiveSheet()->SetCellValue('Y37', $rt['As_found']['Obs']);

		//As Left

		$objPHPExcel->getActiveSheet()->SetCellValue('H44', $rt['As_left']['Parte']);
		$objPHPExcel->getActiveSheet()->SetCellValue('S44', $rt['As_left']['Findings']);

		//Photo 1
		if ($rt['As_left']['Photo1']!=null) {
			$objPHPExcel->getActiveSheet()->SetCellValue('D65', $rt['As_left']['Leyend1']);

			$name_photo = substr($rt['As_left']['Photo1'],-22);
			try {
				copy($rt['As_left']['Photo1'],'/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$foto1 = '/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo;
			} catch (Exception $e) {
				return Redirect::to('/report_tech')
                ->with('mensaje_error', 'Intente nuevamente en unos minutos');
			}


			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Foto1 As Left');
			$objDrawing->setDescription('Foto1 As Left');

			list($width, $height) = getimagesize($foto1);

			if ($height > $width) {
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				//$objDrawing->setRotation(90);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetX(80);
				$objDrawing->setOffsetY(4);
			}else{
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetY(4);
				$objDrawing->setOffsetX(1);
			}
			$objDrawing->setCoordinates('B50');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}
		//Photo2
		if ($rt['As_left']['Photo2']!=null) {
			$objPHPExcel->getActiveSheet()->SetCellValue('U65', $rt['As_left']['Leyend2']);

			$name_photo = substr($rt['As_left']['Photo2'],-22);
			try {
				copy($rt['As_left']['Photo2'],'/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$foto1 = '/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo;
			} catch (Exception $e) {
				return Redirect::to('/report_tech')
                ->with('mensaje_error', 'Intente nuevamente en unos minutos');
			}


			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Foto2 As Left');
			$objDrawing->setDescription('Foto2 As Left');

			list($width, $height) = getimagesize($foto1);

			if ($height > $width) {
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				//$objDrawing->setRotation(90);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetX(80);
				$objDrawing->setOffsetY(4);
			}else{
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetY(4);
				$objDrawing->setOffsetX(1);
			}
			$objDrawing->setCoordinates('S50');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}
		//mediciones AS left
		$objWorksheet= $objPHPExcel->setActiveSheetIndex(1);
		$objPHPExcel->getActiveSheet()->SetCellValue('B7', $rt['As_left']['Pieza']);
		$objPHPExcel->getActiveSheet()->SetCellValue('H7', $rt['As_left']['Param']);
		$objPHPExcel->getActiveSheet()->SetCellValue('O7', $rt['As_left']['Value']);
		$objPHPExcel->getActiveSheet()->SetCellValue('V7', $rt['As_left']['Value_ref']);
		$objPHPExcel->getActiveSheet()->SetCellValue('AA7', $rt['As_left']['Obs']);

		//Comments
		$objPHPExcel->getActiveSheet()->SetCellValue('H15', $rt['Comments']['Count']);
		$objPHPExcel->getActiveSheet()->SetCellValue('M15', $rt['Comments']['Desc']);
		$objPHPExcel->getActiveSheet()->SetCellValue('AK15', $rt['Comments']['Supplied']);
		$objPHPExcel->getActiveSheet()->SetCellValue('H18', $rt['Comments']['Repairs']);
		$objPHPExcel->getActiveSheet()->SetCellValue('H22', $rt['Comments']['Manage_insp']);
		$objPHPExcel->getActiveSheet()->SetCellValue('H25', $rt['Comments']['Recommend']);

		//Anexo
		$objWorksheet= $objPHPExcel->setActiveSheetIndex(2);

		//Photo 1
		if ($rt['Anex']['Photo1']!=null) {
			$objPHPExcel->getActiveSheet()->SetCellValue('E22', $rt['Anex']['Leyend1']);

			$name_photo = substr($rt['Anex']['Photo1'],-22);
			try {
				copy($rt['Anex']['Photo1'],'/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$foto1 = '/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo;
			} catch (Exception $e) {
				return Redirect::to('/report_tech')
                ->with('mensaje_error', 'Intente nuevamente en unos minutos');
			}


			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Foto1 Anexo');
			$objDrawing->setDescription('Foto1 Anexo');

			list($width, $height) = getimagesize($foto1);

			if ($height > $width) {
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				//$objDrawing->setRotation(90);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetX(80);
				$objDrawing->setOffsetY(4);
			}else{
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetY(4);
				$objDrawing->setOffsetX(1);
			}
			$objDrawing->setCoordinates('B7');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}
		//Photo2
		if ($rt['Anex']['Photo2']!=null) {
			$objPHPExcel->getActiveSheet()->SetCellValue('W22', $rt['Anex']['Leyend2']);

			$name_photo = substr($rt['Anex']['Photo2'],-22);
			try {
				copy($rt['Anex']['Photo2'],'/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$foto1 = '/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo;
			} catch (Exception $e) {
				return Redirect::to('/report_tech')
                ->with('mensaje_error', 'Intente nuevamente en unos minutos');
			}

			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Foto2 Anexo');
			$objDrawing->setDescription('Foto2 Anexo');

			list($width, $height) = getimagesize($foto1);

			if ($height > $width) {
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				//$objDrawing->setRotation(90);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetX(80);
				$objDrawing->setOffsetY(4);
			}else{
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetY(4);
				$objDrawing->setOffsetX(1);
			}
			$objDrawing->setCoordinates('U7');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}
		//Photo 3
		if ($rt['Anex']['Photo3']!=null) {
			$objPHPExcel->getActiveSheet()->SetCellValue('E39', $rt['Anex']['Leyend3']);

			$name_photo = substr($rt['Anex']['Photo3'],-22);
			try {
				copy($rt['Anex']['Photo3'],'/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$foto1 = '/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo;
			} catch (Exception $e) {
				return Redirect::to('/report_tech')
                ->with('mensaje_error', 'Intente nuevamente en unos minutos');
			}

			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Foto3 Anexo');
			$objDrawing->setDescription('Foto3 Anexo');

			list($width, $height) = getimagesize($foto1);

			if ($height > $width) {
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				//$objDrawing->setRotation(90);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetX(80);
				$objDrawing->setOffsetY(4);
			}else{
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetY(4);
				$objDrawing->setOffsetX(1);
			}
			$objDrawing->setCoordinates('B24');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}
		//Photo4
		if ($rt['Anex']['Photo4']!=null) {
			$objPHPExcel->getActiveSheet()->SetCellValue('W39', $rt['Anex']['Leyend4']);

			$name_photo = substr($rt['Anex']['Photo4'],-22);
			try {
				copy($rt['Anex']['Photo4'],'/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$foto1 = '/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo;
			} catch (Exception $e) {
				return Redirect::to('/report_tech')
                ->with('mensaje_error', 'Intente nuevamente en unos minutos');
			}

			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Foto4 Anexo');
			$objDrawing->setDescription('Foto4 Anexo');

			list($width, $height) = getimagesize($foto1);

			if ($height > $width) {
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				//$objDrawing->setRotation(90);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetX(80);
				$objDrawing->setOffsetY(4);
			}else{
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetY(4);
				$objDrawing->setOffsetX(1);
			}
			$objDrawing->setCoordinates('U24');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}
		//Photo 5
		if ($rt['Anex']['Photo5']!=null) {
			$objPHPExcel->getActiveSheet()->SetCellValue('E56', $rt['Anex']['Leyend5']);

			$name_photo = substr($rt['Anex']['Photo5'],-22);
			try {
				copy($rt['Anex']['Photo5'],'/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$foto1 = '/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo;
			} catch (Exception $e) {
				return Redirect::to('/report_tech')
                ->with('mensaje_error', 'Intente nuevamente en unos minutos');
			}

			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Foto5 Anexo');
			$objDrawing->setDescription('Foto5 Anexo');

			list($width, $height) = getimagesize($foto1);

			if ($height > $width) {
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				//$objDrawing->setRotation(90);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetX(80);
				$objDrawing->setOffsetY(4);
			}else{
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetY(4);
				$objDrawing->setOffsetX(1);
			}
			$objDrawing->setCoordinates('B41');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}
		//Photo6
		if ($rt['Anex']['Photo6']!=null) {
			$objPHPExcel->getActiveSheet()->SetCellValue('X56', $rt['Anex']['Leyend6']);

			$name_photo = substr($rt['Anex']['Photo6'],-22);
			try {
				copy($rt['Anex']['Photo6'],'/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$foto1 = '/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo;
			} catch (Exception $e) {
				return Redirect::to('/report_tech')
                ->with('mensaje_error', 'Intente nuevamente en unos minutos');
			}

			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Foto6 Anexo');
			$objDrawing->setDescription('Foto6 Anexo');

			list($width, $height) = getimagesize($foto1);

			if ($height > $width) {
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				//$objDrawing->setRotation(90);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetX(80);
				$objDrawing->setOffsetY(4);
			}else{
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetY(4);
				$objDrawing->setOffsetX(1);
			}
			$objDrawing->setCoordinates('U41');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}
		//Photo 7
		if ($rt['Anex']['Photo7']!=null) {
			$objPHPExcel->getActiveSheet()->SetCellValue('E73', $rt['Anex']['Leyend7']);

			$name_photo = substr($rt['Anex']['Photo7'],-22);
			try {
				copy($rt['Anex']['Photo7'],'/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$foto1 = '/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo;
			} catch (Exception $e) {
				return Redirect::to('/report_tech')
                ->with('mensaje_error', 'Intente nuevamente en unos minutos');
			}

			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Foto7 Anexo');
			$objDrawing->setDescription('Foto7 Anexo');

			list($width, $height) = getimagesize($foto1);

			if ($height > $width) {
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				//$objDrawing->setRotation(90);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetX(80);
				$objDrawing->setOffsetY(4);
			}else{
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetY(4);
				$objDrawing->setOffsetX(1);
			}
			$objDrawing->setCoordinates('B58');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}
		//Photo8
		if ($rt['Anex']['Photo8']!=null) {
			$objPHPExcel->getActiveSheet()->SetCellValue('X73', $rt['Anex']['Leyend8']);

			$name_photo = substr($rt['Anex']['Photo8'],-22);
			try {
				copy($rt['Anex']['Photo8'],'/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$foto1 = '/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo;
			} catch (Exception $e) {
				return Redirect::to('/report_tech')
                ->with('mensaje_error', 'Intente nuevamente en unos minutos');
			}

			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Foto8 Anexo');
			$objDrawing->setDescription('Foto8 Anexo');

			list($width, $height) = getimagesize($foto1);

			if ($height > $width) {
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				//$objDrawing->setRotation(90);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetX(80);
				$objDrawing->setOffsetY(4);
			}else{
				$objDrawing->setPath('/var/www/senditlaravel42/public/photos/ReportTech/'.$name_photo);
				$objDrawing->setHeight(345);
				$objDrawing->setOffsetY(4);
				$objDrawing->setOffsetX(1);
			}
			$objDrawing->setCoordinates('U58');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}



		$objWorksheet= $objPHPExcel->setActiveSheetIndex(0);
		//					Imagenes                    //

		//Primera Foto
		/*$objPHPExcel->getActiveSheet()->SetCellValue('D52', $seg[0]['Leyend']);

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
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());*/

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Disposition: attachment; filename="InformeTecnico.xlsx"');
		header("Cache-Control: max-age=0");
		$objWriter->save("ReportTechOut.xlsx");
		$objWriter->save("php://output");

	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//echo "hola PDF ".$id;
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
