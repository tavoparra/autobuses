<?php
//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui
define('IN_EMADMIN', true);

//Agregamos las librerias comunes
include_once('config/securityI.inc.php');
include_once('config/varload.inc.php');
require_once('functions/templates.inc.php');
include_once('functions/functions.inc.php');
include("news/functions/functions.inc.php");


function getHref( $idTabla )
{
	$href = "";
	
	switch ( $idTabla )
	{
		case 1  : $href = ""; break;
		case 2  : $href = "catalogadmin/groupadmin.php?mode=4"; break;
		case 3  : $href = "catalogadmin/groupadmin.php"; break;
		case 4  : $href = "users/userAdmin.php?mode=3"; break;
		case 5  : $href = "seguim/seguimAdmin.php"; break;
		case 6  : $href = "users/userAdmin.php?mode=3"; break;
		case 7  : $href = "catalogadmin/corridaadmin.php?mode=2"; break;
		case 8  : $href = "discount/clientdisc.php"; break;
		case 9  : $href = ""; break;
		case 10 : $href = ""; break;
		case 11 : $href = "seguim/seguimAdmin.php"; break;
		case 12 : $href = "catalogadmin/groupadmin.php?mode=2"; break;
		case 13 : $href = "catalogadmin/groupadmin.php?mode=5"; break;
		case 14 : $href = "catalogadmin/groupadmin.php?mode=1"; break;
		case 15 : $href = "pop/popAdmin.php"; break;
		case 16 : $href = "catalogadmin/catalogadmin.php"; break;
		case 17 : $href = ""; break;
		case 18 : $href = "users/userAdmin.php"; break;
		case 19 : $href = "news/newsAdmin.php"; break;
		
		default : $href = ""; break;	
		
	}
	
	return $href;		

}

$Obj = new Noticias("");

//Si vamos a usar la base de datos inicializamos el objeto de conexion
$dir="";

//**         CABECERA Y MENU IZQ            **//
$Contenido=new template();
$Contenido->addTemplate("header");
$Contenido->asigna_variables(array ("PAGE_NAME" => $arreglo['INDXTITLE'],
									"BGCOLOR" => "#E3E1E2", "DIR" => $dir ) );
$Contenido->compileandgo();



//**             PAGINA PRINCIPAL             **//

$Contenido->addTemplate("indexbody");

if($_SESSION["level"]==1){
	$Menutemplate = new template( );
	$Menutemplate->addTemplate( "menu".$_SESSION["level"] );
	$Menutemplate->asigna_variables( array( "DIR"  => $dir ) );
	$menu = $Menutemplate->compileandsend( );
}
else
{
	//print_r($_SESSION);
	$menu = $Obj->buildmenu($dir);
}


// Plantilla Ventana de Actividades
// --------------------------------------------------------------------------------------

	$actTemplate = new template();
	$actTemplate->addTemplate( "headeract" );
	$act = $actTemplate->compileandsend();	
	
	// Recupera las Últimas 4 Actividades
	// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

		/*$numregs = $Obj->cuentareg( "logs" );
		
		if ( $numregs > 0 )		
		{		
			$rs = $Obj->selectqry( "logs, tablas, usuarios, tipologs", 
			                       DBPREFIX."logs.idtipolog, ".DBPREFIX."logs.idtabla, ".DBPREFIX."logs.idusuario, ".DBPREFIX."logs.fecha, ".DBPREFIX."tipologs.texto, ".DBPREFIX."tablas.descripcion, ".DBPREFIX."usuarios.username",
			                       DBPREFIX."logs.idtipolog = ".DBPREFIX."tipologs.idtipolog and ".DBPREFIX."logs.idtabla = ".DBPREFIX."tablas.idtabla and ".DBPREFIX."logs.idusuario = ".DBPREFIX."usuarios.userid order by fecha desc limit 0,4" );
				
			while( !$rs->EOF ) 
			{ 
				$idtabla = $rs->fields['idtabla']; 
				$href = getHref( $idtabla );	
				$texto = trim( $rs->fields['texto']." ".$rs->fields['descripcion'] );
				$actTemplate->addTemplate( "rowact" );
				$actTemplate->asigna_variables( array ( "HREF" => $href, "ACTTXT" => $texto ) );
				$act .= $actTemplate->compileandsend();
				$rs->MoveNext();			
			}	
		}
		else
		{*/
			$actTemplate->addTemplate( "rowactvoid" );			
			$act .= $actTemplate->compileandsend();
		//}		
		
	// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	
	$actTemplate->addTemplate( "bottomact" );
	$act .= $actTemplate->compileandsend();

	
// Plantilla Ventana de Noticias
// --------------------------------------------------------------------------------------

	$newsTemplate = new template();
	$newsTemplate->addTemplate( "headernoticia" );
	$news = $newsTemplate->compileandsend();

			$newsTemplate->addTemplate( "rownoticiavoid" );			

		
	// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

	$newsTemplate->addTemplate( "bottomnoticia" );
	$news .= $newsTemplate->compileandsend();
	
// ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


$Contenido->asigna_variables(array ( "MENU" => $menu, "SYSTEM" => $arreglo['SYSTEM'], "ACT" => $act, "NEWS" => $news  ) );
$Contenido->compileandgo();


//**             PIE DE PAGINA             **//

$Contenido->addTemplate("footer");
$Contenido->asigna_variables( array ( "DIR" => $dir, "MAP" => "" ) );
$Contenido->compileandgo();



?>
