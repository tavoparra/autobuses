<?php
require_once('../tcpdf/config/lang/spa.php');
require_once('../tcpdf/tcpdf.php');
date_default_timezone_set("America/Mexico_City");
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

if(isset($_GET['articleid']) && ctype_digit($_GET['articleid']))
	$id = $_GET['articleid'];
else
	die('Número de artículo inválido');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Octavio Parra');
$pdf->SetTitle('Vista de impresión Artículo');

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
$ESObject = new articles( '../' );

$articledata  = $ESObject->infoArticles( $id );

$moneda = ($articledata->fields['dollars'] == 1) ? 'D&oacute;lares' : 'Pesos';

// create some HTML content
	$html = '<table border="0" width="90%">
	  <tr>
		<td width="10%"><img src="../imagenes/osito.jpg" width="47" height="84" /></td>
		<td width="90%"><div align="center">REFRISERVICIO Y AIRE ACONDICIONADO PARA EL TRANSPORTE , S.A. DE C.V.
		<br/>
		<span>Art&iacute;culo: '.TildesHtml($articledata->fields['name']).'</span>
		<br/>
		<span style="font-size:small">Detalle de art&iacute;culo</span>
		</div>
		<div align="right" style="font-size:smaller">Fecha: '.date("d-m-Y").'</div></td>
	  </tr>
	</table>';
	
	$html .= '<br/><br/>
			<table width="100%" border="1" style="padding: 2px;">
				<tr>
					<td style="background-color: #999;">N&uacute;mero de art&iacute;culo:</td>
					<td>'.$articledata->fields['id'].'</td>
					<td style="background-color: #999;">C&oacute;digo de art&iacute;culo:</td>
					<td>'.TildesHtml($articledata->fields['code']).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Nombre:</td>
					<td colspan="3">'.$articledata->fields['name'].'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Descripci&oacute;n:</td>
					<td colspan="3">'.$articledata->fields['desc'].'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Unidad de medida:</td>
					<td>'.TildesHtml($ESObject->getmedidaname($articledata->fields['medida_id'])).'</td>
					<td style="background-color: #999;">Peso en KG:</td>
					<td>'.$articledata->fields['weight'].'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Precios:</td>
					<td>
						Precio 1: $'.number_format($articledata->fields['price'],2).'<br/>
						Precio 2: $'.number_format($articledata->fields['price2'],2).'<br/>
						Precio 3: $'.number_format($articledata->fields['price3'],2).'<br/>
						Precio 4: $'.number_format($articledata->fields['price4'],2).'<br/>
						Precio 5: $'.number_format($articledata->fields['price5'],2).'
					</td>
					<td style="background-color: #999;">Costo:</td>
					<td>$'.number_format($articledata->fields['costo'],2).'</td>
				</tr>
				<tr>
					<td style="background-color: #999;">Moneda:</td>
					<td>'.$moneda.'</td>
					<td style="background-color: #999;">Sustituye en lista LAO a:</td>
					<td>'.TildesHtml($articledata->fields['equivale']).'</td>
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
