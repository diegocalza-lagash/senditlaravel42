<?php


    /**
    * Create excel by from direct request
    */
    function xlscreation_direct() {

        $reportdetails = report_details();

        require_once SITEPATH . 'PHPExcel/Classes/PHPExcel.php';

         $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
                ->setCreator("user")
                ->setLastModifiedBy("user")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");

        // Set the active Excel worksheet to sheet 0
        $objPHPExcel->setActiveSheetIndex(0);

        // Initialise the Excel row number
        $rowCount = 0;

        // Sheet cells
        $cell_definition = array(
            'A' => 'BrandIcon',
            'B' => 'Comapany',
            'C' => 'Rank',
            'D' => 'Link'
        );

        // Build headers
        foreach( $cell_definition as $column => $value )
        {
            $objPHPExcel->getActiveSheet()->getColumnDimension("{$column}")->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->setCellValue( "{$column}1", $value );
        }

        // Build cells
        while( $rowCount < count($reportdetails) ){
            $cell = $rowCount + 2;
            foreach( $cell_definition as $column => $value ) {

                $objPHPExcel->getActiveSheet()->getRowDimension($rowCount + 2)->setRowHeight(35);

                switch ($value) {
                    case 'BrandIcon':
                        if (file_exists($reportdetails[$rowCount][$value])) {
                            $objDrawing = new PHPExcel_Worksheet_Drawing();
                            $objDrawing->setName('Customer Signature');
                            $objDrawing->setDescription('Customer Signature');
                            //Path to signature .jpg file
                            $signature = $reportdetails[$rowCount][$value];
                            $objDrawing->setPath($signature);
                            $objDrawing->setOffsetX(25);                     //setOffsetX works properly
                            $objDrawing->setOffsetY(10);                     //setOffsetY works properly
                            $objDrawing->setCoordinates($column.$cell);             //set image to cell
                            $objDrawing->setWidth(32);
                            $objDrawing->setHeight(32);                     //signature height
                            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());  //save
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValue($column.$cell, "Image not found" );
                        }
                        break;
                    case 'Link':
                        //set the value of the cell
                        $objPHPExcel->getActiveSheet()->SetCellValue($column.$cell, $reportdetails[$rowCount][$value]);
                        //change the data type of the cell
                        $objPHPExcel->getActiveSheet()->getCell($column.$cell)->setDataType(PHPExcel_Cell_DataType::TYPE_STRING2);
                        ///now set the link
                        $objPHPExcel->getActiveSheet()->getCell($column.$cell)->getHyperlink()->setUrl(strip_tags($reportdetails[$rowCount][$value]));
                        break;

                    default:
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$cell, $reportdetails[$rowCount][$value] );
                        break;
                }

            }

            $rowCount++;
        }

        $rand = rand(1234, 9898);
        $presentDate = date('YmdHis');
        $fileName = "report_" . $rand . "_" . $presentDate . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        die();
    }

?>