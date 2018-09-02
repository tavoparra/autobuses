<?php

//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui

define( 'IN_EMADMIN', true );

//Definimos PAGE_NAME que nos permite establecer el titulo de nuestra página

if ( isset( $_GET['mode'] ) )
{
    $mode = $_GET['mode'];
}
else
{
    $mode = "";
}
if ( isset( $_GET['newsid'] ) )
{
    $newsid = $_GET['newsid'];
}
else
{
    $newsid = "";
}
$dir = "../";

//Agregamos las librerias comunes

header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new Noticias( '../' );

//**         CABECERA Y MENU IZQ            **//

$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => "Panel de Control - Noticias", "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "newsformbody" );

//Inicializamos las variables

$vigencia = "0000-00-00";
$Newstemplate = new template( );

if ( $mode == "edit" )
{
    $Newstemplate->addTemplate( "newsformedit" );
}
else
{
    $Newstemplate->addTemplate( "newsformadd" );
}
if ( $mode == 'edit' )
{

    //Si esta en modo de edición, jala los datos de la BD para llenar automaticamente el formulario

    $Newsdata   = $ESObject->getNewsdata( $newsid );
    $newsid     = $Newsdata->fields["noticia"];
    $title      = $Newsdata->fields["titulo"];
    $previs     = $Newsdata->fields["previs"];
    $content    = $Newsdata->fields["content"];
    $img1       = $Newsdata->fields["imgone"];
    $newsdate   = $Newsdata->fields["fecha"];
    $vigencia   = $Newsdata->fields["vigencia"];
	$archivo 	= $Newsdata->fields["archivo"];
	$tienefv 	= $Newsdata->fields["tienefv"];

    $formaction = 'config/worknews.php?mode=edit&idNoticia='.$newsid;
    
    if ( $img1 == "" )
    {
        $imgst = 'No Existe Imagen Asignada';
    }
    else
    {
        $imgst = '<a href="javascript:PopupPic(\'media/'.$img1.'\')" > Ver Imagen Actual</a>';
    }
    
	
	//Aparece en Portada
	$combo_portada = "<select name='tienefv' id='tienefv'>";
	for ( $i = 0; $i <= 1; $i++ )
	{
		if($i==1)
			$portada="Si";
		else
			$portada="No";
	
		 if ( $i == $tienefv )
		 {
			 $combo_portada .= "<option value='".$i."' selected>".$portada."</option>";
		 }
		 else
		 {
			 $combo_portada .= "<option value='".$i."'>".$portada."</option>";
		 }
	}
	$combo_portada .= "</select>";
	
    $Newstemplate->asigna_variables( array( "PREVIS" 		=> $previs, 
	     									"CONTENT" 		=> $content, 
											"IMGSTATE" 		=> $imgst, 
											"SIGNDATE" 		=> $newsdate, 
											"VIGENCIA" 		=> $vigencia, 
											"ARCHIVO"  		=> $archivo,
											"FORM_ACTION" 	=> $formaction, 
											"ACTIMG" 		=> $img1, 
											"TITLE" 		=> $title, 
											"NEWSID" 		=> $newsid, 
											"MODE" 			=> $mode, 
											"COMBOPORTADA"	=> $combo_portada ) );
    
}
else
{
    $formaction = 'config/worknews.php?mode=addNews';
    $newsdate = 'Automática';
    $Newstemplate->asigna_variables( array( "FORM_ACTION" => $formaction, "MODE" => $mode, "VIGENCIA" => $vigencia ) );
}

$newsform = $Newstemplate->compileandsend( );
$Menutemplate = new template( );
$Menutemplate->addMTemplate( "menu".$_SESSION["level"] );
$Menutemplate->asigna_variables( array( "DIR" => $dir ) );
$menu = $Menutemplate->compileandsend( );
$Contenido->asigna_variables( array( "SYSTEM" => $arreglo['SYSTEM'], "MENU" => $menu, "CONTENT" => $newsform ) );
$Contenido->compileandgo( );

//**             PIE DE PAGINA             **//

$Contenido->addMTemplate( "footer" );
$Contenido->asigna_variables( array( "DIR" => $dir, "MAP" => "" ) );
$Contenido->compileandgo( );
?>

