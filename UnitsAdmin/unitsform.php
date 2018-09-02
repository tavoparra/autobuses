<?php

//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui

define( 'IN_EMADMIN', true );

//Definimos PAGE_NAME que nos permite establecer el titulo de nuestra página

if ( isset( $_GET['mode'] ) )
    $mode = $_GET['mode'];
else
    $mode = "";
	
$unidadID =$_GET['unidadID'];

$dir = "../";

//Agregamos las librerias comunes
header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new units( '../' );

//**         CABECERA Y MENU IZQ            **//

$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => "Panel de Control - Unidades", "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "unitsformbody" );

//Inicializamos las variables
if(!isset($_SESSION['permisos']['4']['2']) && $_SESSION["level"]==2)
	$link_update = '&nbsp;';
else
	$link_update = '<a href="javascript:validate(1);">Actualizar</a>';

	
if(!isset($_SESSION['permisos']['4']['3']) && $_SESSION["level"]==2)
	$link_delete = '&nbsp;';
else
	$link_delete = '<a href="javascript:MsgOkCanceldrop(\'&iquest;Est&aacute; Seguro de que Desea Eliminar Esta Unidad?\', 2 );">Eliminar </a>';


$unittemplate = new template( );

if ( $mode == 'edit' )
{
    $unittemplate->addTemplate( "unitsformedit" );
    //Si esta en modo de edición, jala los datos de la BD para llenar automaticamente el formulario

    $unitsdata  		= $ESObject->infoUnits($unidadID);
	$unidadID 			= $unitsdata->fields['unidadID'];
    $clienteID 			= $unitsdata->fields['idCliente'];
	$clientename		= $ESObject->getclientenombre($clienteID);
	$tallerID  			= $unitsdata->fields['tallerID'];
	$tallername			= $ESObject->gettallernombre($tallerID);
	$numEconomico 		= $unitsdata->fields['numEconomico'];
	$tipo  				= $unitsdata->fields['tipo'];
	$marca 				= $unitsdata->fields['marca'];
	$modelo  			= $unitsdata->fields['modelo'];
	$placas  			= $unitsdata->fields['placas'];
	$numSerie  			= $unitsdata->fields['numSerie'];
	$garantiaID  		= $unitsdata->fields['garantiaID'];
	$marcaEquipo		= $unitsdata->fields['marcaEquipo'];
	$modeloEquipo		= $unitsdata->fields['modeloEquipo'];
	$serieEquipo		= $unitsdata->fields['serieEquipo'];
	$marcaCaja			= $unitsdata->fields['marcaCaja'];
	$modeloCaja			= $unitsdata->fields['modeloCaja'];
	$serieCaja			= $unitsdata->fields['serieCaja'];
	$marcaCondensador	= $unitsdata->fields['marcaCondensador'];
	$modeloCondensador	= $unitsdata->fields['modeloCondensador'];
	$serieCondensador	= $unitsdata->fields['serieCondensador'];
	$marcaEvaporador	= $unitsdata->fields['marcaEvaporador'];
	$modeloEvaporador	= $unitsdata->fields['modeloEvaporador'];
	$serieEvaporador	= $unitsdata->fields['serieEvaporador'];
	$marcaCompresor		= $unitsdata->fields['marcaCompresor'];
	$modeloCompresor	= $unitsdata->fields['modeloCompresor'];
	$serieCompresor		= $unitsdata->fields['serieCompresor'];
	$marcaMotor			= $unitsdata->fields['marcaMotor'];
	$modeloMotor		= $unitsdata->fields['modeloMotor'];
	$serieMotor			= $unitsdata->fields['serieMotor'];
	$marcaMicro			= $unitsdata->fields['marcaMicro'];
	$modeloMicro		= $unitsdata->fields['modeloMicro'];
	$serieMicro			= $unitsdata->fields['serieMicro'];
	
	$historial = $ESObject->get_historial(4, $unidadID);
	    
    $unittemplate->asigna_variables( array( 	
												"UNIDADID"				=> $unidadID, 
												"CLIENTEID"				=> $clienteID,
											    "CLIENTENAME"			=> $clientename,
												"TALLERID"				=> $tallerID,
	     									    "TALLERNAME"			=> $tallername, 
											    "NUMECONOMICO" 			=> $numEconomico, 
											    "TIPO" 					=> $tipo, 
											    "MARCA"	 				=> $marca, 
											    "MODELO"	 			=> $modelo,
											    "PLACAS"	 			=> $placas,
											    "NUMSERIE"	 			=> $numSerie,
												"GARANTIAID" 			=> $garantiaID,
												"MARCAEQUIPO"			=> $marcaEquipo,
												"MODELOEQUIPO"			=> $modeloEquipo,
												"SERIEEQUIPO"			=> $serieEquipo,
												"MARCACAJA"				=> $marcaCaja,
												"MODELOCAJA"			=> $modeloCaja,
												"SERIECAJA"				=> $serieCaja,
												"MARCACONDENSADOR"		=> $marcaCondensador,
												"MODELOCONDENSADOR"		=> $modeloCondensador,
												"SERIECONDENSADOR"		=> $serieCondensador,
												"MARCAEVAPORADOR"		=> $marcaEvaporador,
												"MODELOEVAPORADOR"		=> $modeloEvaporador,
												"SERIEVAPORADOR"		=> $serieEvaporador,
												"MARCACOMPRESOR"		=> $marcaCompresor,
												"MODELOCOMPRESOR"		=> $modeloCompresor,
												"SERIECOMPRESOR"		=> $serieCompresor,
												"MARCAMOTOR"			=> $marcaMotor,
												"MODELOMOTOR"			=> $modeloMotor,
												"SERIEMOTOR"			=> $serieMotor,
												"MARCAMICRO"			=> $marcaMicro,
												"MODELOMICRO"			=> $modeloMicro,
												"SERIEMICRO"			=> $serieMicro,
												"LINK_UPDATE" => $link_update,
												"LINK_DELETE" => $link_delete,
												"HISTORIAL"		=> $historial
											  ) 
									   );
    
}
else
{
    $unittemplate->addTemplate( "unitsformadd" );
	$newid = $ESObject->newID();
	$clientesbox  = $ESObject->getclientes();
    $unittemplate->asigna_variables(array( "FORM_ACTION" => $formaction, "MODE" => $mode, "NEWID" => $newid, "CLIENTESBOX" => $clientesbox));
}

$rolesform = $unittemplate->compileandsend( );
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
$Contenido->asigna_variables( array( "SYSTEM" => $arreglo['SYSTEM'], "MENU" => $menu, "CONTENT" => $rolesform ) );
$Contenido->compileandgo( );

//**             PIE DE PAGINA             **//

$Contenido->addMTemplate( "footer" );
$Contenido->asigna_variables( array( "DIR" => $dir, "MAP" => "" ) );
$Contenido->compileandgo( );
?>

