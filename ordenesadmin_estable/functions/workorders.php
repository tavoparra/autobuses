<?php
session_start();

include_once( 'functions.inc.php' );

$ESObject = new Ordenes( '../../' );
if ( $_POST['redirect'] == 0 )
{
	$hora_orden = $_POST["horas"].":".$_POST["minutos"];
	$hora_orden2 = $_POST["horas_2"].":".$_POST["minutos_2"];
	$tiempo_est = $_POST["horas2"].":".$_POST["minutos2"];
	$tiempo_real = $_POST["horas3"].":".$_POST["minutos3"];
	
	
	$fecha = $_POST['date1'];
	$dia = substr($fecha, 0, 2);
	$mes   = substr($fecha, 3, 2);
	$ano = substr($fecha, -4);
	// fechal final realizada el cambio de formato a las fechas europeas
	$fecha = $ano . '-' . $mes . '-' . $dia;
	
	$fecha2 = $_POST['date2'];
	$dia = substr($fecha2, 0, 2);
	$mes   = substr($fecha2, 3, 2);
	$ano = substr($fecha2, -4);
	// fechal final realizada el cambio de formato a las fechas europeas
	$fecha2 = $ano . '-' . $mes . '-' . $dia;
	
    $ESObject->addorder( $_POST['numero'], $_POST['folio'], $_POST['factura_num'], $_POST['unidadid'], $fecha, $hora_orden, $fecha2, $hora_orden2,
						$_POST['operador'], $_POST['kilometraje'], $_POST['reporta'], $_POST['dias_estimado'], $tiempo_est, $_POST['dias_real'], $tiempo_real, $_POST['mantenimientoid'], $_POST['observaciones'],
						$_POST['indicaciones'], $_POST['trabajos'], $_POST['horas_equipo_motor'], $_POST['horas_equipo_diesel'], $_POST['horas_stand_by'], $_POST['lugar_servicio'], $_POST['forma_pago'],
						$_POST['diascredito'], $_POST['tipo_cambio'], $_POST['taller_servicio']);
	
	$ordenid = mysql_insert_id();
	$ESObject->savelog($ordenid, 6, $_SESSION['idUsuario'], 1);
	
	
	for($i = 0; $i < count($_SESSION['tecnicos']); $i++)
	{
		$ESObject->addtecnico($ordenid, $_SESSION['tecnicos'][$i]['tecnicoid']);
	}
	
	$descuento_cliente = $ESObject->getdescuento_cliente($_POST['clienteid']);
	
	for($i = 0; $i < count($_SESSION['refacciones']); $i++)
	{
		$ESObject->addordenitem($ordenid, $_SESSION['refacciones'][$i]['articuloid'], $_SESSION['refacciones'][$i]['cantidad'], $_SESSION['refacciones'][$i]['precio'], $descuento_cliente, $_SESSION['refacciones'][$i]['dollars']);
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
	
	$fecha = $_POST['date1'];
	$dia = substr($fecha, 0, 2);
	$mes   = substr($fecha, 3, 2);
	$ano = substr($fecha, -4);
	// fechal final realizada el cambio de formato a las fechas europeas
	$fecha = $ano . '-' . $mes . '-' . $dia;
	
	$fecha2 = $_POST['date2'];
	$dia = substr($fecha2, 0, 2);
	$mes   = substr($fecha2, 3, 2);
	$ano = substr($fecha2, -4);
	// fechal final realizada el cambio de formato a las fechas europeas
	$fecha2 = $ano . '-' . $mes . '-' . $dia;
	
	$ESObject->editorder( $_POST['numero'], $_POST['folio'], $_POST['factura_num'], $_POST['unidadid'], $fecha, $hora_orden, $fecha2, $hora_orden2,
						$_POST['operador'], $_POST['kilometraje'], $_POST['reporta'], $_POST['dias_estimado'], $tiempo_est, $_POST['dias_real'], $tiempo_real, $_POST['mantenimientoid'], $_POST['observaciones'], $_POST['indicaciones'], $_POST['trabajos'], $_POST['horas_equipo_motor'],
						$_POST['horas_equipo_diesel'], $_POST['horas_stand_by'], $_POST['lugar_servicio'], $_POST['forma_pago'], $_POST['diascredito'], $_POST['tipo_cambio'], $_POST['taller_servicio']);
	
	$ordenid = $_POST['numero'];
	
	$ESObject->droptecnicos( $_POST['numero'] );
	for($i = 0; $i < count($_SESSION['tecnicos']); $i++)
	{
		$ESObject->addtecnico($ordenid, $_SESSION['tecnicos'][$i]['tecnicoid']);
	}
	
	$ESObject->droporderitem( $_POST['numero'] );
	$descuento_cliente = $ESObject->getdescuento_cliente($_POST['clienteid']);
	for($i = 0; $i < count($_SESSION['refacciones']); $i++)
	{
		$ESObject->addordenitem($ordenid, $_SESSION['refacciones'][$i]['articuloid'], $_SESSION['refacciones'][$i]['cantidad'], $_SESSION['refacciones'][$i]['precio'], $descuento_cliente, $_SESSION['refacciones'][$i]['dollars']);
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