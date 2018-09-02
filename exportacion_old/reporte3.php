<?php
require_once('../tcpdf/config/lang/spa.php');
require_once('../tcpdf/tcpdf.php');
date_default_timezone_set("America/Mexico_City");

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Octavio Parra');
$pdf->SetTitle('Reporte de recepcion de unidad');

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// set font
$pdf->SetFont('dejavusans', '', 11);

// add a page
$pdf->AddPage();

include_once('../config/varload.inc.php');
include_once('functions/functions.inc.php');

//Si vamos a usar la base de datos inicializamos el objeto de conexion
$ESObject = new exportacion( '../' );

$order_id = $_POST['order_number'];
if($order_id != ''){
	$orden_info = $ESObject->getordenesinfo2($order_id, $_POST['number_type']);
	if($orden_info->EOF)
		die("No se encontró la orden");
}

if($orden_info->fields["fecha_orden"] != '0000-00-00 00:00:00' && !empty($orden_info->fields["fecha_orden"]))
{
	$fechaorden		= date("d-m-Y",strtotime($orden_info->fields["fecha_orden"]));
	$horaorden		= substr($orden_info->fields["fecha_orden"],11,5);
}
if($orden_info->fields["fecha_orden2"] != '0000-00-00 00:00:00' && !empty($orden_info->fields["fecha_orden2"]))
{
	$fechaorden2	= date("d-m-Y",strtotime($orden_info->fields["fecha_orden2"]));
	$horaorden2		= substr($orden_info->fields["fecha_orden2"],11,5);
}
	

$horas_est = substr($orden_info->fields['tiempo_estimado'],0,2);
$minutos_est = substr($orden_info->fields['tiempo_estimado'],3,2);
$horas_real = substr($orden_info->fields['tiempo_real'],0,2);
$minutos_real = substr($orden_info->fields['tiempo_real'],3,2);

// create some HTML content
	$html = '<table width="100%" border="0">
  <tr>
    <td width="45%" rowspan="2"><center><h3><img src="../imagenes/carrier_transicold.jpg"/></h3></center></td>
    <td width="15%" rowspan="2"><center><img src="../imagenes/osito.jpg"/></center></td>
    <td style="padding-right:10px; font-size:36px"><div align="right">CONTROL INTERNO: &nbsp;&nbsp;&nbsp;&nbsp;</div></td>
    <td><span style="color:red; font-size:36px; vertical-align:central; padding-left:10px;">No. '.$orden_info->fields['ordenid'].'</span></td>
  </tr>
  <tr>
	<td colspan="2">
		<br/><div align="center">Fecha: '.date("d-m-Y").'</div>
	</td>
  </tr>
</table>
<p>
<table width="100%" border="0">
  <tr>
    <td width="25%"></td>
    <td width="50%"><center><h1>RECEPCI&Oacute;N DE UNIDAD</h1></center></td>
    <td width="25%"></td>
  </tr>
</table>
</p>

<table width="100%" border="1" cellpadding="5" cellspacing="1" style="padding:5px 5px 5px 5px;">
  <tr>
    <td colspan="2">Cliente: '.$orden_info->fields['nombre'].'</td>
    <td>No. cliente: '.$orden_info->fields['cliente_cod'].'</td>
    <td>Orden de rep: '.$orden_info->fields['folio'].'</td>
  </tr>
  <tr>
    <td>Tipo: '.$orden_info->fields['tipo'].'</td>
    <td>Marca: '.$orden_info->fields['marca'].'</td>
    <td>Modelo: '.$orden_info->fields['modelo'].'</td>
    <td>Placas: '.$orden_info->fields['placas'].'</td>
  </tr>
  <tr>
    <td colspan="2">Serie chasis: '.$orden_info->fields['numSerie'].'</td>
    <td colspan="2">No. Eco. Unidad: '.$orden_info->fields['numEconomico'].'</td>
  </tr>
  <tr>
    <td colspan="2">Nombre del operador: '.$orden_info->fields['operador'].'</td>
    <td colspan="2">Serie Equipo: '.$orden_info->fields['serieEquipo'].'</td>
  </tr>
  <tr>
    <td colspan="2">Modelo Equipo: '.$orden_info->fields['modeloEquipo'].'</td>
    <td colspan="2">Serie compresor: '.$orden_info->fields['serieCompresor'].'</td>
  </tr>
  <tr>
    <td>Fecha entrada: '.$fechaorden.'</td>
    <td>Fecha salida: '.$fechaorden2.'</td>
    <td colspan="2">Tiempo estimado: '.$orden_info->fields['dias_estimado'].' dias, '.$horas_est.' horas y '.$minutos_est.' minutos</td>
  </tr>
  <tr>
    <td>Hora entrada: '.$horaorden.'</td>
    <td>Hora salida: '.$horaorden2.'</td>
    <td colspan="2">Tiempo real: '.$orden_info->fields['dias_real'].' dias, '.$horas_real.' horas y '.$minutos_est.' minutos</td>
  </tr>
  <tr>
    <td colspan="4">Reporta falla: '.htmlspecialchars($orden_info->fields['reporta']).'</td>
  </tr>
  <tr >	
		<td colspan="4">
				Tel&eacute;fono(s): '.$orden_info->fields['telefono'].'
		</td>
  </tr>
  <tr>
    <td colspan="4">Trabajo realizado: '.htmlspecialchars($orden_info->fields['trabajos']).' <br/><br/></td>
  </tr>
  <tr>
    <td colspan="4">Nombre t&eacute;cnico(s): '.htmlspecialchars($orden_info->fields['tecnico']).'</td>
  </tr>
  <tr>
    <td colspan="2"><img src="../imagenes/truck.gif" height="100px"/></td>
    <td colspan="2" style:"text-align:right;"><table border="0"><tr><td width="50%"></td><td><img src="../imagenes/truck2.gif" height="100px"/></td></tr></table></td>
  </tr>
</table>
<br/><br/><P>
<table width="100%" border="0">
  <tr>
    <td width="25%"></td>
    <td width="50%"><center><h4>_____________________________________________<br/>FIRMA DE CONFORMIDAD DE OPERADOR</h4></center></td>
    <td width="25%"></td>
  </tr>
</table></p>
 ';

	
// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('recepcion_unidad_'.date("Ymd").'.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+