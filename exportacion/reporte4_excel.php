<?php 
include_once("../excel_example/lib/PHPExcel/PHPExcel.php"); 
error_reporting(E_ERROR | E_PARSE);

$prueba = new PHPExcel(); 
$prueba->setActiveSheetIndex(0)->setCellValue("A1","Cliente:"); 
$prueba->setActiveSheetIndex(0)->setCellValue("B1",$refacciones_info->fields['cliente']); 
if($unidadid > 0) {
    $prueba->setActiveSheetIndex(0)->setCellValue("C1","Unidad:"); 
    $prueba->setActiveSheetIndex(0)->setCellValue("D1",$refacciones_info->fields['numEconomico']); 
}
if($tallerid > 0) {
    $prueba->setActiveSheetIndex(0)->setCellValue("E1","Taller:"); 
    $prueba->setActiveSheetIndex(0)->setCellValue("F1",$refacciones_info->fields['taller']); 
}

$prueba->getActiveSheet()->mergeCells('A2:E2');
$prueba->setActiveSheetIndex(0)->setCellValue("A2",(int)$dia." de ".$meses[(int)$mes]." de ".$ano." al ".(int)$dia2." de ".$meses[(int)$mes2]." de ".$ano2); 

$prueba->getActiveSheet()->getStyle("A2")->applyFromArray(array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    )
));

$prueba->setActiveSheetIndex(0)->setCellValue("A3", 'NO'); 
$prueba->setActiveSheetIndex(0)->setCellValue("B3", '# de parte'); 
$prueba->setActiveSheetIndex(0)->setCellValue("C3", 'Descripción'); 
$prueba->setActiveSheetIndex(0)->setCellValue("D3", 'Cantidad'); 
$prueba->setActiveSheetIndex(0)->setCellValue("E3", 'Medida'); 

$prueba->getActiveSheet()->getStyle('A3:E3')->getFill()->applyFromArray(array(
    'type' => PHPExcel_Style_Fill::FILL_SOLID,
    'startcolor' => array(
            'rgb' => 'bcc8e1'
    )
));

if ( !$refacciones_info->EOF ) {	
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Reporte Consumo de Refacciones '.$refacciones_info->fields['cliente'].'.xlsx"');
	$line = 4;
    do{
        $prueba->setActiveSheetIndex(0)->setCellValue("A".$line, $line); 
        $prueba->setActiveSheetIndex(0)->setCellValue("B".$line, $refacciones_info->fields['code']); 
        $prueba->setActiveSheetIndex(0)->setCellValue("C".$line, $refacciones_info->fields['desc']); 
        $prueba->setActiveSheetIndex(0)->setCellValue("D".$line, str_replace(",","",number_format($refacciones_info->fields['cantidad'],3)) + 0); 
        $prueba->setActiveSheetIndex(0)->setCellValue("E".$line, $refacciones_info->fields['medida']); 
		$refacciones_info->MoveNext();
		$line++;
    } while ( !$refacciones_info->EOF );
}

$prueba->getActiveSheet()->setTitle("Reporte Consumo de Refacciones"); 

foreach(range('A','F') as $columnID) {
    $prueba->getActiveSheet()->getColumnDimension($columnID)
        ->setAutoSize(true);
}

$objWriter = PHPExcel_IOFactory::createWriter($prueba, 'Excel2007'); 

ob_end_clean();
$objWriter->save('php://output'); 
exit;
?> 
