<?php
//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui

define( 'IN_EMADMIN', true );

//Agregamos las librerias comunes

require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new articles( '../' );
$dir      = "../";
$Block    = "";

header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

if ( !isset( $_GET['pag'] ) )
{
    $pag = 1;
}
else
{
    $pag = intval($_GET['pag']);
}
$regsize = 10;


$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => $arreglo['NEWSTITLE'], "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "articlesadminbody" );

//Esta función obtiene los artículos de la base de datos
$resultados_row = $ESObject->verArticulos( $regsize, $pag );
$numreg = $ESObject->cuentaReg( "articles", "1" );
$paging = $ESObject->paginar( $pag, $numreg, $regsize, "?pag=" );


//Creamos un nuevo objeto plantilla, este nos ayudará a crear las tablas de forma
//dinámica

$Tabletemp = new Template( );

//Primero agregamos la capacidad de ingresar nuevos registros
//La unica variable q contiene este template es hacia donde lo vamos a enviar

if ( !$resultados_row->EOF )
{
	$color = 2;
    do
    {
		$articleID = $resultados_row->fields["id"];
		$articleCode = $resultados_row->fields["code"];
        $articleName = $resultados_row->fields["name"];
		$articlePrice = $resultados_row->fields["price"];
//		$ubicacion = $ESObject->getubicacion($resultados_row->fields["ciudadid"]);
        $Tabletemp->addTemplate ( "articlestable" );
		
		if(!isset($_SESSION['permisos']['5']['2']) && !isset($_SESSION['permisos']['5']['3']) && !isset($_SESSION['permisos']['5']['4']) && $_SESSION["level"]==2)
		{
			$link = "#";
		}
		else
		{
			$link = "articlesform.php?mode=edit&articleid=".$articleID;
		}
		
        $Tabletemp->asigna_variables( 
										array( 
												"ARTICLEID" => $articleID,
												"ARTICLECODE" => $articleCode,
												"ARTICLENAME" => $articleName,
												"ARTICLEPRICE" => '$'.$articlePrice, 
												"ROWCOLOR" => $arreglo['ROWCOLOR'.$color],
												"LINK"=> $link
										) 
									);
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
    $texto = "No Existen Art&iacute;culos Registrados";
	$Tabletemp->addTemplate( "articlesRowVoid" );
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

if(!isset($_SESSION['permisos']['5']['1']) && $_SESSION["level"]==2)
{
	$add_dis1 = '<!--';
	$add_dis2 = '-->';
}

$Contenido->asigna_variables( array( "CONTENT" => $Block, "SYSTEM" => $arreglo['SYSTEM'], "MENU" => $menu, "PAGING" => $paging,
									 "ADD_DIS1" => $add_dis1, "ADD_DIS2" => $add_dis2 ) );
$Contenido->compileandgo( );

//**             PIE DE PAGINA             **//

$Contenido->addMTemplate( "footer" );
$Contenido->asigna_variables( array( "DIR" => $dir, "MAP" => "" ) );
$Contenido->compileandgo( );
?>