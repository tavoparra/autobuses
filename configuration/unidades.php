<?php
session_start();
unset($_SESSION['contactos']);
//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui

define( 'IN_EMADMIN', true );

//Definimos PAGE_NAME que nos permite establecer el titulo de nuestra página

if ( isset( $_GET['mode'] ) )
    $mode = $_GET['mode'];
else
    $mode = "";
	
$clienteid =$_GET['clienteid'];

$dir = "../";

//Agregamos las librerias comunes
header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new configuration( '../' );

//**         CABECERA Y MENU IZQ            **//

$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => "Panel de Control - Configuraci&oacute;n", "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "medidasformbody" );

//Inicializamos las variables


$clientestemplate = new template( );

if ( $mode == 'edit' )
{
    $clientestemplate->addTemplate( "medidasformedit" );
    //Si esta en modo de edición, jala los datos de la BD para llenar automaticamente el formulario

    $clientesdata  = $ESObject->infoclientes( $clienteid );
	$clienteid  = $clientesdata->fields['clienteid'];
	$cliente_cod  = $clientesdata->fields['cliente_cod'];

		
	$contactos_content = $ESObject->get_contactos($clienteid);
    
    $clientestemplate->asigna_variables( array( "CLIENTEID"	=> $clienteid,
											"CLIENTE_COD"	=> $cliente_cod,	) );
    
}
else
{
    $clientestemplate->addTemplate( "medidasformadd" );
    $formaction = 'config/workmedidas.php?mode=addMedidas';
	
    $clientestemplate->asigna_variables(array( "FORM_ACTION" => $formaction));
}

$rolesform = $clientestemplate->compileandsend( );
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

