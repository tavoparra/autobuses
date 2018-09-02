<?php
require_once('../tcpdf/config/lang/spa.php');
require_once('../tcpdf/tcpdf.php');
date_default_timezone_set("America/Mexico_City");
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

if(isset($_GET['unidadid']) && ctype_digit($_GET['unidadid']))
	$unidadID = $_GET['unidadid'];
else
	die('Número de unidad inválido');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Octavio Parra');
$pdf->SetTitle('Vista de impresión Unidad');

$count = 0;

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
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196),'opacity'=>.8, 'blend_mode'=>'Normal'));

// add a page
$pdf->AddPage();

include_once('../config/varload.inc.php');
include_once('functions/functions.inc.php');

//Si vamos a usar la base de datos inicializamos el objeto de conexion
$ESObject = new units( '../' );

$unidaddata  = $ESObject->infoUnits( $unidadID );

// create some HTML content
	$html = '<table border="0" width="90%">
	  <tr>
		<td width="10%"><img src="../imagenes/osito.jpg" width="47" height="84" /></td>
		<td width="90%"><div align="center">REFRISERVICIO Y AIRE ACONDICIONADO PARA EL TRANSPORTE , S.A. DE C.V.
		<br/>
		<span>Unidad: '.TildesHtml($unidaddata->fields['numEconomico']).'</span>
		<br/>
		<span style="font-size:small">Detalle de unidad</span>
		</div>
		<div align="right" style="font-size:smaller">Fecha: '.date("d-m-Y").'</div></td>
	  </tr>
	</table>';
	
	$html .= '<br/><br/>
			<table width="100%" border="1" style="padding: 2px;">
				<tr>
					<td style="background-color: #999;">N&uacute;mero de unidad:</td>
					<td>'.$unidaddata->fields['unidadID'].'</td>
					<td style="background-color: #999;">N&uacute;mero ec&oacute;nomico:</td>
					<td>'.TildesHtml($unidaddata->fields['numEconomico']).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Cliente:</td>
					<td colspan="3">'.TildesHtml($ESObject->getclientenombre($unidaddata->fields['clienteID'])).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Taller:</td>
					<td colspan="3">'.TildesHtml($ESObject->gettallernombre($unidaddata->fields['tallerID'])).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Tipo:</td>
					<td>'.$unidaddata->fields['tipo'].'</td>
					<td style="background-color: #999;">Marca:</td>
					<td>'.TildesHtml($unidaddata->fields['marca']).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Modelo:</td>
					<td>'.$unidaddata->fields['modelo'].'</td>
					<td style="background-color: #999;">Placas:</td>
					<td>'.TildesHtml($unidaddata->fields['placas']).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;"># serie chasis:</td>
					<td>'.$unidaddata->fields['numSerie'].'</td>
					<td style="background-color: #999;">Id de garant&iacute;a:</td>
					<td>'.TildesHtml($unidaddata->fields['garantiaID']).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Equipo:</td>
					<td>Marca: '.$unidaddata->fields['marcaEquipo'].'</td>
					<td>Modelo: '.$unidaddata->fields['modeloEquipo'].'</td>
					<td>Serie: '.$unidaddata->fields['serieEquipo'].'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Caja:</td>
					<td>Marca: '.$unidaddata->fields['marcaCaja'].'</td>
					<td>Modelo: '.$unidaddata->fields['modeloCaja'].'</td>
					<td>Serie: '.$unidaddata->fields['serieCaja'].'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Condensador:</td>
					<td>Marca: '.$unidaddata->fields['marcaCondensador'].'</td>
					<td>Modelo: '.$unidaddata->fields['modeloCondensador'].'</td>
					<td>Serie: '.$unidaddata->fields['serieCondensador'].'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Evaporador:</td>
					<td>Marca: '.$unidaddata->fields['marcaEvaporador'].'</td>
					<td>Modelo: '.$unidaddata->fields['modeloEvaporador'].'</td>
					<td>Serie: '.$unidaddata->fields['serieEvaporador'].'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Compresor:</td>
					<td>Marca: '.$unidaddata->fields['marcaCompresor'].'</td>
					<td>Modelo: '.$unidaddata->fields['modeloCompresor'].'</td>
					<td>Serie: '.$unidaddata->fields['serieCompresor'].'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Motor:</td>
					<td>Marca: '.$unidaddata->fields['marcaMotor'].'</td>
					<td>Modelo: '.$unidaddata->fields['modeloMotor'].'</td>
					<td>Serie: '.$unidaddata->fields['serieMotor'].'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Microprocesador:</td>
					<td>Marca: '.$unidaddata->fields['marcaMicro'].'</td>
					<td>Modelo: '.$unidaddata->fields['modeloMicro'].'</td>
					<td>Serie: '.$unidaddata->fields['serieMicro'].'</td>
				</tr>
			</table>';
	
// output the HTML content
//die(TildesHtml($html));
$pdf->writeHTML($html, true, false, true, false, '');



// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('reporte_'.date("Ymd").'.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+

function TildesHtml($cadena) 
{ 
    return str_replace(array("á","é","í","ó","ú","ñ","Á","É","Í","Ó","Ú","Ñ"),
                                     array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&ntilde;",
                                                "&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;"), $cadena);     
}
