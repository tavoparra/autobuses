<?php 
include_once("lib/PHPExcel/PHPExcel.php"); 

$prueba = new PHPExcel(); 
$prueba->setActiveSheetIndex(0)->setCellValue("A1","PRUEBA"); 

$prueba->getActiveSheet()->setTitle("Hoja de prueba"); 

$objWriter = PHPExcel_IOFactory::createWriter($prueba, 'Excel2007'); 
$objWriter->save('prueba.xlsx'); 

?> 