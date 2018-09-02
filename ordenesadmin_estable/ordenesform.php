<?php
session_start();
unset($_SESSION['refacciones']);
unset($_SESSION['tecnicos']);
date_default_timezone_set("America/Mexico_City");

//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui
define( 'IN_EMADMIN', true );

unset($_SESSION['refacciones']);

$mode = isset( $_GET['mode'] ) ? substr($_GET['mode'], 0, 5) : "";
$ordenid = isset($_GET['ordenid']) ? intval($_GET['ordenid']) : "";

$dir = "../";

//Agregamos las librerias comunes

header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new Ordenes( '../' );

//**         CABECERA Y MENU IZQ            **//
$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => "Panel de Control - Ordenes de reparaci&oacute;n", "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "ordenesformbody" );

//Inicializamos las variables

$vigencia = "0000-00-00";
$Newstemplate = new template( );

if(!isset($_SESSION['permisos']['6']['2']) && $_SESSION["level"]==2)
	$link_update = '&nbsp;';
else
	$link_update = '<a href="javascript:validate(1);">Actualizar</a>';

	
if(!isset($_SESSION['permisos']['6']['3']) && $_SESSION["level"]==2)
	$link_delete = '&nbsp;';
else
	$link_delete = '<a href="javascript:MsgOkCanceldrop(\'¿Está Seguro de que Desea Eliminar Esta Orden?\', 2 );">Eliminar </a>';


if ( $mode == "edit" )
    $Newstemplate->addTemplate( "ordenesformedit" );
else
    $Newstemplate->addTemplate( "ordenesformadd" );

if($mode == 'edit' ){
    //Si esta en modo de edición, jala los datos de la BD para llenar automaticamente el formulario
    $ordendata   	= $ESObject->getordendata( $ordenid );
    $ordenid     	= $ordendata->fields["ordenid"];
    $folio      	= $ordendata->fields["folio"];
	$factura_num    = $ordendata->fields["factura_num"];
    $clienteid     	= $ordendata->fields["cliente"];
	$clientename	= $ESObject->getclientenombre($clienteid);
	$tallerid     	= $ordendata->fields["taller"];
	$tallername		= $ESObject->gettallernombre($tallerid);
	$unidadid     	= $ordendata->fields["unidadid"];
	$unidadname		= $ESObject->getunidadnombre($unidadid);	
	$fechaorden		= date("d-m-Y",strtotime($ordendata->fields["fecha_orden"]));
	$orderhours		= $ESObject->gethoras(substr($ordendata->fields["fecha_orden"],11,2));
	$ordermins		= $ESObject->getminutos(substr($ordendata->fields["fecha_orden"],14,2));
	$fechaorden2		= date("d-m-Y",strtotime($ordendata->fields["fecha_orden2"]));
	$orderhours2		= $ESObject->gethoras(substr($ordendata->fields["fecha_orden2"],11,2));
	$ordermins2		= $ESObject->getminutos(substr($ordendata->fields["fecha_orden2"],14,2));
	$operador     	= $ordendata->fields["operador"];
	$kilometraje   	= $ordendata->fields["kilometraje"];
	$reporta     	= $ordendata->fields["reporta"];
	$dias_estimado 	= $ordendata->fields["dias_estimado"];
	$tiempoest    	= $ordendata->fields["tiempo_estimado"];
	$esthours		= $ESObject->gethoras(substr($tiempoest,0,2));
	$estmins		= $ESObject->getminutos(substr($tiempoest,3,2));
	$dias_real	 	= $ordendata->fields["dias_real"];
	$tiemporeal    	= $ordendata->fields["tiempo_real"];
	$realhours		= $ESObject->gethoras(substr($tiemporeal,0,2));
	$realmins		= $ESObject->getminutos(substr($tiemporeal,3,2));
	$mantenimientosbox  = $ESObject->getmantenimientos($ordendata->fields["tipo_mantenimiento"]);
	$articulos		= $ESObject->getarticulos();
    $observaciones 	= $ordendata->fields["observaciones"];
    $indicaciones  	= $ordendata->fields["indicaciones"];
	$trabajos	  	= $ordendata->fields["trabajos"];
	$horas_equipo_motor 	= $ordendata->fields["horas_equipo_motor"];
	$horas_equipo_diesel 	= $ordendata->fields["horas_equipo_diesel"];
	$horas_stand_by 	= $ordendata->fields["horas_stand_by"];
    $lugar_servicio = $ordendata->fields["lugar_servicio"];
	$tipo_cambio	= $ordendata->fields["tipo_cambio"];
	$pagosbox  = $ESObject->getpagos($ordendata->fields["forma_pago"]);
	$dias = $ESObject->getdias($ordendata->fields["diascredito"]);
	$taller_servicio = $ordendata->fields["taller_servicio"];
	
	if($ordendata->fields["forma_pago"] == 3)
		$display = "inline";
	else
		$display = "none";
    
	$droptalleres = $ESObject->getdroptalleres(0, $taller_servicio);
	
	$tecnicosbox  = $ESObject->gettecnicos();
	$tecnicoscode = $ESObject->getordentecnicos($ordenid);
	$articuloscode = $ESObject->getordenarticulos($ordenid, $tipo_cambio);
	
	$historial = $ESObject->get_historial(6, $ordenid);
	
    $Newstemplate->asigna_variables( array( "ORDENID" 			=> $ordenid, 
	     									"FOLIO" 			=> $folio, 
											"FACTURA_NUM" 		=> $factura_num,
											"CLIENTENOMBRE"		=> $clientename,
											"CLIENTEID"			=> $clienteid, 
											"TALLERNOMBRE"		=> $tallername,
											"TALLERID"			=> $tallerid, 
											"UNIDADID"			=> $unidadid,
											"UNIDADNOMBRE"		=> $unidadname, 
											"FECHAORDEN"		=> $fechaorden,
											"HORASORDEN"		=> $orderhours,
											"MINUTOSORDEN" 		=> $ordermins,
											"FECHAORDEN2"		=> $fechaorden2,
											"HORASORDEN2"		=> $orderhours2,
											"MINUTOSORDEN2" 	=> $ordermins2,
											"OPERADOR" 			=> $operador,
											"KILOMETRAJE"		=> $kilometraje,
											"REPORTA" 			=> $reporta,
											"DIAS_ESTIMADO"		=> $dias_estimado,
											"HORASEST" 			=> $esthours, 
											"MINUTOSEST"		=> $estmins,
											"DIAS_REAL"			=> $dias_real,
											"HORASREAL"			=> $realhours,
											"MINUTOSREAL" 		=> $realmins,
											"TECNICOS"			=> $tecnicosbox, 											
											"MANTENIMIENTOS"	=> $mantenimientosbox,
											"ARTICULOS" 		=> $articulos,
											"OBSERVACIONES"	 	=> $observaciones,
											"INDICACIONES"	 	=> $indicaciones,
											"TRABAJOS"		 	=> $trabajos,
											"TECNICOSCODE"		=> $tecnicoscode,
											"ARTICULOSCODE"		=> $articuloscode,
											"HORAS_EQUIPO_MOTOR"=> $horas_equipo_motor,
											"HORAS_EQUIPO_DIESEL"	=> $horas_equipo_diesel,
											"HORAS_STAND_BY"	=> $horas_stand_by,
											"LUGAR_SERVICIO" 	=> $lugar_servicio,
											"FORMAS_PAGO"		=> $pagosbox,
											"CUENTA"			=> $cuenta,
											"TIPO_CAMBIO"		=> $tipo_cambio,
											"DIAS"				=> $dias,
											"DISPLAY"			=> $display,
											"LINK_UPDATE" 		=> $link_update,
											"LINK_DELETE" 		=> $link_delete,
											"HISTORIAL"			=> $historial,
											"DROPTALLERES" => $droptalleres
											) );
    
}
else{
	$newid = $ESObject->newid();
    $formaction = 'config/worknews.php?mode=addNews';
    $newsdate = 'Automática';
	
	$clientesbox  = $ESObject->getclientes();
	$tecnicosbox  = $ESObject->gettecnicos();
	$mantenimientosbox  = $ESObject->getmantenimientos();
	$articulos  = $ESObject->getarticulos();
	$hours = $ESObject->gethoras();
	$minutes = $ESObject->getminutos();
	$pagosbox  = $ESObject->getpagos();
	$dias = $ESObject->getdias();
	$droptalleres = $ESObject->getdroptalleres();
	
    $Newstemplate->asigna_variables( array( "FORM_ACTION" => $formaction, "MODE" => $mode, "CLIENTESBOX" => $clientesbox, "TALLERESBOX" => $talleresbox, "NEWID" => $newid,
											"MINUTOS" => $minutes, "HORAS" => $hours, "ARTICULOS" => $articulos, "TECNICOS" => $tecnicosbox, "MANTENIMIENTOS" => $mantenimientosbox,
											"FORMAS_PAGO" => $pagosbox, "DIAS" => $dias, "DROPTALLERES" => $droptalleres) );
}

$newsform = $Newstemplate->compileandsend( );
if($_SESSION["level"]==1){
	$Menutemplate = new template( );
	$Menutemplate->addMTemplate( "menu".$_SESSION["level"] );
	$Menutemplate->asigna_variables( array( "DIR"  => $dir ) );
	$menu = $Menutemplate->compileandsend( );
}
else
{
	//print_r($_SESSION);
	$menu = $ESObject->buildmenu($dir);
}
$Contenido->asigna_variables( array( "SYSTEM" => $arreglo['SYSTEM'], "MENU" => $menu, "CONTENT" => $newsform ) );
$Contenido->compileandgo( );

//**             PIE DE PAGINA             **//

$Contenido->addMTemplate( "footer" );
$Contenido->asigna_variables( array( "DIR" => $dir, "MAP" => "" ) );
$Contenido->compileandgo();
?>

