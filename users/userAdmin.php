<?php
session_start();
//Definimos IN_EMADMIN, que nos permitirá llamar scripts desde aqui
define( 'IN_EMADMIN', true );
$mode = isset($_GET['mode']) ? $_GET['mode'] : "";
$pag = isset($_GET['pag']) ? $_GET['pag'] : "1";

$regsize = 20;

//Agregamos las librerias comunes
require_once( 'common.inc.php' );

//Si vamos a usar la base de datos inicializamos el objeto de conexion
$ESObject   = new Usuarios( '../' );
$dir        = "../";
$Block      = "";
$adminlevel = "";

//**         CABECERA Y MENU IZQ            **//
$Contenido = new template( );
$Contenido->addMTemplate( "header" );
$Contenido->asigna_variables( array( "PAGE_NAME" => $arreglo['USERTITLE'], "DIR" => $dir ) );
$Contenido->compileandgo( );

//**             PAGINA PRINCIPAL             **//
$Contenido->addTemplate( "userAdminbody" );

//SI EL USUARIO ES VENDEDOR SE ESTABLECE EL MODO PARA QUE CARGUE USUARIOS
    if ( $mode == "" ){
        $backpage = "../index.php";
        $Tabletemp = new Template( );
        $Tabletemp->addTemplate( "userSelect" );
        $adminlevel = "&nbsp;&nbsp;<a href='userLevels.php'><img src='imagenes/btnAdminNiveles.jpg' border='0'></a>";
        $groups = $ESObject->getLevelselect( 0 );
        $Tabletemp->asigna_variables( array( "GRUPOS" => $groups, "BACKPAGE" => $backpage ) );
        $Block .= $Tabletemp->compileandsend( );
        $msginfo = $arreglo['USERINFO'];
    }
    else{
        $backpage = "userAdmin.php";

        // Primero obtenemos las filas de usuario (Siempre existirá al menos el usuario administrador)
        // En el caso contrario (Clientes y Admin de Soporte) si no hay resultados entonces se debe
        // Mostrar un mensaje de No Hay Usuarios agregados actualmente

        $resultados_row = $ESObject->getUsers( $mode, $regsize, $pag );
        $numreg = $ESObject->countUsersreg( $mode );
        $profile = $ESObject->getProfile( $mode );

        //Creamos un nuevo objeto plantilla, este nos ayudará a crear las tablas de forma
        //dinámica

        $Tabletemp = new Template( );

        //Primero agregamos la capacidad de ingresar nuevos registros

        $Tabletemp->addTemplate( "userDynamiccase" );

        //La unica variable q contiene este template es hacia donde lo vamos a enviar

        $Tabletemp->asigna_variables( array( "BACKPAGE" => $backpage, "TYPE" => $profile, "LEVEL" => $mode, "PAGING" => $paging ) );
        
        
        $Block .= $Tabletemp->compileandsend( );
        if ( !$resultados_row->EOF )
        {

            //Si existen resultados, entonces generamos la tabla dinamica a partir de una plantilla
            //de la tabla

            $color = 2;
            do
            {
                if ( $profile == 3 )
                {
                    $username = $resultados_row->fields[9];
                    $userid = $resultados_row->fields[4];
                }
                else
                {
                    $username = $resultados_row->fields[9];
                    $userid = $resultados_row->fields[4];
                }
                $levelname = $ESObject->getUserlevelname( $userid );
                $Tabletemp->addTemplate( "userTable" );

                //Cuando los datos son repetitivos solo cargaremos una y otra vez la
                //plantilla de los datos

                $Tabletemp->asigna_variables( array( "USERNAME" => $resultados_row->fields[0], "USERID" => $userid, "ROWCOLOR" => $arreglo['ROWCOLOR'.$color], "LEVEL" => $mode, "NOMBREUSER" => $username, "LEVELNAME" => $levelname ) );
                $Block .= $Tabletemp->compileandsend( );
                if ( $color == 2 )
                {
                    $color = 1;
                }
                else
                {
                    $color = 2;
                }
                $resultados_row->MoveNext( );
            }
            while ( !$resultados_row->EOF );
        }
        else
        {

            //Si no encuentra ningun registro envia un mensaje

            $msginfo = $arreglo['USERINFO2'];
            $Tabletemp->addMTemplate( "tablemessage" );
            $Tabletemp->asigna_variables( array( "MESSAGE" => $arreglo['NOUSER'], "ROWCOLOR" => $arreglo['ROWCOLOR2'] ) );
            $Block .= $Tabletemp->compileandsend( );
        }
        
        $profile = $ESObject->getProfile( $mode );
        $addpage = 'userForm.php?mode=add&type='.$profile.'&level='.$mode;
        if ( $mode == 4 )
        {
            $Tabletemp->addTemplate( "userDynamiccased" );
        }
        else
        {
            $Tabletemp->addTemplate( "userDynamiccaseb" );
        }
        
        $paging = $ESObject->paginar( $pag, $numreg, $regsize, "userAdmin.php?mode=".$mode."&pag=" );
        $Tabletemp->asigna_variables( array( "TYPE" => $profile, "LEVEL" => $mode, "PAGING" => $paging ) );
        $Block .= $Tabletemp->compileandsend( );
        $levelname = $ESObject->getLevelname( $mode );
        $msginfo = $arreglo['USERINFO1'].$levelname;
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
$Contenido->asigna_variables( array( "CONTENT" => $Block, "SYSTEM" => $arreglo['SYSTEM'], "USERINFO" => $msginfo, "BACKPAGE" => $backpage, "ADMINLEVEL" => $adminlevel, "MENU" => $menu ) );
$Contenido->compileandgo( );

//**             PIE DE PAGINA             **//

$Contenido->addMTemplate( "footer" );
$Contenido->asigna_variables( array( "DIR" => $dir, "MAP" => "" ) );
$Contenido->compileandgo( );

//Al final se ha generado dinamicamente toda la pagina

?>

