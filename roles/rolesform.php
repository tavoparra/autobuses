<?php
session_start();

//Validamos que el usuario sea administrador
if($_SESSION["level"] != 1)
{
	echo "<script>location.href = '../login.php'; </script>";
}

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
if ( isset( $_GET['rolid'] ) )
{
    $rolid = $_GET['rolid'];
}
else
{
    $rolid = "";
}
$dir = "../";
$bloqueareas = "";

//Agregamos las librerias comunes

header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Content-Type: text/html; charset=utf-8" );

require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion

$ESObject = new Roles( '../' );

//**         CABECERA Y MENU IZQ            **//

$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => "Panel de Control - Roles", "BGCOLOR" => "#FFFFFF", "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate( "rolesformbody" );

//Inicializamos las variables


$Rolestemplate = new template( );

if ( $mode == "edit" )
{
    $Rolestemplate->addTemplate( "rolesformedit" );
}
else
{
    $Rolestemplate->addTemplate( "rolesformadd" );
}
if ( $mode == 'edit' )
{

    //Si esta en modo de edición, jala los datos de la BD para llenar automaticamente el formulario

    $Rolesdata  = $ESObject->inforoles( $rolid );
    $nombrerol  = $Rolesdata->fields['nombre'];
    

    $formaction = 'config/worknews.php?mode=edit&idNoticia='.$newsid;
    
	$Areastemplate = new template();

	$rs_areas = $ESObject->traerAreas();
	if(!$rs_areas->EOF)
	{
		$class = "oddn";
		do{
			
			
			$nombreArea = $rs_areas->fields["nombre"];
			$idarea = $rs_areas->fields["idarea"];
			$Areastemplate->addtemplate('rolesrow');
			$subareas = $ESObject->traersubareas($idarea, $rolid);			
			$Areastemplate->asigna_variables( array( "AREANAME" => $nombreArea, "CLASS" => $class, "SUBAREAS" => $subareas ) );
			$bloqueareas .= $Areastemplate->compileandsend( );
		
			if ( $class == "oddn" )
			{
				$class = "commonn";
			}
			else
			{
				$class = "oddn";
			}
			$rs_areas->MoveNext( );
		}while(!$rs_areas->EOF);
	}


    $Rolestemplate->asigna_variables( array( "ROLNAME"		=> $nombrerol, 
	     									"CONTENT" 		=> $content, 
											"AREAROWS" 		=> $bloqueareas, 
											"SIGNDATE" 		=> $newsdate, 
											"FORM_ACTION" 	=> $formaction, 
											"TITLE" 		=> $title, 
											"ROLID" 		=> $rolid, 
											"MODE" 			=> $mode, 
											"COMBOPORTADA"	=> $combo_portada ) );
    
}
else
{
    $formaction = 'config/workroles.php?mode=addRoles';
    $newsdate = 'Automática';
	
	$Areastemplate = new template();
	
	$rs_areas = $ESObject->traerAreas();
	if(!$rs_areas->EOF)
	{
		$class = "oddn";
		do{
			
			
			$nombreArea = $rs_areas->fields["nombre"];
			$idarea = $rs_areas->fields["idarea"];
			$Areastemplate->addtemplate('rolesrow');
			$subareas = $ESObject->traersubareas($idarea);			
			$Areastemplate->asigna_variables( array( "AREANAME" => $nombreArea, "CLASS" => $class, "SUBAREAS" => $subareas ) );
			$bloqueareas .= $Areastemplate->compileandsend( );
		
			if ( $class == "oddn" )
			{
				$class = "commonn";
			}
			else
			{
				$class = "oddn";
			}
			$rs_areas->MoveNext( );
		}while(!$rs_areas->EOF);
	}
	else
	{
		
	}
	
    $Rolestemplate->asigna_variables( array( "FORM_ACTION" => $formaction, "MODE" => $mode, "AREAROWS" => $bloqueareas ) );
}

$rolesform = $Rolestemplate->compileandsend( );
$Menutemplate = new template( );
$Menutemplate->addMTemplate( "menu".$_SESSION["level"] );
$Menutemplate->asigna_variables( array( "DIR" => $dir ) );
$menu = $Menutemplate->compileandsend( );
$Contenido->asigna_variables( array( "SYSTEM" => $arreglo['SYSTEM'], "MENU" => $menu, "CONTENT" => $rolesform ) );
$Contenido->compileandgo( );

//**             PIE DE PAGINA             **//

$Contenido->addMTemplate( "footer" );
$Contenido->asigna_variables( array( "DIR" => $dir, "MAP" => "" ) );
$Contenido->compileandgo( );
?>

