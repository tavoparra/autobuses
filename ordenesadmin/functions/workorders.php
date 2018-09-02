<?php
session_start();

include_once( 'functions.inc.php' );

$ESObject = new Ordenes( '../../' );
if ( $_POST['redirect'] == 0 ){
	
	$hora_orden = $_POST["horas"].":".$_POST["minutos"];
	$hora_orden2 = $_POST["horas_2"].":".$_POST["minutos_2"];
	$tiempo_est = $_POST["horas2"].":".$_POST["minutos2"];
	$tiempo_real = $_POST["horas3"].":".$_POST["minutos3"];
	
	// fechal final realizada el cambio de formato a las fechas europeas
	$fecha = $ESObject->format_fecha($_POST['date1']);
	$fecha2 = $ESObject->format_fecha($_POST['date2']);
	$fecha_prox = $ESObject->format_fecha($_POST['fecha_prox']);
	
    $ESObject->addorder($_POST['numero'], $_POST['folio'], $_POST['factura_num'], $_POST['unidadid'], $fecha, $hora_orden, $fecha2, $hora_orden2, $_POST['operador'],
						$_POST['kilometraje'], $_POST['reporta'], $_POST['dias_estimado'], $tiempo_est, $_POST['dias_real'], $tiempo_real, $_POST['mantenimientoid'],
						$fecha_prox, $_POST['tipo_prox'], $_POST['observaciones'], $_POST['indicaciones'], $_POST['trabajos'], $_POST['horas_equipo_motor'],
						$_POST['horas_equipo_diesel'], $_POST['horas_stand_by'], $_POST['lugar_servicio'], $_POST['forma_pago'], $_POST['diascredito'], $_POST['tipo_cambio'],
						$_POST['taller_servicio']);
	
	$ordenid = mysql_insert_id();
	$ESObject->savelog($ordenid, 6, $_SESSION['idUsuario'], 1);
	
	
	for($i = 0; $i < count($_SESSION['tecnicos']); $i++)
	{
		$ESObject->addtecnico($ordenid, $_SESSION['tecnicos'][$i]['tecnicoid']);
	}
	
	$descuento_cliente = $_POST['discount'];
	$itemsCount = sizeof($_POST['qty']);
	for($i = 0; $i < $itemsCount; $i++){
		$ESObject->addordenitem($ordenid, $_POST['articuloid'][$i], $_POST['qty'][$i], str_replace('$','',$_POST['price'][$i]), $descuento_cliente, $_POST['dollars'][$i]);
	}
	
	
     echo '<script language="Javascript">';
     echo 'location.href="../ordenesadmin.php";</script>';
}
elseif ( $_POST['redirect'] == 1 )
{
	$hora_orden = $_POST["horas"].":".$_POST["minutos"];
	$hora_orden2 = $_POST["horas_2"].":".$_POST["minutos_2"];
	$tiempo_est = $_POST["horas2"].":".$_POST["minutos2"];
	$tiempo_real = $_POST["horas3"].":".$_POST["minutos3"];
	
	// fechal final realizada el cambio de formato a las fechas europeas
	$fecha = $ESObject->format_fecha($_POST['date1']);
	$fecha2 = $ESObject->format_fecha($_POST['date2']);
	$fecha_prox = $ESObject->format_fecha($_POST['fecha_prox']);
	
	$ESObject->editorder( $_POST['numero'], $_POST['folio'], $_POST['factura_num'], $_POST['unidadid'], $fecha, $hora_orden, $fecha2, $hora_orden2, $_POST['operador'],
						  $_POST['kilometraje'], $_POST['reporta'], $_POST['dias_estimado'], $tiempo_est, $_POST['dias_real'], $tiempo_real, $_POST['mantenimientoid'],
						  $fecha_prox, $_POST['tipo_prox'], $_POST['observaciones'], $_POST['indicaciones'], $_POST['trabajos'], $_POST['horas_equipo_motor'],
						  $_POST['horas_equipo_diesel'], $_POST['horas_stand_by'], $_POST['lugar_servicio'], $_POST['forma_pago'], $_POST['diascredito'],
						  $_POST['tipo_cambio'],$_POST['taller_servicio']);
	
	$ordenid = $_POST['numero'];
	
	$ESObject->droptecnicos( $_POST['numero'] );
	for($i = 0; $i < count($_SESSION['tecnicos']); $i++){
		$ESObject->addtecnico($ordenid, $_SESSION['tecnicos'][$i]['tecnicoid']);
	}
	
	$ESObject->droporderitem( $_POST['numero'] );
	$descuento_cliente = $_POST['discount'];
	$itemsCount = sizeof($_POST['qty']);
	for($i = 0; $i < $itemsCount; $i++){
		$ESObject->addordenitem($ordenid, $_POST['articuloid'][$i], $_POST['qty'][$i], str_replace('$','',$_POST['price'][$i]), $descuento_cliente, $_POST['dollars'][$i]);
	}
	
	$ESObject->savelog($ordenid, 6, $_SESSION['idUsuario'], 2);
	
     echo '<script language="Javascript">';
     echo 'location.href="../ordenesadmin.php";</script>';
	
    
}
elseif ( $_POST['redirect'] == 2 )
{
	$ESObject->droptecnicos( $_POST['numero'] );
	$ESObject->droporderitem( $_POST['numero'] );
    $ESObject->droporder( $_POST['numero'] );
	$ESObject->savelog($_POST['numero']);
	
	echo '<script language="Javascript">';
	echo 'location.href="../ordenesadmin.php";</script>';
}

?>