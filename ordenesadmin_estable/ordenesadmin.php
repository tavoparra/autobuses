<?php
//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui
define( 'IN_EMADMIN', true );
//Agregamos las librerias comunes
require_once( 'common.inc.php' );
date_default_timezone_set('America/Mexico_City');

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new Ordenes( '../' );
$dir      = "../";
$Block    = "";

header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );
//Inicializamos la variable de paginacion

$pag = isset( $_GET['pag']) ? $_GET['pag'] : 1;
$regsize = 10;

///  <<----- ESTE ES EL TAMAÑO DE LA PAGINA EN REGISTROS
//**         CABECERA Y MENU IZQ            **//

$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => $arreglo['NEWSTITLE'], "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "ordenesadminbody" );

//Esta función obtiene las noticias de la base de datos

$resultados_row = $ESObject->verordenes( $regsize, $pag );
$numreg = $ESObject->cuentaReg( "ordenes", "1" );

//Creamos un nuevo objeto plantilla, este nos ayudará a crear las tablas de forma
//dinámica

$Tabletemp = new Template( );

//Primero agregamos la capacidad de ingresar nuevos registros
//La unica variable q contiene este template es hacia donde lo vamos a enviar

if ( !$resultados_row->EOF )
{
    $color = 1;
    do
    {
        $ordenid    = $resultados_row->fields["ordenid"];
        $fecha  = date("d-m-Y",strtotime($resultados_row->fields["fecha_orden"]));
        $newsdate  = $resultados_row->fields["fecha"];
        $folio   = $resultados_row->fields["folio"];
		$unidadid   = $resultados_row->fields["unidadid"];
		$unidad	=  $ESObject->getunidadinfo($unidadid);
		
		if(!isset($_SESSION['permisos']['6']['2']) && !isset($_SESSION['permisos']['6']['3']) && !isset($_SESSION['permisos']['6']['4']) && $_SESSION["level"]==2)
		{
			$link = "#";
		}
		else
		{
			$link = "ordenesform.php?mode=edit&ordenid=".$ordenid;
		}
		
        $Tabletemp->addTemplate ( "ordenestable" );
        $Tabletemp->asigna_variables( array( "FECHA" => $fecha, "ORDENID" => $ordenid, "FOLIO" => $folio, "COLOR" => $arreglo['NEWSCLASS'.$color], "UNIDAD" => $unidad,
										"NEWSDATECLASS" => $arreglo['NEWSDATECLASS'.$color], "LINK" => $link) );
        $Block .= $Tabletemp->compileandsend( );
        if ( $color == 2 )
            $color = 1;
        else
            $color = 2;
        $resultados_row->MoveNext( );
    }
    while ( !$resultados_row->EOF );
}
else
{
    //Si no encuentra ningun registro
    $texto = "No Existen Ordenes Registradas";
	$Tabletemp->addTemplate( "rownewsvoid" );
    $Tabletemp->asigna_variables( array( "TEXTO_MENSAJE" => $texto ) );
    $Block .= $Tabletemp->compileandsend( );
}

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

if(!isset($_SESSION['permisos']['6']['1']) && $_SESSION["level"]==2)
{
	$add_dis1 = '<!--';
	$add_dis2 = '-->';
}

$paging = $ESObject->paginar( $pag, $numreg, $regsize, "?pag=" );
$Contenido->asigna_variables( array( "CONTENT" => $Block, "SYSTEM" => $arreglo['SYSTEM'], "MENU" => $menu, "PAGING" => $paging,
									 "ADD_DIS1" => $add_dis1, "ADD_DIS2" => $add_dis2 ) );
$Contenido->compileandgo( );

//**             PIE DE PAGINA             **//

$Contenido->addMTemplate( "footer" );
$Contenido->asigna_variables( array( "DIR" => $dir, "MAP" => "" ) );
$Contenido->compileandgo( );
?>

