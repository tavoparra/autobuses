<?php
//ini_set('display_errors', '1');
//error_reporting(E_ALL);

require_once('../tcpdf/config/lang/spa.php');
require_once('../tcpdf/tcpdf.php');
date_default_timezone_set("America/Mexico_City");

// create new PDF document
$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Octavio Parra');
$pdf->SetTitle('Agenda de mantenimientos preventivos');

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// add a page
$pdf->AddPage();


include_once('../config/varload.inc.php');
include_once('functions/functions.inc.php');

//Si vamos a usar la base de datos inicializamos el objeto de conexion
$ESObject = new exportacion( '../' );

$clienteid = $_POST['clienteid5'];
$tallerid = $_POST['tallerid5'];
$fecha_inicio  = $_POST['start_date_report5'] == '' ? date("Y-m-d") : $ESObject->formatSqlDate($_POST['start_date_report5']);
$fecha_final  =  $_POST['end_date_report5'] == '' ? '' : $ESObject->formatSqlDate($_POST['end_date_report5']);
$tipo_mantenimiento = implode(",",$_POST['tipo_mantenimiento5']);

$filtro = ' o.fecha_prox >= "'.$fecha_inicio.'"';

if ($fecha_final != '')
	$filtro .= ' AND o.fecha_prox <= "'.$fecha_final.' 23:59:59"';
if($clienteid > 0)
	$filtro .= ' AND c.clienteid = '.$clienteid;
if($tallerid > 0)
	$filtro .= ' AND o.taller_servicio = '.$tallerid;

$filtro .= ' AND o.tipo_prox IN ('.$tipo_mantenimiento.")";

$proximos_mantenimientos = $ESObject->getProximosServicios($filtro);

$pdf->Image('../imagenes/osito.jpg', 15, 10, 8, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

$pdf->SetFont('dejavusans', '', 14);
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196),'opacity'=>.8, 'blend_mode'=>'Normal'));
$pdf->SetX(PDF_MARGIN_LEFT);
$pdf->Cell(0, 0, 'Agenda de mantenimientos preventivos '.date("Y"), 0, 1, 'C', 0, '', 0, false, 'M', 'M');

$pdf->setFontSize(10);
$cliente_nombre = $clienteid > 0 ? "Cliente: ".$ESObject->getClienteName($clienteid)." " : "";
$taller_nombre = $tallerid > 0 ? " Taller: ".$ESObject->getTallerName($tallerid) : "";

$pdf->MultiCell(0,0,$cliente_nombre." ".$taller_nombre, 0, "C", 0);

$rango = $_POST['start_date_report5'] == '' ? "A partir del ".date("d-m-Y") : $_POST['start_date_report5'];
if($_POST['end_date_report5'] != '') $rango .= ' al '.$_POST['end_date_report5'];
$pdf->setFontSize(7);
$pdf->MultiCell(0,0,$rango, 0, "C", 0);
$pdf->MultiCell(0,0,'Fecha: '.date("d-m-Y"), 0, "C", 0);


$pdf->Image('../imagenes/carrier_transicold.jpg', 259, 10, '', 14, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

$pdf->SetXY(PDF_MARGIN_LEFT,28);
$pdf->SetFont('dejavusans', 'B', 7);
$pdf->SetFillColor(225, 225, 225);
//Total width 267
$pdf->Cell(21, 0, "# ecónomico", 1, 0, 'C', 1, '', 0);
$pdf->Cell(20, 0, "Placas", 1, 0, 'C', 1, '', 0);
$pdf->Cell(20, 0, "Marca", 1, 0, 'C', 1, '', 0);
$pdf->Cell(54, 0, "Cliente", 1, 0, 'C', 1, '', 0);
$pdf->Cell(21, 0, "Tipo", 1, 0, 'C', 1, '', 0);
$pdf->Cell(12, 0, "Modelo", 1, 0, 'C', 1, '', 0);
$pdf->Cell(20, 0, "Equipo", 1, 0, 'C', 1, '', 0);
$pdf->Cell(21, 0, "Serie del equipo", 1, 0, 'C', 1, '',1);
$pdf->Cell(39, 0, "Último mantenimiento", 1, 0, 'C', 1, '');
$pdf->SetFillColor(255, 150, 150);
$pdf->Cell(39, 0, "Próximo mantenimiento", 1, 1, 'C', 1, '');


while(!$proximos_mantenimientos->EOF){
	$ultimoServicio = $ESObject->getUltimoServicio($proximos_mantenimientos->fields['unidadid']);
	
	$pdf->SetFont('dejavusans', '', 7);
	$pdf->Cell(21, 0, $proximos_mantenimientos->fields["numEconomico"], 1, 0, 'C', 0, '', 1);
	$pdf->Cell(20, 0, $proximos_mantenimientos->fields["placas"], 1, 0, 'C', 0, '', 1);
	$pdf->Cell(20, 0, $proximos_mantenimientos->fields["marca"], 1, 0, 'C', 0, '', 1);
	$pdf->Cell(54, 0, $proximos_mantenimientos->fields["cliente"], 1, 0, 'C', 0, '', 1);
	$pdf->Cell(21, 0, $proximos_mantenimientos->fields["tipo"], 1, 0, 'C', 0, '', 1);
	$pdf->Cell(12, 0, $proximos_mantenimientos->fields["modelo"], 1, 0, 'C', 0, '', 1);
	$pdf->Cell(20, 0, $proximos_mantenimientos->fields["modeloEquipo"], 1, 0, 'C', 0, '', 1);
	$pdf->Cell(21, 0, $proximos_mantenimientos->fields["serieEquipo"], 1, 0, 'C', 0, '',1);
	$pdf->Cell(20, 0, $ultimoServicio["fechaServicio"], 1, 0, 'C', 0, '',1);
	$pdf->Cell(19, 0, $ultimoServicio["tipoServicio"], 1, 0, 'C', 0, '',1);
	$pdf->SetFont('dejavusans', 'B', 7);
	$pdf->Cell(20, 0, $proximos_mantenimientos->fields["fecha_prox"], 1, 0, 'C', 0, '',1);
	$pdf->Cell(19, 0, $proximos_mantenimientos->fields["prox_mantenimiento"], 1, 1, 'C', 0, '',1);
	$proximos_mantenimientos->MoveNext();
}

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('agenda_mantenimientos_'.date("Ymd").'.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+