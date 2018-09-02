<?php
require_once('../tcpdf/config/lang/spa.php');
require_once('../tcpdf/tcpdf.php');
date_default_timezone_set("America/Mexico_City");
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

if(isset($_GET['tallerid']) && ctype_digit($_GET['tallerid']))
	$tallerid = $_GET['tallerid'];
else
	die('Número de taller inválido');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Octavio Parra');
$pdf->SetTitle('Vista de impresión Taller');

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
$ESObject = new talleres( '../' );

$talleresdata  = $ESObject->infotalleres( $tallerid );

// create some HTML content
	$html = '<table border="0" width="90%">
	  <tr>
		<td width="10%"><img src="../imagenes/osito.jpg" width="47" height="84" /></td>
		<td width="90%"><div align="center">REFRISERVICIO Y AIRE ACONDICIONADO PARA EL TRANSPORTE , S.A. DE C.V.
		<br/>
		<span>Taller: '.TildesHtml($talleresdata->fields['nombre']).'</span>
		<br/>
		<span style="font-size:small">Detalle de taller</span>
		</div>
		<div align="right" style="font-size:smaller">Fecha: '.date("d-m-Y").'</div></td>
	  </tr>
	</table>';
	
	$html .= '<br/><br/>
			<table width="100%" border="1" style="padding: 2px;">
				<tr>
					<td style="background-color: #999;">N&uacute;mero de taller:</td>
					<td>'.$talleresdata->fields['tallerid'].'</td>
					<td style="background-color: #999;">C&oacute;digo de taller:</td>
					<td>'.$talleresdata->fields['taller_cod'].'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Nombre:</td>
					<td colspan="3">'.TildesHtml($talleresdata->fields['nombre']).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Direcci&oacute;n:</td>
					<td colspan="3">'.TildesHtml($ESObject->getDireccion($tallerid)).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Cliente:</td>
					<td colspan="3">'.$ESObject->getclientenombre($talleresdata->fields['clienteid']).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Tel&eacute;fono:</td>
					<td colspan="3">'.$talleresdata->fields['telefono'].'</td>
				</tr>
				<tr>
					<td colspan="4" style="background-color: #999;">Contactos:</td>
				</tr>
				<tr>
					<td colspan="4">
						<center>
							<table width="100%" border="1">
								<tr>
									<td>Nombre</td>
									<td>T&eacute;lefono</td>
									<td>E-mail</td>
								</tr>
								'.TildesHtml($ESObject->print_contactos($tallerid)).'
							</table>
						</center>
					</td>
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
