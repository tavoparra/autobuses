<?php
session_start();
unset($_SESSION['contactos']);
//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui

define( 'IN_EMADMIN', true );

//Definimos PAGE_NAME que nos permite establecer el titulo de nuestra página
$mode = isset($_GET['mode']) ? $_GET['mode'] : "";
$tallerid =$_GET['tallerid'];

$dir = "../";

//Agregamos las librerias comunes
header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new talleres( '../' );

//**         CABECERA Y MENU IZQ            **//

$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => "Panel de Control - Talleres", "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "talleresformbody" );

//Inicializamos las variables
if(!isset($_SESSION['permisos']['3']['2']) && $_SESSION["level"]==2)
	$link_update = '&nbsp;';
else
	$link_update = '<a href="javascript:validate(1);">Actualizar</a>';

	
if(!isset($_SESSION['permisos']['3']['3']) && $_SESSION["level"]==2)
	$link_delete = '&nbsp;';
else
	$link_delete = '<a href="javascript:MsgOkCanceldrop(\'&iquest;Est&aacute; Seguro de que Desea Eliminar Este Taller? \', 2 );">Eliminar </a>';




$tallerestemplate = new template( );

if ( $mode == 'edit' )
{
    $tallerestemplate->addTemplate( "talleresformedit" );
    //Si esta en modo de edición, jala los datos de la BD para llenar automaticamente el formulario

    $talleresdata  = $ESObject->infotalleres( $tallerid );
    $taller_cod  = $talleresdata->fields['taller_cod'];
	$nombre  = $talleresdata->fields['nombre'];
	$calle  = $talleresdata->fields['calle'];
	$num_ext  = $talleresdata->fields['num_ext'];
	$num_int  = $talleresdata->fields['num_int'];
	$colonia  = $talleresdata->fields['colonia'];
	$cod_postal  = $talleresdata->fields['cod_postal'];
	$municipio  = $talleresdata->fields['municipio'];
	$telefono  = $talleresdata->fields['telefono'];
	$clienteid  = $talleresdata->fields['clienteid'];
	$clientenombre = $ESObject->getclientenombre($clienteid);
    $estados = $ESObject->getestados($talleresdata->fields['estadoid']);
	$ciudades = $ESObject->getciudades($talleresdata->fields['estadoid'], $talleresdata->fields['ciudadid']);
	
    $contactos_content = $ESObject->get_contactos($tallerid);
	
	$historial = $ESObject->get_historial(3, $tallerid);

	if($clienteid == -1)
	{
		$marked = 'checked="checked"';
		$mark = "none";
	}
	else
	{
		$marked = "";
		$mark = "block";
	}
	
    $tallerestemplate->asigna_variables( array( "TALLERID"	=> $tallerid, 
											"NOMBRE"		=> $nombre, 
											"CODIGO"		=> $taller_cod,
	     									"CALLE" 		=> $calle, 
											"NUM_EXT" 		=> $num_ext, 
											"NUM_INT" 		=> $num_int, 
											"COLONIA"	 	=> $colonia, 
											"COD_POSTAL"	=> $cod_postal, 
											"MUNICIPIO" 	=> $municipio,
											"TELEFONO"		=> $telefono, 
											"ESTADOS" 		=> $estados,
											"CIUDADES" 		=> $ciudades,
											"CLIENTEID"		=> $clienteid,
											"CLIENTENOMBRE" => $clientenombre,
											"CONTACTOS_CONTENT"=>$contactos_content,
											"LINK_UPDATE" => $link_update,
											"LINK_DELETE" => $link_delete,
											"MARKED"		=>$marked,
											"MARK"			=>$mark,
											"HISTORIAL"		=> $historial) );
    
}
else
{
    $tallerestemplate->addTemplate( "talleresformadd" );
	
	$newid = $ESObject->newid();
	$estados = $ESObject->getestados();
	$ciudades = $ESObject->getciudades(32);
	$clientescode = $ESObject->getclientes();
	
	$newcode = $ESObject->newcode();
	
    $tallerestemplate->asigna_variables(array( "FORM_ACTION" => $formaction, "MODE" => $mode, "ESTADOS" => $estados, "CIUDADES" => $ciudades, "NEWID" => $newid,
												"CLIENTESCODE" => $clientescode, "NEWCODE"=>$newcode));
}

$rolesform = $tallerestemplate->compileandsend( );
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

