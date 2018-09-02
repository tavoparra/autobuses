<?php
//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui
define( 'IN_EMADMIN', true );

//Agregamos las librerias comunes
require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion
$ESObject = new exportacion( '../' );
$dir      = "../";
$Block    = "";

header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

///  <<----- ESTE ES EL TAMAÑO DE LA PAGINA EN REGISTROS
//**         CABECERA Y MENU IZQ            **//
$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => $arreglo['NEWSTITLE'], "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//
$Contenido->addTemplate( "ordenesexportacionbody" );

if($_SESSION["level"]==1){
	$Menutemplate = new template( );
	$Menutemplate->addMTemplate( "menu".$_SESSION["level"] );
	$Menutemplate->asigna_variables( array( "DIR"  => $dir ) );
	$menu = $Menutemplate->compileandsend( );
}
else{
	$menu = $ESObject->buildmenu($dir);
}

if(isset($_SESSION['permisos']['8']['1']) || $_SESSION["level"]==1){
	$temp_template = new Template();
	$temp_template->addTemplate("reporte1");
	$mantenimientos = $ESObject->getmantenimientos("tipo_mantenimiento");
	$temp_template->asigna_variables(array("MANTENIMIENTOS" => $mantenimientos));
	$reporte1 = $temp_template->compileandsend();
}
if(isset($_SESSION['permisos']['8']['2']) || $_SESSION["level"]==1){
	$temp_template = new Template();
	$temp_template->addTemplate("reporte2");
	$mantenimientos2 = $ESObject->getmantenimientos("tipo_mantenimiento2");
	$temp_template->asigna_variables(array("MANTENIMIENTOS2" => $mantenimientos2));
	$reporte2 = $temp_template->compileandsend();
}
if(isset($_SESSION['permisos']['8']['3']) || $_SESSION["level"]==1){
	$temp_template = new Template();
	$temp_template->addTemplate("reporte3");
	$reporte3 = $temp_template->compileandsend();
}
if(isset($_SESSION['permisos']['8']['4']) || $_SESSION["level"]==1){
	$temp_template = new Template();
	$temp_template->addTemplate("reporte4");
	$reporte4 = $temp_template->compileandsend();
}
if(isset($_SESSION['permisos']['8']['5']) || $_SESSION["level"]==1){
	$temp_template = new Template();
	$temp_template->addTemplate("reporte5");
	$mantenimientos5 = $ESObject->getmantenimientos("tipo_mantenimiento5");
	$temp_template->asigna_variables(array("MANTENIMIENTOS5" => $mantenimientos5));
	$reporte5 = $temp_template->compileandsend();
}

$Contenido->asigna_variables( array( "CONTENT" => $Block, "SYSTEM" => $arreglo['SYSTEM'], "MENU" => $menu,
									"REPORTE1" => $reporte1,"REPORTE2" => $reporte2,"REPORTE3" => $reporte3, "REPORTE4" => $reporte4, "REPORTE5" => $reporte5 ) );
$Contenido->compileandgo( );

//**             PIE DE PAGINA             **//
$Contenido->addMTemplate( "footer" );
$Contenido->asigna_variables( array( "DIR" => $dir, "MAP" => "" ) );
$Contenido->compileandgo( );
?>