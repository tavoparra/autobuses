<?php
//Definimos IN_EMADMIN, que nos permitir� llamar scripts desde aqui

define( 'IN_EMADMIN', true );

//Agregamos las librerias comunes

require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new tecnicos( '../' );
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


///  <<----- ESTE ES EL TAMA�O DE LA PAGINA EN REGISTROS
//**         CABECERA Y MENU IZQ            **//

$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => "Panel de Control - T&eacute;cnicos", "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "tecnicosadminbody" );

//Esta funci�n obtiene las noticias de la base de datos

$resultados_row = $ESObject->verTecnicos( $regsize, $pag );
$numreg = $ESObject->cuentaReg( "tecnicos", "1" );
$paging = $ESObject->paginar( $pag, $numreg, $regsize, "?pag=" );


//Creamos un nuevo objeto plantilla, este nos ayudar� a crear las tablas de forma
//din�mica

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

if(!isset($_SESSION['permisos']['7']['1']) && $_SESSION["level"]==2)
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

