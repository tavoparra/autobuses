<?php

//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui

define( 'IN_EMADMIN', true );

//Definimos PAGE_NAME que nos permite establecer el titulo de nuestra página

if ( isset( $_GET['mode'] ) )
    $mode = $_GET['mode'];
else
    $mode = "";
	
$articleID =$_GET['articleid'];

$dir = "../";

//Agregamos las librerias comunes
header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new articles( '../' );

//**         CABECERA Y MENU IZQ            **//

$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => "Panel de Control - Art&iacute;culos", "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "articlesformbody" );

//Inicializamos las variables
if(!isset($_SESSION['permisos']['5']['2']) && $_SESSION["level"]==2)
	$link_update = '&nbsp;';
else
	$link_update = '<a href="javascript:validate(1);">Actualizar</a>';

	
if(!isset($_SESSION['permisos']['5']['3']) && $_SESSION["level"]==2)
	$link_delete = '&nbsp;';
else
	$link_delete = '<a href="javascript:MsgOkCanceldrop(\'&iquest;Est&aacute; Seguro de que Desea Eliminar Este Art&iacute;culo?\', 2 );">Eliminar</a>';


$articletemplate = new template( );

if ( $mode == 'edit' )
{
    $articletemplate->addTemplate( "articlesformedit" );
    //Si esta en modo de edición, jala los datos de la BD para llenar automaticamente el formulario

    $articlesdata  = $ESObject->infoArticles( $articleID );
	$articleID = $articlesdata->fields['id'];
    $code  = $articlesdata->fields['code'];
	$name  = $articlesdata->fields['name'];
	$desc  = $articlesdata->fields['desc'];
	$medida_id  = $articlesdata->fields['medida_id'];
	$weight  = $articlesdata->fields['weight'];
	$price1  = $articlesdata->fields['price'];
	$price2  = $articlesdata->fields['price2'];
	$price3  = $articlesdata->fields['price3'];
	$price4  = $articlesdata->fields['price4'];
	$price5  = $articlesdata->fields['price5'];
	$costo   = $articlesdata->fields['costo'];
	$mano_obra = $articlesdata->fields['mano_obra'];
		if($mano_obra == 1)
			$checked = 'checked="checked"';
	$dollars = $articlesdata->fields['dollars'];
		if($dollars == 1)
			$selected = 'selected="selected"';
	$equivale = $articlesdata->fields['equivale'];
	$contenido_medidas = $ESObject->getmedidas($medida_id);
	
	
	$historial = $ESObject->get_historial(5, $articleID);
    
    $articletemplate->asigna_variables( array( "ARTICLEID"			=> $articleID, 
												"CODE"				=> $code, 
	     										"NAME" 				=> $name, 
												"DESC" 				=> $desc, 
												"CONTENIDO_MEDIDAS"	=> $contenido_medidas, 
												"WEIGHT"	 		=> $weight, 
												"PRICE1"			=> $price1,
												"PRICE2"			=> $price2,
												"PRICE3"			=> $price3,
												"PRICE4"			=> $price4,
												"PRICE5"			=> $price5,
												"COSTO"				=> $costo,
												"CHECKED"			=> $checked,
												"SELECTED"			=> $selected,
												"EQUIVALE"			=> $equivale,
												"LINK_UPDATE" => $link_update,
												"LINK_DELETE" => $link_delete,
												"HISTORIAL"		=> $historial
												)
										);
    
}
else
{
    $articletemplate->addTemplate( "articlesformadd" );
	$newid = $ESObject->newid();
	$contenido_medidas = $ESObject->getmedidas();
    $articletemplate->asigna_variables(array( "FORM_ACTION" => $formaction, "MODE" => $mode, "NEWID" => $newid, "CONTENIDO_MEDIDAS"	=> $contenido_medidas));
}

$rolesform = $articletemplate->compileandsend( );
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

