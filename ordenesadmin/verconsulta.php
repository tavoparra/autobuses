<?php
define( 'IN_EMADMIN', true );
require_once( 'common.inc.php' );
date_default_timezone_set("America/Mexico_City");

$ESObject = new Ordenes( '../' );
$dir      = "../";
$Block    = "";

header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );
//Inicializamos la variable de paginacion

if ( !isset( $_GET['pag'] ) ) $pag = 1; else $pag = intval($_GET['pag']);

$fecha1 = $_GET['fecha1']; $fecha2 = $_GET['fecha2']; $clienteid = $_GET['clienteid'];
$tallerid = $_GET['tallerid']; $unidadid = $_GET['unidadid']; $folio = $_GET['folio']; $tallerservicioid = $_GET['tallerservicioid'];

$dia = substr($fecha1, 0, 2);
$mes   = substr($fecha1, 3, 2);
$ano = substr($fecha1, -4);
// fechal final realizada el cambio de formato a las fechas europeas
$fecha1 = $ano . '-' . $mes . '-' . $dia;

$dia = substr($fecha2, 0, 2);
$mes   = substr($fecha2, 3, 2);
$ano = substr($fecha2, -4);
// fechal final realizada el cambio de formato a las fechas europeas
$fecha2 = $ano . '-' . $mes . '-' . $dia;

$_SESSION['filtro_s']	= true;

if($fecha1 != '--')
{
	$filtro .= ' and o.fecha_orden >= "'.$fecha1.'"';
}

if($fecha2 != '--')
{
	$filtro .= ' and o.fecha_orden <= "'.$fecha2.'"';
}

if($clienteid > 0)
{
	$filtro .= ' and c.clienteid = '.$clienteid;
}

if($tallerid > 0)
{
	$filtro .= ' and t.tallerid = '.$tallerid;
}

if($unidadid > 0)
{
	$filtro .= ' and u.unidadid = '.$unidadid;
}

if($tallerservicioid > 0)
{
	$filtro .= ' and o.taller_servicio = '.$tallerservicioid;
}

if($folio != '')
	$filtro .= ' AND o.folio like "%'.$folio.'%"';
		
$regsize = 10;

$filtro = "1 ".$filtro;

$_SESSION['cadenaf'] = $filtro;

$resultados_row = $ESObject->verordenes( $regsize, $pag, $filtro );
$numreg = $ESObject->countordenes($filtro );


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
        $ordenid    = $resultados_row->fields["ordenid"];
        $fecha  = $resultados_row->fields["fecha_orden"];
        $fecha  = date("d-m-Y",strtotime($resultados_row->fields["fecha_orden"]));
        $folio_orden  = $resultados_row->fields["folio"];
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
        $Tabletemp->asigna_variables( array( "FECHA" => $fecha, "ORDENID" => $ordenid, "FOLIO" => $folio_orden, "COLOR" => $arreglo['NEWSCLASS'.$color], "UNIDAD" => $unidad,
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
    $texto = "No se encontraron ordenes";
	$Tabletemp->addTemplate( "rownewsvoid" );
    $Tabletemp->asigna_variables( array( "TEXTO_MENSAJE" => $texto ) );
    $Block .= $Tabletemp->compileandsend( );
}

$paging = $ESObject->paginarfiltro( $pag, $numreg, $regsize, $fecha1, $fecha2, $clienteid, $tallerid, $unidadid, $tallerservicioid, $folio);

$Contenido->asigna_variables( array( "CONTENT" => $Block, "PAGING" => $paging, "DIR" => $dir, "TODOS" => $numreg) );
$Contenido->compileandgo( );
?>