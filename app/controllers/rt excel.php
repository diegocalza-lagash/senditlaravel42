/*$objPHPExcel = new PHPExcel();
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$objPHPExcel = $objReader->load("/var/www/senditlaravel42/public/ReportTech.xlsx");
		$objWorksheet= $objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->SetCellValue('B6', $r['Entry']['AnswersJson']['report_technical']['order_manag']);
		$objPHPExcel->getActiveSheet()->SetCellValue('J6', $r['Entry']['AnswersJson']['report_technical']['mode_fail']);
		$objPHPExcel->getActiveSheet()->SetCellValue('AC6', $r['Entry']['AnswersJson']['report_technical']['code']);
		$objPHPExcel->getActiveSheet()->SetCellValue('B8', $r['Entry']['AnswersJson']['report_technical']['equipment']);
		$objPHPExcel->getActiveSheet()->SetCellValue('J8', $r['Entry']['AnswersJson']['report_technical']['equipment_desc']);
		$objPHPExcel->getActiveSheet()->SetCellValue('AC8', $r['Entry']['AnswersJson']['report_technical']['date_report_tech']);
		$objPHPExcel->getActiveSheet()->SetCellValue('B10', $r['Entry']['AnswersJson']['report_technical']['report_by']);
		$objPHPExcel->getActiveSheet()->SetCellValue('J10', $r['Entry']['AnswersJson']['report_technical']['company_exec']);
		$objPHPExcel->getActiveSheet()->SetCellValue('S10', $r['Entry']['AnswersJson']['report_technical']['supervisor_plant']);
		$objPHPExcel->getActiveSheet()->SetCellValue('AC10', $r['Entry']['AnswersJson']['report_technical']['Loc_technical']);

		//para PDF
		$rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
		$rendererLibrary = 'domPDF0.6.0beta3';
		$rendererLibraryPath = dirname(__FILE__). 'libs/classes/dompdf' . $rendererLibrary;
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
		$objWriter->save("ReportTechOut.xlsx");
		$objWriter->save("ReportTechOut.pdf");*/