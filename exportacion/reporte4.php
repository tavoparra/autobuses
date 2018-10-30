<?php
//ini_set('display_errors', '1');
//error_reporting(E_ALL);

require_once('../tcpdf/config/lang/spa.php');
require_once('../tcpdf/tcpdf.php');
date_default_timezone_set("America/Mexico_City");


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Octavio Parra');
$pdf->SetTitle('Reporte de Consumo de Refacciones');



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


// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();



include_once('../config/varload.inc.php');
include_once('functions/functions.inc.php');

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new exportacion( '../' );

$fecha1 = $_POST['start_date_report4'];
$dia = substr($fecha1, 0, 2);
$mes   = substr($fecha1, 3, 2);
$ano = substr($fecha1, -4);
// fechal final realizada el cambio de formato a las fechas europeas
$fecha1 = $ano . '-' . $mes . '-' . $dia;
$fecha2 = $_POST['end_date_report4'];
$dia2 = substr($fecha2, 0, 2);
$mes2   = substr($fecha2, 3, 2);
$ano2 = substr($fecha2, -4);
// fechal final realizada el cambio de formato a las fechas europeas
$fecha2 = $ano2 . '-' . $mes2 . '-' . $dia2;

$clienteid = $_POST['clienteid3'];
$tallerid = $_POST['tallerid3'];
$unidadid = $_POST['unidadid3'];

$filtro = '1';
if($clienteid > 0)
	$filtro .= ' AND c.clienteid = '.$clienteid;
if($tallerid > 0)
	$filtro .= ' AND o.taller_servicio = '.$tallerid;
if($unidadid > 0)
	$filtro .= ' AND o.unidadid = '.$unidadid;

$filtro .= ' AND o.fecha_orden >= "'.$fecha1.'" AND o.fecha_orden <= "'.$fecha2.' 23:59:59"';

$refacciones_info = $ESObject->getrefaccionesinfo($filtro);
$meses = array(1=>"Enero", 2=>"Febrero", 3=>"Marzo", 4=>"Abril", 5=>"Mayo", 6=>"Junio", 
			7=>"Julio", 8=>"Agosto", 9=>"Septiembre", 10=>"Octubre", 11=>"Noviembre", 12=>"Diciembre" );

if ($_POST['excel'] === 'true') {
	require_once('./reporte4_excel.php');
	die;
}

// create some HTML content
	$html = '<table border="0" width="90%">
	  <tr>
		<td width="25%"><img src="../imagenes/osito.jpg" width="65" height="92" /></td>
		<td width="50%">
			<div align="center">
			<strong>Reporte de Consumo de Refacciones </strong>
			<br/>';
		
			if($clienteid > 0) $html .= "<u><i>".htmlspecialchars($refacciones_info->fields['cliente'])."</i></u>";
			if($unidadid > 0) $html .= "<br/>Unidad: ".htmlspecialchars($refacciones_info->fields['numEconomico']);
	
	$html .='<br/>';
	
	if($tallerid > 0) $html .= htmlspecialchars($refacciones_info->fields['taller']);
	
	$html .=	'
		<br/><span style="font-size: small;">'.(int)$dia." de ".$meses[(int)$mes]." de ".$ano." al ".(int)$dia2." de ".$meses[(int)$mes2]." de ".$ano2.'</span></div>
		</td>
		<td align="right"><img src="../imagenes/carrier_transicold.jpg" height="92" /></td>
	  </tr>
	  <tr>
		<td></td>
		<td></td>
		<td><div align="right" style="font-size:smaller">Fecha: '.date("d-m-Y").'</div></td>
	  </tr>
	</table>';
      	  
		  
		  
			
		  
	$html .= '<br/>
	  </h2>
  </p>
  </div>
	<table width="100%" border="1" cellpadding="5" style="border-top-style: double;">
  <tr>
	<td width="45px">NO</td>
    <td width="125px"># de parte</td>
    <td width="295px">Descripción</td>
    <td align="center" width="80px">Cantidad</td>
	<td align="center">Medida</td>
  </tr>
 ';

if ( !$refacciones_info->EOF )
{	
	$line = 1;
    do{
        $html .= '
		<tr>
			<td align="right">'.$line.'</td>
			<td>'.htmlspecialchars($refacciones_info->fields['code']).'</td>
			<td>'.htmlspecialchars($refacciones_info->fields['desc']).'</td>
			<td align="center">'.(float)number_format($refacciones_info->fields['cantidad'],3).'</td>
			<td align="center">'.htmlspecialchars($refacciones_info->fields['medida']).'</td>
		</tr>';
		$refacciones_info->MoveNext();
		$line++;
    }
    while ( !$refacciones_info->EOF );
	
	$html .= '';
	
}
else
{
    //Si no encuentra ningun registro
    $html .= '<tr>
    <td colspan="4"><div align="center">No se encontraron datos con los parametros seleccionados</div></td>
  </tr>';
}

	$html .= '</table>';
// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('reporte_'.date("Ymd").'.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+