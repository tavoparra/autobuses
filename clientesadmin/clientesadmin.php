<?php
//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui

define( 'IN_EMADMIN', true );

//Agregamos las librerias comunes

require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new clientes( '../' );
$dir      = "../";
$Block    = "";

header( "Cache-Control: no-store, no-cache, must-revalidate" );
//header( "Content-Type: text/html; charset=utf-8" );
//Inicializamos la variable de paginacion
$pag = isset($_GET['pag']) ? intval($_GET['pag']) : 1;
$regsize = 10;


///  <<----- ESTE ES EL TAMAÑO DE LA PAGINA EN REGISTROS
//**         CABECERA Y MENU IZQ            **//

$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => $arreglo['NEWSTITLE'], "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "clientesadminbody" );

//Esta función obtiene las noticias de la base de datos

$resultados_row = $ESObject->verClientes( $regsize, $pag );
$numreg = $ESObject->cuentaReg( "clientes", "1" );
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
		$codigo = $resultados_row->fields["cliente_cod"];
		$clienteid = $resultados_row->fields["clienteid"];
		$rfc = $resultados_row->fields["rfc"];
        $Tabletemp->addTemplate ( "clientestable" );
		
		if(!isset($_SESSION['permisos']['2']['2']) && !isset($_SESSION['permisos']['2']['3']) && !isset($_SESSION['permisos']['2']['4']) && $_SESSION["level"]==2)
		{
			$link = "#";
		}
		else
		{
			$link = "clientesform.php?mode=edit&clienteid=".$clienteid;
		}
		
		
        $Tabletemp->asigna_variables( array( "NOMBRE" => $nombre, "CODIGO" => $codigo, "CLIENTEID" => $clienteid, "RFC" => $rfc, "ROWCOLOR" => $arreglo['ROWCOLOR'.$color],
											"LINK" => $link) );
        $Block .= $Tabletemp->compileandsend( );

        $resultados_row->MoveNext( );
		$color = ($color == 2) ? 1 : 2;
    }
    while ( !$resultados_row->EOF );
}
else
{
    //Si no encuentra ningun registro
    $texto = "No Existen Clientes Registrados";
	$Tabletemp->addTemplate( "rowclientesvoid" );
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

$estadoslist = $ESObject->getestados();
if(!isset($_SESSION['permisos']['2']['1']) && $_SESSION["level"]==2)
{
	$add_dis1 = '<!--';
	$add_dis2 = '-->';
}

$Contenido->asigna_variables( array( "CONTENT" => $Block, "SYSTEM" => $arreglo['SYSTEM'], "MENU" => $menu, "PAGING" => $paging, "ESTADOSLIST" => $estadoslist,
									 "ADD_DIS1" => $add_dis1, "ADD_DIS2" => $add_dis2	) );
$Contenido->compileandgo( );

//**             PIE DE PAGINA             **//

$Contenido->addMTemplate( "footer" );
$Contenido->asigna_variables( array( "DIR" => $dir, "MAP" => "" ) );
$Contenido->compileandgo( );
?>

