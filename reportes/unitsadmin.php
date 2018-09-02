<?php
//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui

define( 'IN_EMADMIN', true );

//Agregamos las librerias comunes

require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new units( '../' );
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
    $pag = intval($_GET['pag']);//echo $pag;die();
}
$regsize = 10;

///  <<----- ESTE ES EL TAMAÑO DE LA PAGINA EN REGISTROS
//**         CABECERA Y MENU IZQ            **//

$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => $arreglo['NEWSTITLE'], "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "unitsadminbody" );

//Esta función obtiene las unidades  de la base de datos
$resultados_row = $ESObject->seeUnits();

// A continuación se manda a llamar los métodos para paginar los resultados de la Base de Datos
$numreg = $ESObject->cuentaReg( "units", "1" );
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
        $unidadID = $resultados_row->fields["unidadID"];
		$numEco = $resultados_row->fields["numEconomico"];
		$placas = $resultados_row->fields["placas"];
        $Tabletemp->addTemplate ( "unitstable" );
        $Tabletemp->asigna_variables( 
										array( 
												"UNIDADID" 	=> $unidadID,
												"NUMECO"	=> $numEco,
												"PLACAS"	=> $placas, 
												"ROWCOLOR" => $arreglo['ROWCOLOR'.$color]
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
    $texto = "No Existen Unidades Registradas";
	$Tabletemp->addTemplate( "unitsRowVoid" );
    $Tabletemp->asigna_variables( array( "TEXTO_MENSAJE" => $texto ) );
    $Block .= $Tabletemp->compileandsend( );
}

$Menutemplate = new template( );
$Menutemplate->addMTemplate( "menu".$_SESSION["level"] );
$Menutemplate->asigna_variables( array( "DIR"  => $dir ) );
$menu = $Menutemplate->compileandsend( );
$Contenido->asigna_variables( array( "CONTENT" => $Block, "SYSTEM" => $arreglo['SYSTEM'], "MENU" => $menu, "PAGING" => $paging ) );
$Contenido->compileandgo( );

//**             PIE DE PAGINA             **//

$Contenido->addMTemplate( "footer" );
$Contenido->asigna_variables( array( "DIR" => $dir, "MAP" => "" ) );
$Contenido->compileandgo( );
?>