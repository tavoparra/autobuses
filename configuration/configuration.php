<?php
//Definimos IN_EMADMIN, que nos permitir� llamar scripts desde aqui
define( 'IN_EMADMIN', true );

//Agregamos las librerias comunes
require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion
$ESObject = new configuration( '../' );
$dir      = "../";
$Block    = "";

header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

//**         CABECERA Y MENU IZQ            **//
$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => $arreglo['NEWSTITLE'], "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

$msg = $ESObject->restore_database();

//**             PAGINA PRINCIPAL             **//
$Contenido->addTemplate( "configurationbody" );

if($_POST['iva'] != '') $ESObject->savetasa($_POST['iva']);
$iva = $ESObject->gettasa();

$Tabletemp = new Template( );
$resultados_row = $ESObject->verMedidas($regsize);

if ( !$resultados_row->EOF ) {
	$color = 1;
    do {
        $nombre = $resultados_row->fields["medida"];
		$medidaid = $resultados_row->fields["medida_id"];
        $Tabletemp->addTemplate ( "medidastable" );
        $Tabletemp->asigna_variables( array( "NOMBRE" => $nombre, "MEDIDAID" => $medidaid, "ROWCOLOR" => $arreglo['ROWCOLOR'.$color]) );
        $Block .= $Tabletemp->compileandsend( );

        $resultados_row->MoveNext( );
		if ($color == 2)			
			$color = 1;
		else
			$color = 2;
    } while ( !$resultados_row->EOF );
} else {
    //Si no encuentra ningun registro
    $texto = "No Existen Medidas Registradas";
	$Tabletemp->addTemplate( "rowmedidasvoid" );
    $Tabletemp->asigna_variables( array( "TEXTO_MENSAJE" => $texto ) );
    $Block .= $Tabletemp->compileandsend( );
}

if($_SESSION["level"]==1){
	$Menutemplate = new template( );
	$Menutemplate->addMTemplate( "menu".$_SESSION["level"] );
	$Menutemplate->asigna_variables( array( "DIR"  => $dir ) );
	$menu = $Menutemplate->compileandsend( );
} else {
	$menu = $ESObject->buildmenu($dir);
}

if(!isset($_SESSION['permisos']['9']['1']) && $_SESSION["level"]==2) {
	$add_dis1 = '<!--';
	$add_dis2 = '-->';
}
if(!isset($_SESSION['permisos']['9']['2']) && $_SESSION["level"]==2) {
	$add_dis3 = '<!--';
	$add_dis4 = '-->';
}

$backup_section = "";
if(isset($_SESSION['permisos']['9']['3']) || $_SESSION["level"]==1) {
	$backup_template = new Template();
	$backup_template->addTemplate("backup_section");
	$backup_section = $backup_template->compileandsend();
}

$backup_file = '';
if ($_GET['action'] === 'backup') {
	include_once("./createbackup.php");
	$script = '<script>location.href="backups/'.$backup_file.'"</script>';
}

$Contenido->asigna_variables( array( "CONTENT" => $Block, "SYSTEM" => $arreglo['SYSTEM'], "MENU" => $menu, "IVA" => $iva, "SCRIPT" => $script,
									 "ADD_DIS1" => $add_dis1, "ADD_DIS2" => $add_dis2,"ADD_DIS3" => $add_dis3, "ADD_DIS4" => $add_dis4,
									 "BACKUP_SECTION" => $backup_section, "MESSAGE" => $msg ) );
$Contenido->compileandgo( );

//**             PIE DE PAGINA             **//
$Contenido->addMTemplate( "footer" );
$Contenido->asigna_variables( array( "DIR" => $dir, "MAP" => "" ) );
$Contenido->compileandgo( );