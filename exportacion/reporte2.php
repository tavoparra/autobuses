<?php
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '240');
date_default_timezone_set("America/Mexico_City");

require_once('../tcpdf/config/lang/spa.php');
require_once('../tcpdf/tcpdf.php');
include_once('../config/varload.inc.php');
include_once('functions/functions.inc.php');

//Si vamos a usar la base de datos inicializamos el objeto de conexion
$ESObject = new exportacion( '../' );

$fecha1 = $_POST['start_date_report2'];
$dia = substr($fecha1, 0, 2);
$mes   = substr($fecha1, 3, 2);
$ano = substr($fecha1, -4);
// fechal final realizada el cambio de formato a las fechas europeas
$fecha1 = $ano . '-' . $mes . '-' . $dia;
$fecha2 = $_POST['end_date_report2'];
$dia2 = substr($fecha2, 0, 2);
$mes2   = substr($fecha2, 3, 2);
$ano2 = substr($fecha2, -4);
// fechal final realizada el cambio de formato a las fechas europeas
$fecha2 = $ano2 . '-' . $mes2 . '-' . $dia2;
$clienteid = $_POST['clienteid2'];
$unidadid = $_POST['unidadid2'];
$tallerid = $_POST['tallerid2'];
$tipo_mantenimiento = implode(",",$_POST['tipo_mantenimiento2']);
$separarEquipos = (isset($_POST['separarEquipos']) && $_POST['separarEquipos'] == true) ? true : false; 

if(count($_POST['tipo_mantenimiento']) == 1)
		$mantenimiento_text = '('.$ESObject->mantenimiento_name($tipo_mantenimiento).') - ';
		
class MYPDF extends TCPDF {
	// Page footer
	public function Footer() {
		global $mantenimiento_text;

		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->Cell(0, 10, 'Acumulado de mantenimientos '.$mantenimiento_text.'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Octavio Parra');
$pdf->SetTitle('Reporte de acumulado de mantenimientos');
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT - 10, 5, PDF_MARGIN_RIGHT);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->SetFont('dejavusans', '', 10);
$pdf->AddPage();


$filtro = ' o.fecha_orden between "'.$fecha1.'" AND "'.$fecha2.'"';
if($_POST['filtro_mantenimiento2'] > 0)
	$filtro .= ' AND o.tipo_mantenimiento IN ('.$tipo_mantenimiento.")";
if($clienteid > 0)
	$filtro .= ' AND c.clienteid = '.$clienteid;
if($tallerid > 0)
	$filtro .= ' AND o.taller_servicio = '.$tallerid;
if($unidadid > 0)
	$filtro .= ' AND o.unidadid = '.$unidadid;

$totales = $ESObject->total_mantenimientos($filtro);
$mantenimientos_info = $ESObject->mantenimientos_info($filtro, $separarEquipos);
$meses = array( "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" );

$osito = !$_POST['externo'] ? '<img src="../imagenes/osito.jpg" width="65" height="92" />' : $ESObject->getExternalLogo();
$nombre_empresa = !$_POST['externo'] ? 'REFRISERVICIO Y AIRE ACONDICIONADO PARA TRANSPORTE , S.A. DE C.V.' : $_POST['nombre_externo'];

// create some HTML content
	$html = '<div align="center" style="text-align: center;"><br/><br/><br/><br/><br/><br/>
  <p>'.$osito.'</p><br/><br/>
  <h1>'.$nombre_empresa.'<br/><br/><br/>
  <p>REPORTE DE ACUMULADO DE MANTENIMIENTOS';
	if(count($_POST['tipo_mantenimiento2']) == 1 && $_POST['filtro_mantenimiento2'] > 0)
		$html .= '<br/>('.$ESObject->mantenimiento_name($tipo_mantenimiento).')';
  
  $html .='</p></h1>
  <br/><br/><br/>
  <p><h2>Del '; 
	$html .= (int)$dia." de ".$meses[(int)$mes - 1]." de ".$ano;
	$html .= " al ".(int)$dia2." de ".$meses[(int)$mes2 - 1]." de ".$ano2;
  
  
  $html .= '</h2></p>
  <div>Fecha: '.date("d-m-Y").'</div>
  <p>
  <h2 style="font-weight:bolder">
      	  '.$mantenimientos_info->fields['cliente'].'<br/>';
		  
		  if($tallerid > 0) $html .= $mantenimientos_info->fields['taller'];
		  if($unidadid > 0) $html .= "Unidad: ".$mantenimientos_info->fields['numEconomico'];
			
		  
	$html .= '
	  </h2>
	  <img src="../imagenes/carrier_transicold.jpg"/>
  </p>
  <br/><br/>
  </div>
	<table>
		<tr>
			<td style="width:20%"></td>
			<td style="width:60%"><table><tr><td>
			 <table width="100%" border="1" align="center">
				<tr>
				  <td>TOTAL DE UNIDADES:</td>
				  <td>'.$totales->fields['unidades'].'</td>
				</tr>
				<tr>
				  <td>TOTAL DE SERVICIOS:</td>
				  <td>'.$totales->fields['servicios'].'</td>
				</tr>
				<tr>
				  <td>COSTO TOTAL:</td>
				  <td>$'.number_format($totales->fields['costo'],2).'</td>
				</tr>
				<tr>
				  <td>PROMEDIO COSTO/UNIDAD:</td>
				  <td>$'.number_format($totales->fields['costo']/$totales->fields['unidades'],2).'</td>
				</tr>
			  </table>
			</td></tr></table></td>
			<td style="width:25%"></td>
		</tr>
	</table>
	<br pagebreak="true"/>
 ';

if ( !$mantenimientos_info->EOF ) {
	$equipoActual = $mantenimientos_info->fields["modeloEquipo"];
	$total_ene = $total_feb = $total_mar = $total_abr = $total_may = $total_jun = $total_jul = $total_ago = $total_sep = $total_oct = $total_nov = $total_dic = $total_serv = 0;
	$html .= '<table border="0" width="90%">
					  <tr>
						<td width="10%">'.$osito.'</td>
						<td width="90%"><div align="center">'.$nombre_empresa.'
						<br/>
						<span style="font-size:small">Acumulado de mantenimientos ';
	if(count($_POST['tipo_mantenimiento2']) == 1 && $_POST['filtro_mantenimiento2'] > 0)
		$html .= '('.$ESObject->mantenimiento_name($tipo_mantenimiento).') ';
  
  $html .='realizados a '.$mantenimientos_info->fields['cliente'].'</span>
						<br/>
						<span style="font-size:small">';
			
			if($tallerid > 0)
				$html .= "Taller: ".$mantenimientos_info->fields['taller'];

			if($unidadid > 0)
				$html .= "<br/>Unidad: ".$mantenimientos_info->fields['numEconomico'];
				
			$html .= '</span>
						</div>
						<div align="right" style="font-size:smaller;">'.$meses[$mes - 1]." ".$ano." a ".$meses[$mes2 - 1].' '.$ano2.'</div></td>
					  </tr>
					</table>';

			$html .= '<table width="100%" border="1" style="text-align:center; padding: 0px;">';
			
			$html .= ($separarEquipos) ? '<tr><td colspan="16"><h2 align="center">'.$equipoActual.'</h2></td></tr>' : '';

			$html .=	  '<tr nobr="true">
							<td width="6%"></td>
							<td width="11%"><strong>ECO.</strong></td>
							<td><strong>ENE</strong></td>
							<td><strong>FEB</strong></td>
							<td><strong>MAR</strong></td>
							<td><strong>ABR</strong></td>
							<td><strong>MAY</strong></td>
							<td><strong>JUN</strong></td>
							<td><strong>JUL</strong></td>
							<td><strong>AGO</strong></td>
							<td><strong>SEP</strong></td>
							<td><strong>OCT</strong></td>
							<td><strong>NOV</strong></td>
							<td><strong>DIC</strong></td>
							<td width=".5%"></td>
							<td><strong>SERV</strong></td>
						  </tr>
						  <tr>
							<td colspan="16" style="font-size:xx-small;"></td>
						  </tr>';

			$contador = 1;
			$anhio_actual = "0";
			$rowcount = ($separarEquipos) ? 10 : 7;
	
	
    do{
		$anhio 	= $mantenimientos_info->fields["anhio"];
        if(($anhio != $anhio_actual) && $ano !== $ano2){
			$anhio_actual = $anhio;
        	$html .= '<tr>
					  <td colspan="16"><h2 align="center"><strong>'.$anhio.'</strong></h2></td>
					  </tr>';
        	$rowcount += 1.5;
        }
        $tallerid 	= $mantenimientos_info->fields["tallerid"];
        if($equipoActual != $mantenimientos_info->fields["modeloEquipo"] && $separarEquipos == true){
        	$equipoActual = $mantenimientos_info->fields["modeloEquipo"];
        	$html .= '<tr>
					  <td colspan="16"><h2 align="center">'.$equipoActual.'</h2></td>
					  </tr>';
        	$rowcount += 1.5;
        }

			$html .= '<tr>
						<td>'.$contador.'</td>
						<td>'.$mantenimientos_info->fields["numEconomico"].'</td>
						<td>'; $html .= ($mantenimientos_info->fields["ene"] > 0) ? $mantenimientos_info->fields["ene"]:"&nbsp;"; $html .= '</td>
						<td>'; $html .= ($mantenimientos_info->fields["feb"] > 0) ? $mantenimientos_info->fields["feb"]:"&nbsp;"; $html .= '</td>
						<td>'; $html .= ($mantenimientos_info->fields["mar"] > 0) ? $mantenimientos_info->fields["mar"]:"&nbsp;"; $html .= '</td>
						<td>'; $html .= ($mantenimientos_info->fields["abr"] > 0) ? $mantenimientos_info->fields["abr"]:"&nbsp;"; $html .= '</td>
						<td>'; $html .= ($mantenimientos_info->fields["may"] > 0) ? $mantenimientos_info->fields["may"]:"&nbsp;"; $html .= '</td>
						<td>'; $html .= ($mantenimientos_info->fields["jun"] > 0) ? $mantenimientos_info->fields["jun"]:"&nbsp;"; $html .= '</td>
						<td>'; $html .= ($mantenimientos_info->fields["jul"] > 0) ? $mantenimientos_info->fields["jul"]:"&nbsp;"; $html .= '</td>
						<td>'; $html .= ($mantenimientos_info->fields["ago"] > 0) ? $mantenimientos_info->fields["ago"]:"&nbsp;"; $html .= '</td>
						<td>'; $html .= ($mantenimientos_info->fields["sep"] > 0) ? $mantenimientos_info->fields["sep"]:"&nbsp;"; $html .= '</td>
						<td>'; $html .= ($mantenimientos_info->fields["oc"] > 0) ? $mantenimientos_info->fields["oc"]:"&nbsp;"; $html .= '</td>
						<td>'; $html .= ($mantenimientos_info->fields["nov"] > 0) ? $mantenimientos_info->fields["nov"]:"&nbsp;"; $html .= '</td>
						<td>'; $html .= ($mantenimientos_info->fields["dic"] > 0) ? $mantenimientos_info->fields["dic"]:"&nbsp;"; $html .= '</td>
						<td></td>
						<td>'.$mantenimientos_info->fields["total"].'</td>
					  </tr>';
			
			$total_ene += $mantenimientos_info->fields["ene"]; $total_feb += $mantenimientos_info->fields["feb"]; $total_mar += $mantenimientos_info->fields["mar"];
			$total_abr += $mantenimientos_info->fields["abr"]; $total_may += $mantenimientos_info->fields["may"]; $total_jun += $mantenimientos_info->fields["jun"];
			$total_jul += $mantenimientos_info->fields["jul"]; $total_ago += $mantenimientos_info->fields["ago"]; $total_sep += $mantenimientos_info->fields["sep"];
			$total_oct += $mantenimientos_info->fields["oc"];  $total_nov += $mantenimientos_info->fields["nov"]; $total_dic += $mantenimientos_info->fields["dic"];
			$total_serv += $mantenimientos_info->fields["total"];

			$rowcount++;
			
			if($rowcount >= 58 && !$mantenimientos_info->EOF)
			{
				$html .= '</table>
				<br pagebreak="true"/>
				<table width="100%" border="1" style="text-align:center; padding: 0px;">
						  <tr nobr="true">
							<td width="6%"></td>
							<td width="11%"><strong>ECO.</strong></td>
							<td><strong>ENE</strong></td>
							<td><strong>FEB</strong></td>
							<td><strong>MAR</strong></td>
							<td><strong>ABR</strong></td>
							<td><strong>MAY</strong></td>
							<td><strong>JUN</strong></td>
							<td><strong>JUL</strong></td>
							<td><strong>AGO</strong></td>
							<td><strong>SEP</strong></td>
							<td><strong>OCT</strong></td>
							<td><strong>NOV</strong></td>
							<td><strong>DIC</strong></td>
							<td width=".5%"></td>
							<td><strong>SERV</strong></td>
						  </tr>
						  <tr>
							<td colspan="16" style="font-size:xx-small;"></td>
						  </tr>';
				$rowcount = 0;
			}
			

			$mantenimientos_info->MoveNext();
			
			if($mantenimientos_info->EOF)
			{
				$html .= '<tr>
							<td colspan="16" style="font-size:xx-small;"></td>
						  </tr>
					  <tr>
						<td colspan="2">Total:</td>
						<td>'.$total_ene.'</td>
						<td>'.$total_feb.'</td>
						<td>'.$total_mar.'</td>
						<td>'.$total_abr.'</td>
						<td>'.$total_may.'</td>
						<td>'.$total_jun.'</td>
						<td>'.$total_jul.'</td>
						<td>'.$total_ago.'</td>
						<td>'.$total_sep.'</td>
						<td>'.$total_oct.'</td>
						<td>'.$total_nov.'</td>
						<td>'.$total_dic.'</td>
						<td></td>
						<td>'.$total_serv.'</td>
					  </tr>
					  </table>
					  <br/><br/>';
				
				$totalMantenimientos = $ESObject->totalesMantenimientos($filtro);
				$html .= '<div align="center" nobr="true"><strong>
							Total unidades: '.$contador.'<br/>';
				while(!$totalMantenimientos->EOF){
					$html .= $totalMantenimientos->fields['mantenimiento'].': '.$totalMantenimientos->fields['conteo']."<br/>";
					$totalMantenimientos->MoveNext();
				};
				
				$html .= '<br/>
					  Total de mantenimientos realizados: '.$total_serv.'
					  </strong></div>';
					  
					if(!$mantenimientos_info->EOF) $html .= '<br pagebreak="true"/>';
			}
			$contador++;
			
    }
    while ( !$mantenimientos_info->EOF );
	
	$html .= '';
	
}
else
{
    //Si no encuentra ningun registro
    $html .= '<p align="center">No se encontraron resultados con los parametros seleccionados</p>';
}
//die($html);
	
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
