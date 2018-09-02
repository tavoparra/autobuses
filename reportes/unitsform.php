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


$unittemplate = new template( );

if ( $mode == 'edit' )
{
    $unittemplate->addTemplate( "unitsformedit" );
    //Si esta en modo de edición, jala los datos de la BD para llenar automaticamente el formulario

    $unitsdata  		= $ESObject->infoUnits($unidadID);
	$unidadID 			= $unitsdata->fields['unidadID'];
    $clienteID 			= $unitsdata->fields['clienteID'];
	$clientesbox		= $ESObject->getclientes($clienteID);
	$tallerID  			= $unitsdata->fields['tallerID'];
	$talleres 			= $ESObject->gettalleres($clienteID, $tallerID);
	$numEconomico 		= $unitsdata->fields['numEconomico'];
	$tipo  				= $unitsdata->fields['tipo'];
	$marca 				= $unitsdata->fields['marca'];
	$modelo  			= $unitsdata->fields['modelo'];
	$placas  			= $unitsdata->fields['placas'];
	$numSerie  			= $unitsdata->fields['numSerie'];
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
	

	    
    $unittemplate->asigna_variables( array( 	
												"UNIDADID"				=> $unidadID, 
											    "CLIENTESBOX"			=> $clientesbox, 
	     									    "TALLERESBOX"			=> $talleres, 
											    "NUMECONOMICO" 			=> $numEconomico, 
											    "TIPO" 					=> $tipo, 
											    "MARCA"	 				=> $marca, 
											    "MODELO"	 			=> $modelo,
											    "PLACAS"	 			=> $placas,
											    "NUMSERIE"	 			=> $numSerie,
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
												"SERIECOMPRESOR"		=> $serieCompresor
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
$Menutemplate = new template( );
$Menutemplate->addMTemplate( "menu".$_SESSION["level"] );
$Menutemplate->asigna_variables( array( "DIR" => $dir ) );
$menu = $Menutemplate->compileandsend( );
$Contenido->asigna_variables( array( "SYSTEM" => $arreglo['SYSTEM'], "MENU" => $menu, "CONTENT" => $rolesform ) );
$Contenido->compileandgo( );

//**             PIE DE PAGINA             **//

$Contenido->addMTemplate( "footer" );
$Contenido->asigna_variables( array( "DIR" => $dir, "MAP" => "" ) );
$Contenido->compileandgo( );
?>

