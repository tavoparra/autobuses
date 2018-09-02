<?php
session_start();

//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui
define('IN_EMADMIN', true);
$mode= isset($_GET['mode']) ? $_GET['mode'] : "";
$User= isset($_GET['idUsuario']) ? $_GET['idUsuario'] : "";
$data= isset($_GET['level']) ? $_GET['level'] : "";

$dir="../";
$Block="";
$adminsoport="";
$tipouser="";
//Agregamos las librerias comunes
require_once('common.inc.php');
//Si vamos a usar la base de datos inicializamos el objeto de conexion
$ESObject=new Usuarios('../');


//**         CABECERA Y MENU IZQ            **//
$Contenido=new template();
$Contenido->addMTemplate("header");
$Contenido->asigna_variables(array ("PAGE_NAME" => "Seccion de Usuarios", "BGCOLOR" => "#E3E1E2", "DIR" => $dir
							 ) );
$Contenido->compileandgo();

//**             PAGINA PRINCIPAL             **//
$Contenido->addTemplate("userFormbody");

$Tabletemp=new Template(); //Creamos un nuevo objeto plantilla, este nos ayudará a crear las tablas de forma dinámica

//Revisamos si el modo es checar o editar
if($mode=='check' || $mode=='edit'){
// Primero obtenemos las filas de usuario, esto será tanto para el modo de
// check como para el modo de edición. 
$UserRows=$ESObject->getUserdata($User);
                  		//Elige el tipo de Usuario
$tipouser=$ESObject->getLevelname($UserRows->fields[3]);
$backpage='userAdmin.php?mode='.$UserRows->fields[3];     //Elige la direccion del boton regresar
$profile=$ESObject->getProfile($UserRows->fields[3]);
$rol=$ESObject->getRol($UserRows->fields['rolid']);

  if($mode=="check"){
    $Tabletemp->addTemplate("userTablecheck");	
	}elseif($mode=="edit") {
		if($profile == 1)
			$Tabletemp->addTemplate("userTableedit");
		elseif($profile == 2)
		{
			$Tabletemp->addTemplate("userTableedit_user");	
			$permisos = $ESObject->buildpermisos($User);
		}
	$formAction='config/workuser.php?mode=edit&type=1&idUsuario='.$User;
	$tipouser=$ESObject->selectDinamico($_GET['level']);
	}	

 //Aqui debe de venir la seleccion del template a utilizar. Como los valores serán los
 //mismos, tendremos entonces que cambiar solo el template que estamos cargando
 $edituser="userForm.php?mode=edit&idUsuario=".$User."&level=".$data;
 $Tabletemp->asigna_variables(array ("USERNAME" => $UserRows->fields[9], "PERMISOS" => $permisos,
									"LASTNAME" => $UserRows->fields[10], "JOB" => $UserRows->fields[8],
									"EMAIL" => $UserRows->fields[14], "PHONENUM" => $UserRows->fields[11],
									"CELLPHONE" => $UserRows->fields[12], "NEXTEL" => $UserRows->fields[13],
									"LOGIN" => $UserRows->fields[0],
									"PROFILE" => $tipouser, "USERID" => $User, "ROL" => $rol,
									"PASSWORD" => $UserRows->fields[1],		"LEVEL" => $data,																
									"EDITUSER" => $edituser,
									"BACKPAGE" => $backpage, "USERID" => $_GET['idUsuario']
							) );	

$Block.=$Tabletemp->compileandsend(); 
}
elseif($mode=="add"){    //Si no es modo check o modo edit entonces verifica que sea modo Add
$Tabletemp=new Template(); 
 
if($_GET['type']==1)
{
	$backpage="userAdmin.php?mode=1";
	$Tabletemp->addTemplate("userTableadd");
	$tipouser=$ESObject->selectDinamico($_GET['level']);
	$formAction='config/workuser.php?mode=add&type=1';
}
elseif($_GET['type']==2)
{
	$backpage="userAdmin.php?mode=2";
	$Tabletemp->addTemplate("userTableadd_user");
	$tipouser=$ESObject->selectDinamico($_GET['level']);
	$formAction='config/workuser.php?mode=add&type=1';
}

$Tabletemp->asigna_variables(array ("BACKPAGE" => $backpage,"PROFILE" => $tipouser,
									"SUPPADMIN" => $adminsoport,"TYPE" => $_GET['type'],
									"LEVEL" => $_GET['level'],"TYPENAME" => $typename
							) );
$Block.=$Tabletemp->compileandsend();
$msginfo=$arreglo['USERINFO6'];
}else{

$Tabletemp=new Template();  
$formAction='config/workuser.php?mode=add&type=2';
$backpage="userAdmin.php?mode=3";
$backpage='userAdmin.php'; 
$Tabletemp->addTemplate("userTableaddcs");
$Adminsup=$ESObject->showAdminname($_SESSION["idUsuario"]);
$tipouser=$ESObject->selectDinamico(0);
$idcontact=$ESObject->getAdmincontact($_SESSION["idUsuario"]);
$roles=$ESObject->llenarRoles();
$Tabletemp->asigna_variables(array ("BACKPAGE" => $backpage,"PROFILE" => $tipouser, "ROLES" => $roles,
									"SUPPADMIN" => $Adminsup,"SELECTEDC" => $idcontact
									
							) );
$Block.=$Tabletemp->compileandsend();
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

$Contenido->asigna_variables(array ("CONTENT" => $Block, "SYSTEM" => $arreglo['SYSTEM'],
									"BACKPAGE" => $backpage,
									"MENU" => $menu
							) );
							
$Contenido->compileandgo();



//**             PIE DE PAGINA             **//

$Contenido->addMTemplate("footer");
$Contenido->asigna_variables(array ("DIR" => $dir,"MAP" => "") );
$Contenido->compileandgo();

//Al final se ha generado dinamicamente toda la pagina

?>
