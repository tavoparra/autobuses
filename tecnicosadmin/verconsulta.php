<?php
define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );

$ESObject = new tecnicos( '../' );
$dir      = "../";
$Block    = "";

header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );
//Inicializamos la variable de paginacion

if ( !isset( $_GET['pag'] ) ) $pag = 1; else $pag = intval($_GET['pag']);

$texto = $_GET['nombre']; $campo = $_GET['campo']; $estadoid = $_GET['estadoid'];

$_SESSION['filtro_s']	= true;
$_SESSION['texto_s']	= $texto;
$_SESSION['campo_s']	= $campo;
$_SESSION['estado_s'] 	= $estadoid;

$filtro = ' '.$campo.' like "%'.$texto.'%"';

if($estadoid > 0)
{
	$filtro .= ' and estadoid = '.$estadoid;
}
		
$regsize = 10;

if ($filtro == "") $filtro = 1;

$_SESSION['cadenaf'] = $filtro;

$resultados_row = $ESObject->verTecnicos($regsize, $pag, $filtro);
$numreg = $ESObject->cuentaReg( "tecnicos", $filtro );


$Contenido = new template( );
$Contenido->addTemplate( "filtradobody" );

$Tabletemp = new Template( );

//Primero agregamos la capacidad de ingresar nuevos registros
//La unica variable q contiene este template es hacia donde lo vamos a enviar

if ( !$resultados_row->EOF )
{
	$color = 1;
    do
    {
        $nombre = $resultados_row->fields["nombre"]." ".$resultados_row->fields["apeido_pat"]." ".$resultados_row->fields["apeido_mat"];
		$codigo = $resultados_row->fields["codigo"];
		$tecnicoid = $resultados_row->fields["tecnicoid"];
		$puesto = $resultados_row->fields["puesto"];
        $Tabletemp->addTemplate ( "tecnicostable" );
		
		if(!isset($_SESSION['permisos']['7']['2']) && !isset($_SESSION['permisos']['7']['3']) && !isset($_SESSION['permisos']['7']['4']) && $_SESSION["level"]==2)
		{
			$link = "#";
		}
		else
		{
			$link = "tecnicosform.php?mode=edit&tecnicoid=".$tecnicoid;
		}
		
		
        $Tabletemp->asigna_variables( array( "NOMBRE" => $nombre, "CODIGO" => $codigo, "TECNICOID" => $tecnicoid, "PUESTO" => $puesto, "ROWCOLOR" => $arreglo['ROWCOLOR'.$color], "LINK" => $link) );
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
    $texto = "No Existen T&eacute;cnicos Registrados";
	$Tabletemp->addTemplate( "rowtecnicosvoid" );
    $Tabletemp->asigna_variables( array( "TEXTO_MENSAJE" => $texto ) );
    $Block .= $Tabletemp->compileandsend( );
}

$paging = $ESObject->paginarfiltro( $pag, $numreg, $regsize, $texto, $campo, $estadoid);

$Contenido->asigna_variables( array( "CONTENT" => $Block, "PAGING" => $paging, "DIR" => $dir, "TODOS" => $numreg) );
$Contenido->compileandgo( );
?>