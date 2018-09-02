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

$ESObject = new clientes( '../' );

//**         CABECERA Y MENU IZQ            **//

$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => "Panel de Control - Clientes", "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "clientesformbody" );
//Inicializamos las variables
$clientestemplate = new template( );

if(!isset($_SESSION['permisos']['2']['2']) && $_SESSION["level"]==2)
	$link_update = '&nbsp;';
else
	$link_update = '<a href="javascript:validate(1);">Actualizar</a>';

	
if(!isset($_SESSION['permisos']['2']['3']) && $_SESSION["level"]==2)
	$link_delete = '&nbsp;';
else
	$link_delete = '<a href="javascript:MsgOkCanceldrop(\'&iquest;Est&aacute; Seguro de que Desea Eliminar Este Cliente?\', 2 );">Eliminar </a>';


if ( $mode == 'edit' )
{
    $clientestemplate->addTemplate( "clientesformedit" );
    //Si esta en modo de edición, jala los datos de la BD para llenar automaticamente el formulario

    $clientesdata  = $ESObject->infoclientes( $clienteid );
	$clienteid  = $clientesdata->fields['clienteid'];
	$cliente_cod  = $clientesdata->fields['cliente_cod'];
    $nombre  = $clientesdata->fields['nombre'];
	$calle  = $clientesdata->fields['calle'];
	$num_ext  = $clientesdata->fields['num_ext'];
	$num_int  = $clientesdata->fields['num_int'];
	$colonia  = $clientesdata->fields['colonia'];
	$cod_postal  = $clientesdata->fields['cod_postal'];
	$municipio  = $clientesdata->fields['municipio'];
	$rfc  = $clientesdata->fields['rfc'];
	$telefono  = $clientesdata->fields['telefono'];
	$url  = $clientesdata->fields['url'];
	$contrato  = $clientesdata->fields['contrato'];
	$descuento  = $clientesdata->fields['descuento'];
	
	
	$lista_precios = $ESObject->getlistaprecios($clientesdata->fields['lista_precios']);
    $estados = $ESObject->getestados($clientesdata->fields['estadoid']);
	$ciudades = $ESObject->getciudades($clientesdata->fields['estadoid'], $clientesdata->fields['ciudadid']);

	if($clientesdata->fields['logotipo'] != '')
		$ver_imagen = '<a href="logotipos/'.$clientesdata->fields['logotipo'].'" target = "_blank">Ver actual</a>';
	else
		$ver_imagen = '';
		
	$contactos_content = $ESObject->get_contactos($clienteid);
	
	$historial = $ESObject->get_historial(2, $clienteid);
    
    $clientestemplate->asigna_variables( array( "CLIENTEID"	=> $clienteid,
											"CLIENTE_COD"	=> $cliente_cod,	
											"NOMBRE"		=> $nombre, 
	     									"CALLE" 		=> $calle, 
											"NUM_EXT" 		=> $num_ext, 
											"NUM_INT" 		=> $num_int, 
											"COLONIA"	 	=> $colonia, 
											"COD_POSTAL"	=> $cod_postal, 
											"MUNICIPIO" 	=> $municipio, 
											"RFC" 			=> $rfc, 
											"TELEFONO"		=> $telefono, 
											"URL" 			=> $url,
											"CONTRATO"		=> $contrato,
											"ESTADOS" 		=> $estados,
											"CIUDADES" 		=> $ciudades,
											"VER_IMAGEN"	=> $ver_imagen,
											"DESCUENTO"		=> $descuento,
											"CONTACTOS_CONTENT"=>$contactos_content,
											"LINK_UPDATE" => $link_update,
											"LINK_DELETE" => $link_delete,
											"LISTA_PRECIOS" => $lista_precios,
											"HISTORIAL"		=> $historial) );
    
}
else
{
    $clientestemplate->addTemplate( "clientesformadd" );
    $formaction = 'config/workclientes.php?mode=addClientes';
	
	$newid = $ESObject->newid();
	$estados = $ESObject->getestados();
	$ciudades = $ESObject->getciudades();
	$lista_precios = $ESObject->getlistaprecios();
	$newcode = $ESObject->newcode();
	
    $clientestemplate->asigna_variables(array( "FORM_ACTION" => $formaction, "MODE" => $mode, "ESTADOS" => $estados, "CIUDADES" => $ciudades, "NEWID" => $newid,
											"LISTA_PRECIOS" => $lista_precios, "NEWCODE"=>$newcode));
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

