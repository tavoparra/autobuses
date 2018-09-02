<?php
session_start();
unset($_SESSION['contactos']);
//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui

define( 'IN_EMADMIN', true );

//Agregamos las librerias comunes

require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new talleres( '../' );
$dir      = "../";
$Block    = "";

header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );
//Inicializamos la variable de paginacion
if ( !isset( $_GET['pag'] ) )
{
    $pag = 1;
}
else
{
    $pag = intval($_GET['pag']);
}
$regsize = 10;

///  <<----- ESTE ES EL TAMAÑO DE LA PAGINA EN REGISTROS
//**         CABECERA Y MENU IZQ            **//

$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => $arreglo['NEWSTITLE'], "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "talleresadminbody" );

//Esta función obtiene las noticias de la base de datos

$resultados_row = $ESObject->verTalleres( $regsize, $pag );
$numreg = $ESObject->cuentaReg( "talleres", "1" );
$paging = $ESObject->paginar( $pag, $numreg, $regsize, "?pag=" );



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
        $nombre = $resultados_row->fields["nombre"];
		$tallerid = $resultados_row->fields["tallerid"];
		$taller_cod = $resultados_row->fields["taller_cod"];
		$ubicacion = $ESObject->getubicacion($resultados_row->fields["ciudadid"]);
		
		if(isset($_SESSION['permisos']['3']['2']) || isset($_SESSION['permisos']['3']['3']) || isset($_SESSION['permisos']['3']['4']) || $_SESSION["level"]==1)
		{
			$link = "talleresform.php?mode=edit&tallerid=".$tallerid;
		}
		else
		{
			$link = "#";
		}
		
        $Tabletemp->addTemplate ( "tallerestable" );
        $Tabletemp->asigna_variables( array( "NOMBRE" => $nombre, "CODIGO" => $taller_cod, "TALLERID" => $tallerid, "UBICACION" => $ubicacion, "ROWCOLOR" => $arreglo['ROWCOLOR'.$color],
										"LINK" => $link));
        $Block .= $Tabletemp->compileandsend( );

        $resultados_row->MoveNext( );
		if ($color == 2)			
			$color = 1;
		else
			$color = 2;
    }
    while ( !$resultados_row->EOF );
}
else
{
    //Si no encuentra ningun registro
    $texto = "No Existen Talleres Registrados";
	$Tabletemp->addTemplate( "rowtalleresvoid" );
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

if(!isset($_SESSION['permisos']['3']['1']) && $_SESSION["level"]==2)
{
	$add_dis1 = '<!--';
	$add_dis2 = '-->';
}

$estadoslist = $ESObject->getestados();
$Contenido->asigna_variables( array( "CONTENT" => $Block, "SYSTEM" => $arreglo['SYSTEM'], "MENU" => $menu, "PAGING" => $paging, "ESTADOSLIST" => $estadoslist,
									 "ADD_DIS1" => $add_dis1, "ADD_DIS2" => $add_dis2 ) );
$Contenido->compileandgo( );



//**             PIE DE PAGINA             **//

$Contenido->addMTemplate( "footer" );
$Contenido->asigna_variables( array( "DIR" => $dir, "MAP" => "" ) );
$Contenido->compileandgo( );
?>

