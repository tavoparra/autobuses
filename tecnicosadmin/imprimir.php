<?php
require_once('../tcpdf/config/lang/spa.php');
require_once('../tcpdf/tcpdf.php');
date_default_timezone_set("America/Mexico_City");
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

if(isset($_GET['tecnicoid']) && ctype_digit($_GET['tecnicoid']))
	$tecnicoid = $_GET['tecnicoid'];
else
	die('Número de técnico inválido');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Octavio Parra');
$pdf->SetTitle('Vista de impresión Técnico');

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
$ESObject = new tecnicos( '../' );

$tecnicodata  = $ESObject->infotecnico( $tecnicoid );
$status = ($tecnicodata->fields['status'] == 2) ? 'Baja' : 'Activo';
// create some HTML content
	$html = '<table border="0" width="90%">
	  <tr>
		<td width="10%"><img src="../imagenes/osito.jpg" width="47" height="84" /></td>
		<td width="90%"><div align="center">REFRISERVICIO Y AIRE ACONDICIONADO PARA EL TRANSPORTE , S.A. DE C.V.
		<br/>
		<span>Art&iacute;culo: '.TildesHtml($tecnicodata->fields['nombre']." ".$tecnicodata->fields['apeido_pat']." ".$tecnicodata->fields['apeido_mat']).'</span>
		<br/>
		<span style="font-size:small">Detalle de art&iacute;culo</span>
		</div>
		<div align="right" style="font-size:smaller">Fecha: '.date("d-m-Y").'</div></td>
	  </tr>
	</table>';
	
	$html .= '<br/><br/>
			<table width="100%" border="1" style="padding: 2px;">
				<tr>
					<td style="background-color: #999;">N&uacute;mero de t&eacute;cnico:</td>
					<td>'.$tecnicodata->fields['tecnicoid'].'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">C&oacute;digo de t&eacute;cnico:</td>
					<td>'.$tecnicodata->fields['codigo'].'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Nombre:</td>
					<td>'.TildesHtml($tecnicodata->fields['nombre']).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Apellido Paterno:</td>
					<td>'.TildesHtml($tecnicodata->fields['apeido_pat']).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Apellido Materno:</td>
					<td>'.TildesHtml($tecnicodata->fields['apeido_mat']).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Status:</td>
					<td>'.$status.'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Puesto:</td>
					<td>'.TildesHtml($tecnicodata->fields['puesto']).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Salario:</td>
					<td>$'.number_format($tecnicodata->fields['salario'],2).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">NSS:</td>
					<td>'.$tecnicodata->fields['nss'].'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">RFC:</td>
					<td>'.$tecnicodata->fields['rfc'].'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">CURP:</td>
					<td>'.$tecnicodata->fields['curp'].'</td>
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
