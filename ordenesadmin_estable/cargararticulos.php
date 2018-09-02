<?php
session_start();

define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new Ordenes( '../' );

$articulo_info = $ESObject->getarticuloinfo($_GET['clave']);
$tasa_impuesto = $ESObject->gettasa();;

header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

$descuento_cliente = $ESObject->getdescuento_cliente($_GET['clienteid']);

if($_GET['delete'] == 1)
{
	$position = $_GET['position'];
	unset($_SESSION['refacciones'][$position]);
	$_SESSION['refacciones'] = array_values($_SESSION['refacciones']);
}
elseif($articulo_info != 0){
	$temp_array['articuloid'] = $articulo_info['id'];
	$temp_array['cantidad'] = $_GET['cantidad'];
	$temp_array['refaccion'] = $articulo_info['name'];
	$temp_array['precio'] = $_GET['precio'];
	$temp_array['mano_obra'] = $articulo_info['mano_obra'];
	$temp_array['dollars'] = $articulo_info['dollars'];
	$temp_array['code'] = $articulo_info['code'];
	
	if($temp_array['dollars'] == true && $_GET['tipo_cambio'] < 1)
			die("<h2><center>DEBE DEFINIR PRIMERO EL TIPO DE CAMBIO</center></h2>");
		

	$_SESSION['refacciones'][] = $temp_array;
}
else
{
	echo "<center><h4>No se encontr&oacute; el art&iacute;culo</h4></center>";
}

echo '<table width="100%" border="1">
		<tr>
		<td>&nbsp;</td>
        <td><div align="center"><strong>Cant.</strong></div></td>
        <td><div align="center"><strong>Refacci&oacute;n</strong></div></td>
		<td><div align="center"><strong>C&oacute;d.</strong></div></td>
        <td><div align="center"><strong>Precio</strong></div></td>
		<td><div align="center"><strong>Desc.</strong></div></td>
		<td style="width:55px;"><div align="center"><strong>Importe</strong></div></td>
      </tr>
      <tr>';
	  
$total_ref = 0;
$total_mano = 0;	  
	for($i = 0; $i < count($_SESSION['refacciones']); $i++)
	{
			
		$importe = $_SESSION['refacciones'][$i]['precio'] * $_SESSION['refacciones'][$i]['cantidad'];
		
		if($_SESSION['refacciones'][$i]['dollars'] == true)
		{
			$importe = $importe * $_GET['tipo_cambio'];
			$precio_mostrar = $_SESSION['refacciones'][$i]['precio'] * $_GET['tipo_cambio'];
		}
		else
			$precio_mostrar = $_SESSION['refacciones'][$i]['precio'];
		
		if($descuento_cliente > 0)
			$importe = $importe - ($importe * ($descuento_cliente /100));
		
		echo '<td><strong><a href="javascript:quitar_articulo('.$i.')">X</a></strong></td>';
		echo '<td>'.$_SESSION['refacciones'][$i][cantidad].'</td>';
		echo '<td>'.$_SESSION['refacciones'][$i][refaccion].'</td>';
		echo '<td>'.$_SESSION['refacciones'][$i][code].'</td>';
		echo '<td><div align="right">$'.$precio_mostrar.'</div></td>';
		echo '<td><div align="right">'.$descuento_cliente.'%</div></td>';
		echo '<td><div align="right">$'.$importe.'</div></td>';
		echo '</tr><tr>';
		
		if($_SESSION['refacciones'][$i][mano_obra] == 0)
			$total_ref += $importe;
		else
			$total_mano += $importe;
	}
	
echo '</tr>
    </table>';
	
$subtotal = $total_ref + $total_mano;
$impuesto = $subtotal * ($tasa_impuesto / 100);
$total = $subtotal + $impuesto;
echo '<table align="right" border="1">
		<tr>
			<td>Total mano de obra:</td>
			<td style="width:55px;"><div align="right"><strong>$'.$total_mano.'</strong></div></td>
		</tr>
		<tr>
			<td>Total refacciones:</td>
			<td style="width:55px;"><div align="right"><strong>$'.$total_ref.'</strong></div></td>
		</tr>
		<tr>
			<td>Tasa de impuesto:</td>
			<td style="width:55px;"><div align="right"><strong>'.$tasa_impuesto.'%</strong></div></td>
		</tr>
		<tr>
			<td>Subtotal:</td>
			<td style="width:55px;"><div align="right"><strong>$'.$subtotal.'</strong></div></td>
		</tr>
		<tr>
			<td>Impuesto:</td>
			<td style="width:55px;"><div align="right"><strong>$'.$impuesto.'</strong></div></td>
		</tr>
		<tr>
			<td>Total:</td>
			<td style="width:55px;"><div align="right"><strong>$'.$total.'</strong></div></td>
		</tr>
	  </table>';

?>