<?php
define( 'IN_EMADMIN', true );
include_once('functions/functions.inc.php');
include_once('functions/jsonwrapper.php');

$ESObject = new Ordenes( '../' );

$articulo_info = $ESObject->getarticuloinfo($_GET['clave']);

if($articulo_info === 0){
	unset($articulo_info);
	$articulo_info['error'] = 'No se encontr&oacute; el art&iacute;culo';
	$articulo_info['error_field'] = '#articulo_name';
}
else{
	if($articulo_info['dollars'] == true && $_GET['tipo_cambio'] == ''){
		$articulo_info['error'] = 'Debe definir primero el tipo de cambio';
		$articulo_info['error_field'] = '#tipo_cambio';
	}

	//si el numero es decimal y la medida no acepta decimales lanzamos error
	if($articulo_info['decimal'] != 1 && preg_match('/^\d+\.\d+$/',$_GET['cantidad'])){
		//$articulo_info['error'] = 'No puede definir '.$_GET['cantidad'].' '.$articulo_info['medida'].' de '.strtolower($articulo_info['name']);
		$articulo_info['error'] = 'Este art&iacute;culo no acepta decimales en la cantidad';
		$articulo_info['error_field'] = '#cantidad_art';
	}
}
/*$jsonString = '{';
foreach($articulo_info as $k => $v){
	$jsonString .= '"'.$k.'":"'.$v.'"';
	if (next($articulo_info)==true) $jsonString .= ",";
}
$jsonString .= '}';	

echo $jsonString;*/
echo json_encode($articulo_info);

?>
