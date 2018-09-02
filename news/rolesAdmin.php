<?php
//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui

define( 'IN_EMADMIN', true );

//Agregamos las librerias comunes

require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new Roles( '../' );
$dir      = "../";
$Block    = "";

header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );
//Inicializamos la variable de paginacion


///  <<----- ESTE ES EL TAMAÑO DE LA PAGINA EN REGISTROS
//**         CABECERA Y MENU IZQ            **//

$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => $arreglo['NEWSTITLE'], "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "rolesadminbody" );

//Esta función obtiene las noticias de la base de datos

$resultados_row = $ESObject->verRoles();


//Creamos un nuevo objeto plantilla, este nos ayudará a crear las tablas de forma
//dinámica

$Tabletemp = new Template( );

//Primero agregamos la capacidad de ingresar nuevos registros
//La unica variable q contiene este template es hacia donde lo vamos a enviar

if ( !$resultados_row->EOF )
{
    do
    {
        $rolTitle = $resultados_row->fields["nombre"];
		$rolid = $resultados_row->fields["idrol"];
        $Tabletemp->addTemplate ( "rolestable" );
        $Tabletemp->asigna_variables( array( "NOMBREROL" => $rolTitle,  "ROLID" => $rolid) );
        $Block .= $Tabletemp->compileandsend( );

        $resultados_row->MoveNext( );
    }
    while ( !$resultados_row->EOF );
}
else
{
    //Si no encuentra ningun registro
    $texto = "No Existen Roles Registrados";
	$Tabletemp->addTemplate( "rowrolesvoid" );
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

