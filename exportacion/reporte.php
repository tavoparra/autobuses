<?php
ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');
require_once('../tcpdf/config/lang/spa.php');
require_once('../tcpdf/tcpdf.php');
date_default_timezone_set("America/Mexico_City");

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Octavio Parra');
$pdf->SetTitle('Relacion de Servicios por Cliente y Numero Economico');

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

// add a page
$pdf->AddPage();



include_once('../config/varload.inc.php');
include_once('functions/functions.inc.php');

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new exportacion( '../' );

$fecha1 = $_POST['date1'];
$dia = substr($fecha1, 0, 2);
$mes   = substr($fecha1, 3, 2);
$ano = substr($fecha1, -4);
// fechal final realizada el cambio de formato a las fechas europeas
$fecha1 = $ano . '-' . $mes . '-' . $dia;
$fecha2 = $_POST['date2'];
$dia = substr($fecha2, 0, 2);
$mes   = substr($fecha2, 3, 2);
$ano = substr($fecha2, -4);
// fechal final realizada el cambio de formato a las fechas europeas
$fecha2 = $ano . '-' . $mes . '-' . $dia;
$costeado = $_POST['costeado'];
$clienteid = $_POST['clienteid1'];
$tallerid = $_POST['tallerid1'];
$unidad_inicial = $_POST['unidad_inicial'];
$unidad_final = $_POST['unidad_final'];
$criteria = $_POST['criteria'];
$taller_servicio = $_POST['tallerid4'];
$filtro_mantenimiento = $_POST['filtro_mantenimiento'];
$tipo_mantenimiento = implode(",",$_POST['tipo_mantenimiento']);

if($_POST['date1'] != '')
	$filtro = ' o.fecha_orden >= "'.$fecha1.'"';
else
	$filtro = ' 1';

if($_POST['date2'] != '')
	$filtro .= ' AND o.fecha_orden <= "'.$fecha2.' 23:59:59"';

if($clienteid > 0)
	$filtro .= ' and c.clienteid = '.$clienteid;
if($tallerid > 0)
	$filtro .= ' and o.taller_servicio = '.$tallerid;
if($taller_servicio > 0)
	$filtro .= ' and o.taller_servicio IN ('.$taller_servicio.')';
if($unidad_inicial != '')
	$filtro .= ' and u.'.$criteria.' >= "'.$unidad_inicial.'"';
if($unidad_final != '')
	$filtro .= ' and u.'.$criteria.' <= "'.$unidad_final.'"';
if($filtro_mantenimiento > 0)
	$filtro .= ' AND o.tipo_mantenimiento IN ('.$tipo_mantenimiento.")";


$resultados_row = $ESObject->getordenesinfo($filtro);
$osito = !$_POST['externo'] ? '<img src="../imagenes/osito.jpg" width="47" height="84" />' : '';
$nombre_empresa = !$_POST['externo'] ? 'REFRISERVICIO Y AIRE ACONDICIONADO PARA EL TRANSPORTE , S.A. DE C.V.' : $_POST['nombre_externo'];

// create some HTML content
	$html = '<table border="0" width="90%">
	  <tr>
		<td width="10%">'.$osito.'</td>
		<td width="90%"><div align="center">'.$nombre_empresa.'
		<br/>
		<span style="font-size:small">Relación de servicios por Cliente y Número Económico</span>
		<br/>
		<span style="font-size:smaller">';
		
		if($_POST['date1'] != '' || $_POST['date1'] != '')
			$html .= 'Período del Reporte: '.$fecha1.' al '.$fecha2;
		else 
			$html .= '&nbsp;';
			
		$html .= '</span>
		</div>
		<div align="right" style="font-size:smaller">Fecha: '.date("d-m-Y").'</div></td>
	  </tr>
	</table>';

if ( !$resultados_row->EOF )
{
	$cliente_actual = "";
	$unidad_actual = "";
	$total_unidad = 0;
	$total_cliente = 0;
	$total_reporte = 0;
	$servicios_cliente = 0;
	$servicios_reporte = 0;
	
    do{
        $ordenid 	= $resultados_row->fields["ordenid"];
		$cliente_cod    = htmlspecialchars($resultados_row->fields["cliente_cod"]);
        $cliente_nombre  = htmlspecialchars($resultados_row->fields["nombre"]);
        $num_economico  = htmlspecialchars($resultados_row->fields["numEconomico"]);
        $tipo   = htmlspecialchars($resultados_row->fields["tipo"]);
		$marca   = htmlspecialchars($resultados_row->fields["marca"]);
		$modelo   = htmlspecialchars($resultados_row->fields["modelo"]);
		$marcaEquipo   = htmlspecialchars($resultados_row->fields["marcaEquipo"]);
		$modeloEquipo   = htmlspecialchars($resultados_row->fields["modeloEquipo"]);
		$serieEquipo   = htmlspecialchars($resultados_row->fields["serieEquipo"]);
		$placas   = htmlspecialchars($resultados_row->fields["placas"]);
		$folio   = htmlspecialchars($resultados_row->fields["folio"]);
		$fecha_orden   = $resultados_row->fields["fecha_orden"];
		$factura_num   = $resultados_row->fields["factura_num"];
		$trabajos   = htmlspecialchars($resultados_row->fields["trabajos"]);
		$tecnicos   = htmlspecialchars($resultados_row->fields["tecnicos"]);
		$tallerservicio   = htmlspecialchars($resultados_row->fields["tallerservicio"]);
		$horas_totales   = $resultados_row->fields["horas_totales"];
		$horas_equipo_diesel   = $resultados_row->fields["horas_equipo_diesel"];
		$horas_stand_by   = $resultados_row->fields["horas_stand_by"];
		
		
		if($cliente_actual != $cliente_cod)
		{
			$html .= '<h4> Cliente: '.$cliente_cod.' - '.$cliente_nombre.'</h4>';
					
			$cliente_actual = $cliente_cod;
			$total_cliente = 0;
			$servicios_cliente = 0;
		}
		

		if($unidad_actual != $num_economico)
		{
			$html .= '<table nobr="true" width="100%" border="0" style="padding:5px 5px 5px 5px; font-size:smaller; border-style: solid; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px">
					  <tr>
						<td>N. económico: '.$num_economico.'</td>
						<td>Tipo: '.$tipo.'</td>
						<td>Marca: '.$marca.'</td>
						<td>Modelo: '.$modelo.'</td>
						<td>Placas: '.$placas.'</td>
					  </tr>
					  <tr>
						<td></td>
						<td></td>
						<td>Marca Equipo: '.$marcaEquipo.'</td>
						<td>Modelo Equipo: '.$modeloEquipo.'</td>
						<td>Serie Equipo: '.$serieEquipo.'</td>
					  </tr>
					</table><br/>
					<table width="100%" border="0" style="padding:3px 3px 3px 3px; font-size:smaller;">
					  <tr>
						<td width = "11%">Orden</td>
						<td width = "10%">Fecha</td>
						<td width = "10%">Factura</td>
						<td width = "59%">Descripción del servicio</td>';
			if($costeado)		
					$html .= '<td width = "10%">Importe</td>';
			$html .=  '</tr>';
			$unidad_actual = $num_economico;
			$total_unidad = 0;
		}
		
		$refacciones = $ESObject->getrefacciones($ordenid);
		$ordentotal = $ESObject->getordentotal($ordenid);
		$total_cliente += $ordentotal;
		$total_unidad += $ordentotal;
		$total_reporte += $ordentotal;
			
		$fecha_orden = date("d-m-Y",strtotime($fecha_orden));

		$html .= '<tr>
					<td>'.$folio.'</td>
					<td>'.$fecha_orden.'</td>
					<td>'.$factura_num.'</td>
					<td>
						LUGAR DE SERVICIO: '.$tallerservicio.'<br/>
						REPORTO: '.$trabajos.'<br/>
						TÉCNICO(S): '.$tecnicos.'<br/>&nbsp;<br/>
						
						REFACCIONES: '.$refacciones."<br/>";
						
					
					if($horas_totales != 0)
						$html .= "<br/>Horas totales: ".$horas_totales;
					
					if($horas_equipo_diesel != 0)
						$html .= "<br/>Horas de equipo diesel: ".$horas_equipo_diesel;
					
					if($horas_stand_by != 0)
						$html .= "<br/>Horas Stand By: ".$horas_stand_by;

		$html .=  '</td>';			
						
						
						
			if($costeado)		
					$html .= '<td><div align="right">$'.number_format($ordentotal,2).'</div></td>';
			$html .=  '</tr>';
			
			
		$servicios_cliente++;
		$servicios_reporte++;
        $resultados_row->MoveNext();

			if($unidad_actual != $resultados_row->fields["numEconomico"] || $resultados_row->EOF)
			{
				if($costeado)
				{
					$html .= '<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							  </tr>
							  <tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td><div align="right">Subtotal del número económico: '.$num_economico.'</div></td>
								<td><div align="right">$'.number_format($total_unidad,2).'</div></td>
							  </tr>';
				}
						  
				$html .= " </table><br/>&nbsp;<br/>&nbsp;<br/>";
				$pdf->writeHTML($html, false, false, false, false, '');
				unset($html);
			}
			
			if($cliente_actual != $resultados_row->fields["cliente_cod"] || $resultados_row->EOF)
			{
				$html .= '<table border = "1" style="padding: 1px; background-color:#999;" width="100%"><tr><td>
			<table width="100%" border="0" style="padding:5px 5px 5px 5px; border-style: solid; border-width: 1px; background-color:#999">
				  <tr nobr="true">
					<td width="73%">Total de Servicios del cliente:<br/>'.$cliente_cod.' - '.$cliente_nombre.'</td>
					<td width="10%"><br/>'.$servicios_cliente.'</td>';
				if($costeado)	
					$html .= '<td width="17%"><br/>$'.number_format($total_cliente,2).'</td>';
				 
				 $html .= ' </tr></table>
				 </td></tr></table>';
				
			}
    }
    while ( !$resultados_row->EOF );
	
	$html .= '<br/>&nbsp;<br/><div align="center"><table width="40%" border="1" style="padding:5px 5px 5px 5px; background-color:#999">
				  <tr nobr="true">
					<td>Total de Servicios del reporte: '.$servicios_reporte.'</td>
				  </tr>
				</table></div>';
	
	if($costeado)
	{
		$html .= '<br/>&nbsp;<br/><div style="text-align:center;">
				<table border = "1" style="padding: 1px; background-color:#999;" width="100%"><tr><td>
				<table width="100%" border="0" style="padding:5px 5px 5px 5px; background-color:#999; border-style: solid; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px;">
				  <tr nobr="true">
					<td>GRAN TOTAL DEL REPORTE: </td>
					<td><div align="right">$'.number_format($total_reporte, 2).'</div></td>
				  </tr>
				</table>
				</td></tr></table></div>';	
	}
}
else
{
    //Si no encuentra ningun registro
    $html .= '<p align="center">No se encontraron ordenes con los parametros seleccionados</p>';
}
	
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
