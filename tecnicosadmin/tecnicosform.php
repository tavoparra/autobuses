<?php
//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui
define( 'IN_EMADMIN', true );
$mode = isset($_GET['mode']) ? $_GET['mode'] : "";
	
$tecnicoid =$_GET['tecnicoid'];

$dir = "../";

//Agregamos las librerias comunes
header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new tecnicos( '../' );

//**         CABECERA Y MENU IZQ            **//

$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => "Panel de Control - T&eacute;cnicos", "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "tecnicosformbody" );

//Inicializamos las variables
if(!isset($_SESSION['permisos']['7']['2']) && $_SESSION["level"]==2)
	$link_update = '&nbsp;';
else
	$link_update = '<a href="javascript:validate(1);">Actualizar</a>';

	
if(!isset($_SESSION['permisos']['7']['3']) && $_SESSION["level"]==2)
	$link_delete = '&nbsp;';
else
	$link_delete = '<a href="javascript:MsgOkCanceldrop(\'¿Está Seguro de que Desea Eliminar Este Técnico?\', 2 );">Eliminar </a>';


$clientestemplate = new template( );

if ( $mode == 'edit' )
{
    $clientestemplate->addTemplate( "tecnicosformedit" );
    //Si esta en modo de edición, jala los datos de la BD para llenar automaticamente el formulario

    $tecnicosdata  = $ESObject->infotecnico( $tecnicoid );
	$tecnicoid  = $tecnicosdata->fields['tecnicoid'];
	$codigo  = $tecnicosdata->fields['codigo'];
    $nombre  = $tecnicosdata->fields['nombre'];
	$apeido_pat  = $tecnicosdata->fields['apeido_pat'];
	$apeido_mat  = $tecnicosdata->fields['apeido_mat'];
	$status  = $tecnicosdata->fields['status'];
	$puesto  = $tecnicosdata->fields['puesto'];
	$salario  = $tecnicosdata->fields['salario'];
	$nss  = $tecnicosdata->fields['nss'];
	$rfc  = $tecnicosdata->fields['rfc'];
	$curp  = $tecnicosdata->fields['curp'];
	
	
	if($status == 2)
		$baja = ' selected = true';
	else
		$baja = '';
	
    $historial = $ESObject->get_historial(7, $tecnicoid);
	
    $clientestemplate->asigna_variables( array( "TECNICOID"	=> $tecnicoid,
											"TECNICOCOD"	=> $codigo,	
											"NOMBRE"		=> $nombre, 
	     									"APEIDO_PAT" 	=> $apeido_pat, 
											"APEIDO_MAT" 	=> $apeido_mat, 
											"BAJA" 			=> $baja, 
											"PUESTO"	 	=> $puesto,
											"SALARIO"		=> $salario,
											"NSS"			=> $nss,  
											"RFC" 			=> $rfc, 
											"TELEFONO"		=> $telefono, 
											"URL" 			=> $url,
											"CURP"			=> $curp,
											"LINK_UPDATE" 	=> $link_update,
											"LINK_DELETE" 	=> $link_delete,
											"HISTORIAL"		=> $historial) );
    
}
else
{
    $clientestemplate->addTemplate( "tecnicosformadd" );
	
	$newid = $ESObject->newid();
	$estados = $ESObject->getestados();
	$ciudades = $ESObject->getciudades(1);
	$newcode = $ESObject->newcode();
	
    $clientestemplate->asigna_variables(array( "FORM_ACTION" => $formaction, "MODE" => $mode, "ESTADOS" => $estados, "CIUDADES" => $ciudades, "NEWID" => $newid, "NEWCODE"=>$newcode));
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

